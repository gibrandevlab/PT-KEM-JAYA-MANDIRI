<?php

namespace App\Filament\Admin\Resources\Vendors\Schemas;

use App\Filament\Admin\Resources\Vendors\Pages\CreateVendor;
use App\Models\Vendor;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VendorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Vendor')->columns(2)->schema([
                TextInput::make('kode_vendor')->label('Kode Vendor')->default(fn (): string => CreateVendor::nextCode())->readOnly(fn (?Vendor $record): bool => $record === null)->unique(ignoreRecord: true),
                TextInput::make('nama_perusahaan')->label('Nama Perusahaan')->required(),
                Textarea::make('alamat')->label('Alamat')->rows(3)->columnSpanFull(),
                TextInput::make('kota')->label('Kota'),
                TextInput::make('telepon')->label('Telepon')->tel(),
                TextInput::make('email')->label('Email')->email(),
                Hidden::make('pic'),
            ]),
            Section::make('PIC Vendor')->schema([
                Repeater::make('pic_list')->label('Daftar PIC')->dehydrated(false)->default(fn (?Vendor $record): array => self::parsePics($record?->pic))->defaultItems(1)->columns(2)->schema([
                    TextInput::make('nama')->label('Nama PIC')->required(),
                    TextInput::make('telepon')->label('Telepon PIC')->tel(),
                ]),
            ]),
        ]);
    }

    public static function combinePics(array $rows): ?string
    {
        $pics = [];
        foreach ($rows as $row) {
            $name = trim((string) ($row['nama'] ?? ''));
            if ($name === '') continue;
            $phone = trim((string) ($row['telepon'] ?? ''));
            $pics[] = $name . ($phone !== '' ? ' - ' . $phone : '');
        }
        return $pics ? implode("\n\n", $pics) : null;
    }

    private static function parsePics(?string $value): array
    {
        $rows = array_filter(array_map('trim', preg_split('/\n\s*\n/', $value ?? '') ?: []));
        return $rows ? array_map(function (string $pic): array {
            [$name, $phone] = array_pad(explode(' - ', $pic, 2), 2, '');
            return ['nama' => $name, 'telepon' => $phone];
        }, array_values($rows)) : [['nama' => '', 'telepon' => '']];
    }
}
