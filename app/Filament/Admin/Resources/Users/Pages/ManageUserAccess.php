<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Filament\Admin\Resources\Users\Schemas\UserForm;
use App\Filament\Admin\Resources\Users\UserResource;
use App\Models\UserAccessOverride;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class ManageUserAccess extends EditRecord
{
    protected static string $resource = UserResource::class;
    protected static ?string $title = 'Atur Hak Akses';

    public function form(Schema $schema): Schema
    {
        return UserForm::accessOnly($schema);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $overrides = $this->record->accessOverrides()->get()->keyBy(fn ($row): string => "{$row->feature}.{$row->action}");
        foreach (config('access.features') as $feature => $label) {
            foreach (config('access.actions') as $action) {
                $override = $overrides->get("{$feature}.{$action}");
                $data['permissions'][$feature][$action] = $override
                    ? ($override->allowed ? 'allow' : 'deny')
                    : 'inherit';
            }
        }
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions'], $data['legend']);

        // Halaman ini hanya mengubah hak akses, bukan data profil user.
        $this->pendingPermissions = $permissions;

        return [];
    }

    protected array $pendingPermissions = [];

    public function getSubheading(): ?string
    {
        $user = $this->record;

        return $user->nama . ' · ' . ($user->role === 'admin' ? 'Admin / Finance' : 'Pekerja');
    }

    protected function afterSave(): void
    {
        DB::transaction(function (): void {
            foreach (config('access.features') as $feature => $label) foreach (config('access.actions') as $action) {
                $key = ['user_id' => $this->record->id, 'feature' => $feature, 'action' => $action];
                $value = $this->pendingPermissions[$feature][$action] ?? 'inherit';
                if (! in_array($value, ['inherit', 'allow', 'deny'], true)) {
                    $value = 'inherit';
                }
                $value === 'inherit'
                    ? UserAccessOverride::query()->where($key)->delete()
                    : UserAccessOverride::query()->updateOrCreate($key, ['allowed' => $value === 'allow']);
            }
        });
    }

    protected function getHeaderActions(): array
    {
        return [Action::make('back')->label('Kembali ke Pengguna')->icon('heroicon-o-arrow-left')->url(UserResource::getUrl())];
    }
}
