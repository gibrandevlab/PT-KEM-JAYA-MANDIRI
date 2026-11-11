<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\Penawarans\PenawaranResource;
use App\Models\Penawaran;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestPenawaransTable extends BaseWidget
{
    protected static ?int $sort = 4;

    protected static ?string $heading = 'Penawaran Terbaru';

    protected int | string | array $columnSpan = ['default' => 1, 'xl' => 2];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Penawaran::query()
                    ->with('user:id,nama')
                    ->latest('created_at')
                    ->limit(5)
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('no_penawaran')
                    ->label('No. Penawaran')
                    ->weight('bold')
                    ->color('primary')
                    ->searchable(),
                TextColumn::make('nama_customer')
                    ->label('Pelanggan')
                    ->limit(20)
                    ->searchable(),
                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR', locale: 'id'),
            ])
            ->recordActions([
                Action::make('detail')
                    ->label('Detail')
                    ->icon('heroicon-m-eye')
                    ->color('primary')
                    ->modalHeading(fn (Penawaran $record): string => "Detail Penawaran: {$record->no_penawaran}")
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn (Penawaran $record) => view('filament.pages.modals.penawaran-detail', [
                        'record' => $record->loadMissing(['penawaran_items', 'user:id,nama']),
                    ])),
            ])
            ->headerActions([
                Action::make('lihat_semua')
                    ->label('Lihat Semua')
                    ->url(PenawaranResource::getUrl())
                    ->icon('heroicon-m-arrow-right'),
            ]);
    }
}
