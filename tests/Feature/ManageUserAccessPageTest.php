<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserAccessOverride;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class ManageUserAccessPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Skema `users` produksi (db_kem) beda dari migration stub Laravel default
     * (kolom `name` vs `nama`, plus `username`, `role`, `foto`, `aktif`).
     * Test memakai DB sqlite in-memory, jadi kolom yang hilang ditambahkan di sini.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Schema::table('users', function ($table): void {
            $table->string('nama')->nullable();
            $table->string('username')->nullable();
            $table->string('role')->default('pekerja');
            $table->string('foto')->nullable();
            $table->boolean('aktif')->default(true);
            $table->dropColumn('name');
        });
    }

    private function admin(): User
    {
        return User::query()->create([
            'nama' => 'Admin Uji',
            'email' => 'admin@example.test',
            'username' => 'adminuji',
            'password' => bcrypt('secret'),
            'role' => 'admin',
            'aktif' => true,
        ]);
    }

    private function pekerja(): User
    {
        return User::query()->create([
            'nama' => 'Pekerja Uji',
            'email' => 'pekerja@example.test',
            'username' => 'pekerjauji',
            'password' => bcrypt('secret'),
            'role' => 'pekerja',
            'aktif' => true,
        ]);
    }

    public function test_page_renders_for_admin(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/users/'.$this->pekerja()->id.'/access')
            ->assertSuccessful()
            ->assertSee('Atur Hak Akses');
    }

    public function test_page_renders_when_form_schema_is_built(): void
    {
        $target = $this->pekerja();

        Livewire::actingAs($this->admin())
            ->test(\App\Filament\Admin\Resources\Users\Pages\ManageUserAccess::class, ['record' => $target->getRouteKey()])
            ->assertSuccessful()
            ->assertFormExists();
    }

    public function test_saving_permissions_stores_overrides(): void
    {
        $target = $this->pekerja();

        Livewire::actingAs($this->admin())
            ->test(\App\Filament\Admin\Resources\Users\Pages\ManageUserAccess::class, ['record' => $target->getRouteKey()])
            ->fillForm([
                'permissions' => [
                    'barangs' => ['view' => 'deny', 'create' => 'allow', 'update' => 'inherit', 'delete' => 'inherit'],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('user_access_overrides', [
            'user_id' => $target->id,
            'feature' => 'barangs',
            'action' => 'view',
            'allowed' => false,
        ]);
        $this->assertDatabaseHas('user_access_overrides', [
            'user_id' => $target->id,
            'feature' => 'barangs',
            'action' => 'create',
            'allowed' => true,
        ]);
        $this->assertDatabaseMissing('user_access_overrides', [
            'user_id' => $target->id,
            'feature' => 'barangs',
            'action' => 'update',
        ]);

        // Data profil user tidak boleh ikut berubah dari halaman akses.
        $this->assertSame('Pekerja Uji', $target->fresh()->nama);
        $this->assertSame('pekerja', $target->fresh()->role);
    }

    public function test_permission_sections_are_collapsed_by_default(): void
    {
        $target = $this->pekerja();

        $page = Livewire::actingAs($this->admin())
            ->test(\App\Filament\Admin\Resources\Users\Pages\ManageUserAccess::class, ['record' => $target->getRouteKey()])
            ->assertSuccessful();

        $sections = $page->instance()->getSchema('form')->getComponents();

        $collapsed = collect($sections)
            ->filter(fn ($component): bool => $component instanceof \Filament\Schemas\Components\Section
                && $component->isCollapsible());

        $this->assertNotEmpty($collapsed, 'Grup hak akses harus ada.');
        $this->assertTrue(
            $collapsed->every(fn ($section): bool => $section->isCollapsed()),
            'Semua grup hak akses harus tertutup secara default.',
        );
    }

    public function test_page_has_no_verbose_help_text(): void
    {
        $target = $this->pekerja();

        $this->actingAs($this->admin())
            ->get('/admin/users/' . $target->id . '/access')
            ->assertSuccessful()
            ->assertDontSee('Pilih Default role, Izinkan, atau Tolak untuk tiap aksi.')
            ->assertDontSee('Default = ikut role. Izinkan = selalu diberi akses. Tolak = selalu diblokir.')
            ->assertDontSee('Akses History mengikuti model transaksi terkait.');
    }

    public function test_user_list_and_edit_pages_render(): void
    {
        $admin = $this->admin();
        $target = $this->pekerja();

        $this->actingAs($admin)
            ->get('/admin/users')
            ->assertSuccessful();

        $this->actingAs($admin)
            ->get('/admin/users/' . $target->id . '/edit')
            ->assertSuccessful()
            ->assertDontSee('Identitas dan kredensial akun.')
            ->assertDontSee('Opsional, tautan gambar profil.');

        $this->actingAs($admin)
            ->get('/admin/users/' . $target->id)
            ->assertSuccessful();
    }

    public function test_existing_overrides_are_filled_as_state(): void
    {
        $target = $this->pekerja();
        UserAccessOverride::query()->create([
            'user_id' => $target->id,
            'feature' => 'invoices',
            'action' => 'delete',
            'allowed' => true,
        ]);

        Livewire::actingAs($this->admin())
            ->test(\App\Filament\Admin\Resources\Users\Pages\ManageUserAccess::class, ['record' => $target->getRouteKey()])
            ->assertFormSet([
                'permissions.invoices.delete' => 'allow',
                'permissions.invoices.view' => 'inherit',
            ]);
    }
}
