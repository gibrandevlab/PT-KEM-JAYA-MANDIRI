<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PoItem
 * 
 * @property int $id
 * @property int $po_id
 * @property int|null $barang_id
 * @property string|null $kode_barang
 * @property string|null $nama_barang
 * @property string|null $deskripsi
 * @property string $satuan
 * @property float $qty
 * @property float|null $harga_satuan
 * @property float|null $subtotal
 * @property int $urutan
 * 
 * @property PurchaseOrder $purchase_order
 * @property Barang|null $barang
 *
 * @package App\Models
 */
class PoItem extends Model
{
	protected $table = 'po_items';
	public $timestamps = false;

	protected $casts = [
		'po_id' => 'int',
		'barang_id' => 'int',
		'qty' => 'float',
		'harga_satuan' => 'float',
		'subtotal' => 'float',
		'urutan' => 'int'
	];

	protected $fillable = [
		'po_id',
		'barang_id',
		'kode_barang',
		'nama_barang',
		'deskripsi',
		'satuan',
		'qty',
		'harga_satuan',
		'subtotal',
		'urutan'
	];

	public function purchase_order()
	{
		return $this->belongsTo(PurchaseOrder::class, 'po_id');
	}

	public function barang()
	{
		return $this->belongsTo(Barang::class);
	}
}
