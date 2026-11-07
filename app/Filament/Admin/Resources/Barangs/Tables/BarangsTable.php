<?php

namespace App\Filament\Admin\Resources\Barangs\Tables;

use App\Filament\Admin\Resources\Barangs\BarangResource;
use App\Filament\Admin\Resources\StokLogs\StokLogResource;
use App\Models\Barang;
use App\Models\KategoriBarang;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Collection;

class BarangsTable
{
    public static function configure(Table $table): Table
    {
        return $table->searchable()->searchPlaceholder('Cari nama, kode...')->defaultSort('nama_barang')
            ->columns([
                TextColumn::make('kode_barang')->label('Kode')->searchable()->sortable(),
                TextColumn::make('nama_barang')->label('Nama Barang')->searchable()->sortable()
                    ->url(fn (Barang $record): string => self::historyUrl($record)),
                TextColumn::make('kategori_barang.nama_kategori')->label('Kategori')->badge()->searchable()->sortable(),
                TextColumn::make('satuan')->label('Satuan'),
                TextColumn::make('tahun')->label('Tahun')->sortable(),
                TextColumn::make('harga_satuan')->label('Harga Satuan')->money('IDR')->sortable(),
            ])
            ->filters([
                SelectFilter::make('kategori_id')->label('Kategori')->relationship('kategori_barang', 'nama_kategori')->searchable()->preload(),
                SelectFilter::make('tahun')->label('Tahun')->options(fn (): array => Barang::query()->whereNotNull('tahun')->where('tahun', '!=', '')->distinct()->orderByDesc('tahun')->pluck('tahun', 'tahun')->all()),
            ])
            ->recordAction(null)
            ->recordActions([
                ActionGroup::make([
                    Action::make('history')->label('History Stok')->icon('heroicon-o-clock')->url(fn (Barang $record): string => self::historyUrl($record)),
                    EditAction::make()->icon('heroicon-o-pencil-square'),
                    DeleteAction::make()->icon('heroicon-o-trash'),
                ])->iconButton()->tooltip('Aksi')->visible(fn (): bool => auth()->user()?->hasAccess('barangs', 'update') ?? false),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('setCategory')->label('Pindah Kategori')->icon('heroicon-o-tag')
                        ->form([Select::make('kategori_id')->label('Kategori Baru')->options(fn (): array => KategoriBarang::query()->orderBy('nama_kategori')->pluck('nama_kategori', 'id')->all())->required()->searchable()->preload()])
                        ->visible(fn (): bool => auth()->user()?->hasAccess('barangs', 'update') ?? false)
                        ->action(fn (Collection $records, array $data) => $records->each->update(['kategori_id' => $data['kategori_id']])),
                    DeleteBulkAction::make()->authorizeIndividualRecords('delete')
                        ->visible(fn (): bool => auth()->user()?->hasAccess('barangs', 'delete') ?? false),
                ]),
            ]);
    }

    private static function historyUrl(Barang $record): string
    {
        return StokLogResource::getUrl('index', ['tableFilters' => ['barang_id' => ['value' => $record->id]]]);
    }
}
