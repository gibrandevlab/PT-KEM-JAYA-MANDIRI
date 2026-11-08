<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

/**
 * Class User
 * 
 * @property int $id
 * @property string $nama
 * @property string $email
 * @property string $username
 * @property string $password
 * @property string $role
 * @property string|null $foto
 * @property bool $aktif
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Invoice[] $invoices
 * @property Collection|Penawaran[] $penawarans
 * @property Collection|PurchaseOrder[] $purchase_orders
 * @property Collection|StokLog[] $stok_logs
 * @property Collection|SuratJalan[] $surat_jalans
 *
 * @package App\Models
 */
class User extends Authenticatable implements FilamentUser, HasName, HasAvatar
{
	use Notifiable;

	protected $table = 'users';

	protected $casts = [
		'aktif' => 'bool'
	];

	protected $hidden = [
		'password',
		'remember_token',
	];

	protected $fillable = [
		'nama',
		'email',
		'username',
		'password',
		'role',
		'foto',
		'aktif',
		'remember_token',
	];

	public function canAccessPanel(Panel $panel): bool
	{
		return (bool) $this->aktif && in_array($this->role, ['admin', 'pekerja'], true);
	}

	public function hasAccess(string $feature, string $action = 'view'): bool
	{
		if (! array_key_exists($feature, config('access.features', [])) || ! in_array($action, config('access.actions', []), true)) {
			return false;
		}

		if (config('access.overrides_enabled', false)) {
			$override = UserAccessOverride::query()
				->where('user_id', $this->getKey())
				->where('feature', $feature)
				->where('action', $action)
				->value('allowed');

			if ($override !== null) {
				return (bool) $override;
			}
		}

		$defaults = config("access.defaults.{$this->role}", []);
		return (in_array($this->role, ['admin', 'pekerja'], true) && in_array('*', $defaults['*'] ?? [], true))
			|| in_array('*', $defaults[$feature] ?? [], true)
			|| in_array($action, $defaults[$feature] ?? [], true)
			|| in_array('*', $defaults['*']['*'] ?? [], true);
	}

	public function accessOverrides()
	{
		return $this->hasMany(UserAccessOverride::class);
	}

	public function getFilamentName(): string
	{
		return $this->nama ?? $this->username ?? 'User';
	}

	public function getFilamentAvatarUrl(): ?string
	{
		if (! filled($this->foto)) {
			return null;
		}

		if (filter_var($this->foto, FILTER_VALIDATE_URL)) {
			return $this->foto;
		}

		return Storage::disk('public')->exists($this->foto)
			? Storage::disk('public')->url($this->foto)
			: null;
	}

	public function getNameAttribute(): string
	{
		return $this->nama ?? $this->username ?? 'User';
	}

	public function invoices()
	{
		return $this->hasMany(Invoice::class, 'created_by');
	}

	public function penawarans()
	{
		return $this->hasMany(Penawaran::class, 'created_by');
	}

	public function purchase_orders()
	{
		return $this->hasMany(PurchaseOrder::class, 'created_by');
	}

	public function stok_logs()
	{
		return $this->hasMany(StokLog::class, 'created_by');
	}

	public function surat_jalans()
	{
		return $this->hasMany(SuratJalan::class, 'created_by');
	}
}
