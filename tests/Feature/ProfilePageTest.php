<?php

namespace Tests\Feature;

use App\Filament\Admin\Pages\ProfilePage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProfilePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_save_a_profile_photo_to_the_public_disk(): void
    {
        Storage::fake('public');
        $user = $this->user();

        Livewire::actingAs($user)
            ->test(ProfilePage::class)
            ->set('profilePhotoUpload', UploadedFile::fake()->createWithContent(
                'profile.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/Jx8AAAAASUVORK5CYII='),
            ))
            ->call('save')
            ->assertHasNoErrors();

        $photoPath = $user->fresh()->foto;

        $this->assertIsString($photoPath);
        $this->assertStringStartsWith('profiles/', $photoPath);
        Storage::disk('public')->assertExists($photoPath);
        $this->assertNotNull($user->fresh()->getFilamentAvatarUrl());
    }

    public function test_invalid_upload_does_not_replace_existing_photo(): void
    {
        Storage::fake('public');
        $user = $this->user();
        Storage::disk('public')->put('profiles/current.png', 'current');
        $user->update(['foto' => 'profiles/current.png']);

        Livewire::actingAs($user)
            ->test(ProfilePage::class)
            ->set('profilePhotoUpload', UploadedFile::fake()->create('invalid.txt', 10, 'text/plain'))
            ->call('save')
            ->assertHasErrors('profilePhotoUpload');

        $this->assertSame('profiles/current.png', $user->fresh()->foto);
        Storage::disk('public')->assertExists('profiles/current.png');
    }

    public function test_user_can_reset_profile_photo_to_name_initials(): void
    {
        Storage::fake('public');
        $user = $this->user();
        Storage::disk('public')->put('profiles/current.png', 'current');
        $user->update(['foto' => 'profiles/current.png']);

        Livewire::actingAs($user)
            ->test(ProfilePage::class)
            ->call('resetPhoto')
            ->assertHasNoErrors();

        $this->assertNull($user->fresh()->foto);
        Storage::disk('public')->assertMissing('profiles/current.png');
    }

    private function user(): User
    {
        return User::query()->create([
            'nama' => 'Pengguna Uji',
            'email' => 'profile@example.test',
            'username' => 'profileuji',
            'password' => bcrypt('secret'),
            'role' => 'pekerja',
            'aktif' => true,
        ]);
    }
}
