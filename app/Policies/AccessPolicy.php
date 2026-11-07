<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Penawaran;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AccessPolicy
{
    public function before(User $user): ?bool
    {
        if (! $user->aktif) {
            return false;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        $feature = $this->currentResourceFeature();

        return $feature !== null && $user->hasAccess($feature, 'view');
    }

    public function view(User $user, Model $model): bool
    {
        return $user->hasAccess($this->feature($model), 'view');
    }

    public function create(User $user): bool
    {
        $feature = $this->currentResourceFeature();

        return $feature !== null && $user->hasAccess($feature, 'create');
    }

    public function update(User $user, Model $model): bool
    {
        if ($model instanceof Penawaran && $user->role !== 'admin' && (int) $model->created_by !== (int) $user->getKey()) {
            return false;
        }

        return $user->hasAccess($this->feature($model), 'update');
    }

    public function delete(User $user, Model $model): bool
    {
        if ($model instanceof Penawaran && $user->role !== 'admin' && (int) $model->created_by !== (int) $user->getKey()) {
            return false;
        }

        return $user->hasAccess($this->feature($model), 'delete');
    }

    public function deleteAny(User $user, string|Model|null $model = null): bool
    {
        if ($model !== null) {
            return $user->hasAccess($this->feature($model), 'delete');
        }

        $feature = $this->currentResourceFeature();

        return $feature !== null && $user->hasAccess($feature, 'delete');
    }

    public function scope(User $user, \Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        if ($query->getModel() instanceof Penawaran && $user->role !== 'admin') {
            return $query->where('created_by', $user->getKey());
        }

        return $query;
    }

    private function feature(string|Model $model): string
    {
        $table = $model instanceof Model ? $model->getTable() : (new $model())->getTable();
        $aliases = [
            'invoice_items' => 'invoices',
            'invoice_attachments' => 'invoices',
            'penawaran_items' => 'penawarans',
            'penawaran_attachments' => 'penawarans',
            'po_items' => 'purchase_orders',
            'po_attachments' => 'purchase_orders',
            'surat_jalan_items' => 'surat_jalans',
        ];

        if (isset($aliases[$table])) {
            return $aliases[$table];
        }

        $feature = Str::plural(Str::snake($table));

        if (array_key_exists($feature, config('access.features', []))) {
            return $feature;
        }

        return $table;
    }

    private function currentResourceFeature(): ?string
    {
        $routeName = request()->route()?->getName();
        $refererPath = parse_url(request()->headers->get('referer', ''), PHP_URL_PATH) ?: '';

        foreach (Filament::getCurrentPanel()?->getResources() ?? [] as $resource) {
            $slug = preg_quote($resource::getSlug(), '#');
            if (($routeName && str_contains($routeName, '.resources.' . $resource::getSlug() . '.'))
                || preg_match('#/' . $slug . '(?:/|$)#', $refererPath)) {
                return $this->feature($resource::getModel());
            }
        }

        return null;
    }
}
