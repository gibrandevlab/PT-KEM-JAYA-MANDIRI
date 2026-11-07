<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SuratJalan
 * 
 * @property int $id
 * @property string $no_sj
 * @property Carbon $tanggal
 * @property int|null $po_id
 * @property int|null $penawaran_id
 * @property int|null $customer_id
 * @property string $nama_penerima
 * @property string $alamat_pengiriman
 * @property string|null $kota_tujuan
 * @property string|null $telepon_penerima
 * @property string|null $nama_pengirim
 * @property string|null $pic_penerima
 * @property string|null $no_po
 * @property string|null $kendaraan
 * @property string|null $no_polisi
 * @property string|null $catatan
 * @property string $status
 * @property int $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property bool|null $is_history
 * 
 * @property PurchaseOrder|null $purchase_order
 * @property Penawaran|null $penawaran
 * @property User $user
 * @property Collection|Invoice[] $invoices
 * @property Collection|SuratJalanItem[] $surat_jalan_items
 *
 * @package App\Models
 */
class SuratJalan extends Model
{
	protected $table = 'surat_jalan';

	protected $casts = [
		'tanggal' => 'datetime',
		'po_id' => 'int',
		'penawaran_id' => 'int',
		'customer_id' => 'int',
		'created_by' => 'int',
		'is_history' => 'bool'
	];

	protected $fillable = [
		'no_sj',
		'tanggal',
		'po_id',
		'penawaran_id',
		'customer_id',
		'nama_penerima',
		'alamat_pengiriman',
		'kota_tujuan',
		'telepon_penerima',
		'nama_pengirim',
		'pic_penerima',
		'no_po',
		'kendaraan',
		'no_polisi',
		'catatan',
		'status',
		'created_by',
		'is_history'
	];

	public function purchase_order()
	{
		return $this->belongsTo(PurchaseOrder::class, 'po_id');
	}

	public function penawaran()
	{
		return $this->belongsTo(Penawaran::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function invoices()
	{
		return $this->hasMany(Invoice::class, 'sj_id');
	}

	public function surat_jalan_items()
	{
		return $this->hasMany(SuratJalanItem::class, 'sj_id');
	}
}
