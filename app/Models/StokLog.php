<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StokLog
 * 
 * @property int $id
 * @property int $barang_id
 * @property string $jenis
 * @property float $qty
 * @property float $stok_sebelum
 * @property float $stok_sesudah
 * @property string|null $referensi_tipe
 * @property int|null $referensi_id
 * @property string|null $keterangan
 * @property int $created_by
 * @property Carbon|null $created_at
 * 
 * @property Barang $barang
 * @property User $user
 *
 * @package App\Models
 */
class StokLog extends Model
{
	protected $table = 'stok_log';
	public $timestamps = false;

	protected $casts = [
		'barang_id' => 'int',
		'qty' => 'float',
		'stok_sebelum' => 'float',
		'stok_sesudah' => 'float',
		'referensi_id' => 'int',
		'created_by' => 'int'
	];

	protected $fillable = [
		'barang_id',
		'jenis',
		'qty',
		'stok_sebelum',
		'stok_sesudah',
		'referensi_tipe',
		'referensi_id',
		'keterangan',
		'created_by'
	];

	public function barang()
	{
		return $this->belongsTo(Barang::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'created_by');
	}
}
