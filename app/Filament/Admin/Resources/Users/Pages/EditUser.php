<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Models\UserAccessOverride;
use App\Filament\Admin\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected array $pendingPermissions = [];

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
        unset($data['permissions']);

        if ($this->record->is(auth()->user()) && ($data['role'] !== 'admin' || ! $data['aktif'])) {
            throw ValidationException::withMessages(['data.role' => 'Jangan ubah role atau nonaktifkan akun administrator yang sedang dipakai.']);
        }

        $this->pendingPermissions = $permissions;

        return $data;
    }

    protected function afterSave(): void
    {
        DB::transaction(function (): void {
            foreach (config('access.features') as $feature => $label) {
                foreach (config('access.actions') as $action) {
                    $value = $this->pendingPermissions[$feature][$action] ?? 'inherit';
                    $key = ['user_id' => $this->record->id, 'feature' => $feature, 'action' => $action];
                    if ($value === 'inherit') {
                        UserAccessOverride::query()->where($key)->delete();
                    } else {
                        UserAccessOverride::query()->updateOrCreate($key, ['allowed' => $value === 'allow']);
                    }
                }
            }
        });
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()->visible(fn (): bool => ! $this->record->is(auth()->user())),
        ];
    }
}
