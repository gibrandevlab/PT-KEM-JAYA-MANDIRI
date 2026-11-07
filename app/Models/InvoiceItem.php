<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class InvoiceItem
 * 
 * @property int $id
 * @property int $invoice_id
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
 * @property Invoice $invoice
 * @property Barang|null $barang
 *
 * @package App\Models
 */
class InvoiceItem extends Model
{
	protected $table = 'invoice_items';
	public $timestamps = false;

	protected $casts = [
		'invoice_id' => 'int',
		'barang_id' => 'int',
		'qty' => 'float',
		'harga_satuan' => 'float',
		'subtotal' => 'float',
		'urutan' => 'int'
	];

	protected $fillable = [
		'invoice_id',
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

	public function invoice()
	{
		return $this->belongsTo(Invoice::class);
	}

	public function barang()
	{
		return $this->belongsTo(Barang::class);
	}
}
