<?php

namespace App\Filament\Admin\Resources\Customers\Schemas;

use App\Models\Customer;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Perusahaan')->columns(2)->schema([
                TextInput::make('kode_customer')->label('Kode Customer')->required()->unique(ignoreRecord: true)->default(fn (): string => \App\Filament\Admin\Resources\Customers\Pages\CreateCustomer::nextCode())->readOnly(fn (?Customer $record): bool => $record === null),
                TextInput::make('nama_perusahaan')->label('Nama Perusahaan')->required(),
                Repeater::make('customer_addresses_form')->label('Alamat Perusahaan')->dehydrated(false)->default(fn (?Customer $record): array => self::addresses($record?->alamat))->defaultItems(1)->columns(1)->schema([
                    Textarea::make('alamat_lengkap')->label('Alamat')->rows(2)->required(),
                ])->columnSpanFull(),
                Repeater::make('warehouse_addresses_form')->label('Alamat Gudang')->dehydrated(false)->default(fn (?Customer $record): array => self::addresses($record?->alamat_gudang))->defaultItems(1)->columns(1)->schema([
                    Textarea::make('alamat_lengkap')->label('Alamat Gudang')->rows(2)->required(),
                ])->columnSpanFull(),
                Hidden::make('alamat'),
                Hidden::make('alamat_gudang'),
                TextInput::make('kota')->label('Kota'),
                TextInput::make('telepon')->label('Telepon')->tel(),
                TextInput::make('email')->label('Email')->email(),
                TextInput::make('npwp')->label('NPWP'),
            ]),
            Section::make('PIC Customer')->columns(2)->schema([
                TextInput::make('pic')->label('PIC Utama'),
                TextInput::make('pic_phone')->label('Telepon PIC')->tel(),
                TextInput::make('pic_email')->label('Email PIC')->email(),
                Repeater::make('additional_customer_pics')->label('PIC Customer Lain')->dehydrated(false)->default(fn (?Customer $record): array => $record?->customer_pics()->where('tipe', 'Customer')->where('nama', '!=', $record->pic)->get()->map(fn ($pic): array => ['nama' => $pic->nama, 'telepon' => $pic->telepon, 'email' => $pic->email])->all() ?? [])->schema([
                    TextInput::make('nama')->label('Nama')->required(), TextInput::make('telepon')->label('Telepon')->tel(), TextInput::make('email')->label('Email')->email(),
                ])->columns(3)->columnSpanFull(),
            ]),
            Section::make('PIC Gudang')->columns(2)->schema([
                TextInput::make('pic_gudang')->label('PIC Gudang Utama'),
                TextInput::make('pic_gudang_phone')->label('Telepon PIC Gudang')->tel(),
                Repeater::make('additional_warehouse_pics')->label('PIC Gudang Lain')->dehydrated(false)->default(fn (?Customer $record): array => $record?->customer_pics()->where('tipe', 'Gudang')->where('nama', '!=', $record->pic_gudang)->get()->map(fn ($pic): array => ['nama' => $pic->nama, 'telepon' => $pic->telepon])->all() ?? [])->schema([
                    TextInput::make('nama')->label('Nama')->required(), TextInput::make('telepon')->label('Telepon')->tel(),
                ])->columns(2)->columnSpanFull(),
            ]),
        ]);
    }

    private static function addresses(?string $value): array
    {
        $items = array_values(array_filter(array_map('trim', preg_split('/\n\s*\n/', $value ?? '') ?: [])));
        return $items ? array_map(fn (string $address): array => ['alamat_lengkap' => $address], $items) : [['alamat_lengkap' => '']];
    }
}
