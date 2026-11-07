<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PenawaranItem
 * 
 * @property int $id
 * @property int $penawaran_id
 * @property int|null $barang_id
 * @property string|null $kode_barang
 * @property string|null $nama_barang
 * @property string|null $deskripsi
 * @property string|null $note
 * @property string $satuan
 * @property float $qty
 * @property float|null $harga_satuan
 * @property float $diskon_persen
 * @property float|null $subtotal
 * @property int $urutan
 * 
 * @property Penawaran $penawaran
 * @property Barang|null $barang
 *
 * @package App\Models
 */
class PenawaranItem extends Model
{
	protected $table = 'penawaran_items';
	public $timestamps = false;

	protected $casts = [
		'penawaran_id' => 'int',
		'barang_id' => 'int',
		'qty' => 'float',
		'harga_satuan' => 'float',
		'diskon_persen' => 'float',
		'subtotal' => 'float',
		'urutan' => 'int'
	];

	protected $fillable = [
		'penawaran_id',
		'barang_id',
		'kode_barang',
		'nama_barang',
		'deskripsi',
		'note',
		'satuan',
		'qty',
		'harga_satuan',
		'diskon_persen',
		'subtotal',
		'urutan'
	];

	public function penawaran()
	{
		return $this->belongsTo(Penawaran::class);
	}

	public function barang()
	{
		return $this->belongsTo(Barang::class);
	}
}
