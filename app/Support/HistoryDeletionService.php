<?php

namespace App\Support;

use App\Models\Invoice;
use App\Models\Penawaran;
use App\Models\PurchaseOrder;
use App\Models\SuratJalan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;

class HistoryDeletionService
{
    public static function deleteMany(iterable $records): array
    {
        $deleted = 0;
        $skipped = 0;
        foreach ($records as $record) {
            if (! self::canDelete($record) || ! self::canDeleteForUser($record)) { $skipped++; continue; }
            self::delete($record);
            $deleted++;
        }
        return [$deleted, $skipped];
    }

    public static function notify(int $deleted, int $skipped): void
    {
        $notification = Notification::make()->title("{$deleted} data dihapus" . ($skipped ? ", {$skipped} masih dipakai dokumen lain" : ''));
        $skipped ? $notification->warning() : $notification->success();
        $notification->send();
    }

    public static function canDelete(Model $record): bool
    {
        return match (true) {
            $record instanceof Invoice => true,
            $record instanceof SuratJalan => ! $record->invoices()->exists(),
            $record instanceof PurchaseOrder => ! $record->surat_jalans()->exists() && ! $record->invoices()->exists(),
            $record instanceof Penawaran => ! $record->purchase_orders()->exists() && ! $record->surat_jalans()->exists() && ! $record->invoices()->exists(),
            default => false,
        };
    }

    public static function canDeleteForUser(Model $record): bool
    {
        return (! self::isArchived($record) || auth()->user()?->role === 'admin')
            && (bool) auth()->user()?->can('delete', $record);
    }

    public static function delete(Model $record): void
    {
        abort_unless(self::canDeleteForUser($record), 403);
        abort_unless(self::canDelete($record), 409, 'Dokumen masih dipakai dokumen lain. Hapus dokumen turunannya terlebih dahulu.');

        $files = DB::transaction(function () use ($record): array {
            $files = [];
            if ($record instanceof Invoice) {
                $files = self::deleteAttachments($record->invoice_attachments);
                $record->invoice_items()->delete();
            } elseif ($record instanceof SuratJalan) {
                $record->surat_jalan_items()->delete();
            } elseif ($record instanceof PurchaseOrder) {
                $files = self::deleteAttachments($record->po_attachments);
                $record->po_items()->delete();
            } elseif ($record instanceof Penawaran) {
                $files = self::deleteAttachments($record->penawaran_attachments);
                $record->penawaran_items()->delete();
            }

            $record->delete();
            return $files;
        });
        foreach ($files as $file) Storage::disk('public')->delete($file);
    }

    private static function isArchived(Model $record): bool
    {
        return (bool) ($record->is_history ?? false) || match (true) {
            $record instanceof PurchaseOrder, $record instanceof Penawaran => $record->status === 'selesai',
            $record instanceof SuratJalan => $record->status === 'diterima',
            $record instanceof Invoice => $record->status_payment === 'Paid',
            default => false,
        };
    }

    private static function deleteAttachments(iterable $attachments): array
    {
        $files = [];
        foreach ($attachments as $attachment) {
            if (filled($attachment->file_path)) $files[] = $attachment->file_path;
            $attachment->delete();
        }
        return $files;
    }
}
