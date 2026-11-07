<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Barang
 * 
 * @property int $id
 * @property string $kode_barang
 * @property string|null $nama_barang
 * @property int|null $kategori_id
 * @property string $satuan
 * @property float $stok
 * @property float $stok_minimum
 * @property float|null $harga_beli
 * @property string|null $tahun
 * @property float|null $harga_satuan
 * @property string|null $deskripsi
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property KategoriBarang|null $kategori_barang
 * @property Collection|InvoiceItem[] $invoice_items
 * @property Collection|PenawaranItem[] $penawaran_items
 * @property Collection|PoItem[] $po_items
 * @property Collection|StokLog[] $stok_logs
 * @property Collection|SuratJalanItem[] $surat_jalan_items
 *
 * @package App\Models
 */
class Barang extends Model
{
	protected $table = 'barang';

	protected $casts = [
		'kategori_id' => 'int',
		'stok' => 'float',
		'stok_minimum' => 'float',
		'harga_beli' => 'float',
		'harga_satuan' => 'float'
	];

	protected $fillable = [
		'kode_barang',
		'nama_barang',
		'kategori_id',
		'satuan',
		'stok',
		'stok_minimum',
		'harga_beli',
		'tahun',
		'harga_satuan',
		'deskripsi'
	];

	public function kategori_barang()
	{
		return $this->belongsTo(KategoriBarang::class, 'kategori_id');
	}

	public function invoice_items()
	{
		return $this->hasMany(InvoiceItem::class);
	}

	public function penawaran_items()
	{
		return $this->hasMany(PenawaranItem::class);
	}

	public function po_items()
	{
		return $this->hasMany(PoItem::class);
	}

	public function stok_logs()
	{
		return $this->hasMany(StokLog::class);
	}

	public function surat_jalan_items()
	{
		return $this->hasMany(SuratJalanItem::class);
	}
}
