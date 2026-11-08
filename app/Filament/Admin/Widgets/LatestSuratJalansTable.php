<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\SuratJalans\SuratJalanResource;
use App\Models\SuratJalan;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestSuratJalansTable extends BaseWidget
{
    protected static ?int $sort = 5;

    protected static ?string $heading = 'Surat Jalan Terbaru';

    protected int | string | array $columnSpan = ['default' => 1, 'xl' => 2];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SuratJalan::query()
                    ->with('user:id,nama')
                    ->latest('created_at')
                    ->limit(5)
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('no_sj')
                    ->label('No. SJ')
                    ->weight('bold')
                    ->color('success')
                    ->searchable(),
                TextColumn::make('nama_penerima')
                    ->label('Penerima')
                    ->limit(20)
                    ->searchable(),
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d/m/Y'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'draft' => 'gray',
                        'dikirim' => 'warning',
                        'diterima' => 'success',
                        default => 'info',
                    }),
            ])
            ->recordActions([
                Action::make('detail')
                    ->label('Detail')
                    ->icon('heroicon-m-eye')
                    ->color('success')
                    ->modalHeading(fn (SuratJalan $record): string => "Detail Surat Jalan: {$record->no_sj}")
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn (SuratJalan $record) => view('filament.pages.modals.surat-jalan-detail', [
                        'record' => $record->loadMissing(['surat_jalan_items', 'user:id,nama']),
                    ])),
            ])
            ->headerActions([
                Action::make('lihat_semua')
                    ->label('Lihat Semua')
                    ->url(SuratJalanResource::getUrl())
                    ->icon('heroicon-m-arrow-right'),
            ]);
    }
}
