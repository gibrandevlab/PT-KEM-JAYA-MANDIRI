<?php

namespace App\Filament\Admin\Pages;

use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Storage;

class ProfilePage extends Page
{
    protected string $view = 'filament.admin.pages.profile';

    protected static ?string $slug = 'profile';

    protected static ?string $title = 'Profil Saya';

    protected static bool $shouldRegisterNavigation = false;

    protected Width | string | null $maxContentWidth = Width::Full;

    /** @var array<string, mixed> | null */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user() instanceof User;
    }

    public function mount(): void
    {
        $user = auth()->user();
        abort_unless($user instanceof User, 403);

        $photoPath = $user->foto;
        if (! is_string($photoPath) || ! Storage::disk('public')->exists($photoPath)) {
            $photoPath = null;
        }

        $this->form->fill(['foto' => $photoPath]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Foto Profil')
                    ->description('Pilih foto profil. Format JPG, PNG, atau WEBP dengan ukuran maksimal 1 MB.')
                    ->schema([
                        FileUpload::make('foto')
                            ->hiddenLabel()
                            ->image()
                            ->disk('public')
                            ->directory('profiles')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(1024)
                            ->helperText('Maksimal 1 MB.'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $user = auth()->user();
        abort_unless($user instanceof User, 403);

        $data = $this->form->getState();
        $oldPhoto = $user->foto;
        $newPhoto = $data['foto'] ?? null;

        if ($newPhoto === null && (! is_string($oldPhoto) || ! str_starts_with($oldPhoto, 'profiles/'))) {
            $newPhoto = $oldPhoto;
        }

        $user->update(['foto' => $newPhoto]);

        if (is_string($oldPhoto) && $oldPhoto !== $newPhoto && str_starts_with($oldPhoto, 'profiles/')) {
            Storage::disk('public')->delete($oldPhoto);
        }

        $this->form->fill(['foto' => $newPhoto]);

        Notification::make()
            ->success()
            ->title('Profil berhasil diperbarui')
            ->send();
    }

    protected function getViewData(): array
    {
        return ['user' => auth()->user()];
    }
}
