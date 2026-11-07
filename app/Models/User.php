<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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
class User extends Model
{
	protected $table = 'users';

	protected $casts = [
		'aktif' => 'bool'
	];

	protected $hidden = [
		'password'
	];

	protected $fillable = [
		'nama',
		'email',
		'username',
		'password',
		'role',
		'foto',
		'aktif'
	];

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
