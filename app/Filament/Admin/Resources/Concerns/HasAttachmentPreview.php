<?php

namespace App\Filament\Admin\Resources\Concerns;

use Filament\Actions\Action;

trait HasAttachmentPreview
{
    protected static function attachmentPreviewAction(): Action
    {
        return Action::make('preview')
            ->label('Pratinjau')
            ->icon('heroicon-o-eye')
            ->modalHeading(fn ($record): string => 'Pratinjau: ' . $record->file_name)
            ->modalContent(fn ($record) => view('filament.admin.attachments.preview', [
                'attachment' => $record,
            ]))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup');
    }
}
