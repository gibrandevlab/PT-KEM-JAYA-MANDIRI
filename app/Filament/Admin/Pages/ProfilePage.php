<?php

namespace App\Filament\Admin\Pages;

use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Livewire\WithFileUploads;

class ProfilePage extends Page
{
    use WithFileUploads;

    protected string $view = 'filament.admin.pages.profile';

    protected static ?string $slug = 'profile';

    protected static ?string $title = 'Profil Saya';

    protected static bool $shouldRegisterNavigation = false;

    protected Width | string | null $maxContentWidth = Width::Full;

    /** @var array<string, mixed> | null */
    public ?array $data = [];

    public ?UploadedFile $profilePhotoUpload = null;

    public static function canAccess(): bool
    {
        return auth()->user() instanceof User;
    }

    public function mount(): void
    {
        $user = auth()->user();
        abort_unless($user instanceof User, 403);

        $this->form->fill([]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([]);
    }

    public function save(): void
    {
        $user = auth()->user();
        abort_unless($user instanceof User, 403);

        $oldPhoto = $user->foto;
        $newPhoto = $oldPhoto;

        if ($this->profilePhotoUpload instanceof UploadedFile) {
            $this->validate([
                'profilePhotoUpload' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
            ]);

            $newPhoto = $this->profilePhotoUpload->storePublicly('profiles', 'public');

            if (! is_string($newPhoto) || ! Storage::disk('public')->exists($newPhoto)) {
                Notification::make()->danger()->title('Foto gagal disimpan')->body('Silakan coba unggah kembali.')->send();

                return;
            }

            $user->update(['foto' => $newPhoto]);

            if (is_string($oldPhoto) && $oldPhoto !== $newPhoto && str_starts_with($oldPhoto, 'profiles/')) {
                Storage::disk('public')->delete($oldPhoto);
            }
        }

        $this->form->fill([]);
        $this->profilePhotoUpload = null;

        Notification::make()
            ->success()
            ->title('Profil berhasil diperbarui')
            ->send();
    }

    public function resetPhoto(): void
    {
        $user = auth()->user();
        abort_unless($user instanceof User, 403);

        $oldPhoto = $user->foto;
        $user->update(['foto' => null]);
        $this->profilePhotoUpload = null;

        if (is_string($oldPhoto) && str_starts_with($oldPhoto, 'profiles/')) {
            Storage::disk('public')->delete($oldPhoto);
        }

        $this->form->fill([]);

        Notification::make()
            ->success()
            ->title('Foto profil direset')
            ->send();
    }

    protected function getViewData(): array
    {
        return ['user' => auth()->user()];
    }
}
