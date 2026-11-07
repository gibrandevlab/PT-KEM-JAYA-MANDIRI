<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StockGudang
 * 
 * @property int $id
 * @property int $barang_id
 * @property string $nama_barang
 * @property string|null $kategori
 * @property string|null $uom
 * @property int|null $stok_masuk
 * @property int|null $barang_keluar
 * @property int|null $stok_update
 * @property Carbon|null $tanggal_keluar
 * @property string|null $pic
 * @property string|null $keterangan
 * @property Carbon|null $created_at
 *
 * @package App\Models
 */
class StockGudang extends Model
{
	protected $table = 'stock_gudang';
	public $timestamps = false;

	protected $casts = [
		'barang_id' => 'int',
		'stok_masuk' => 'int',
		'barang_keluar' => 'int',
		'stok_update' => 'int',
		'tanggal_keluar' => 'datetime'
	];

	protected $fillable = [
		'barang_id',
		'nama_barang',
		'kategori',
		'uom',
		'stok_masuk',
		'barang_keluar',
		'stok_update',
		'tanggal_keluar',
		'pic',
		'keterangan'
	];
}
