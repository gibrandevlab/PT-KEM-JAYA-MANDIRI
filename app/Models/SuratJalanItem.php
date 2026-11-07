<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SuratJalanItem
 * 
 * @property int $id
 * @property int $sj_id
 * @property int|null $barang_id
 * @property string|null $kode_barang
 * @property string|null $nama_barang
 * @property string $satuan
 * @property float $qty
 * @property string|null $keterangan
 * @property int $urutan
 * 
 * @property SuratJalan $surat_jalan
 * @property Barang|null $barang
 *
 * @package App\Models
 */
class SuratJalanItem extends Model
{
	protected $table = 'surat_jalan_items';
	public $timestamps = false;

	protected $casts = [
		'sj_id' => 'int',
		'barang_id' => 'int',
		'qty' => 'float',
		'urutan' => 'int'
	];

	protected $fillable = [
		'sj_id',
		'barang_id',
		'kode_barang',
		'nama_barang',
		'satuan',
		'qty',
		'keterangan',
		'urutan'
	];

	public function surat_jalan()
	{
		return $this->belongsTo(SuratJalan::class, 'sj_id');
	}

	public function barang()
	{
		return $this->belongsTo(Barang::class);
	}
}
