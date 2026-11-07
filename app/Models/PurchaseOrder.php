<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PurchaseOrder
 * 
 * @property int $id
 * @property string $no_po
 * @property string|null $no_po_customer
 * @property string $jenis
 * @property Carbon $tanggal
 * @property int|null $penawaran_id
 * @property int|null $customer_id
 * @property int|null $po_customer_id
 * @property int|null $vendor_id
 * @property string $nama_perusahaan
 * @property string|null $term_of_delivery
 * @property string|null $delivery_time
 * @property string|null $term_of_payment
 * @property string|null $alamat
 * @property string|null $telepon
 * @property Carbon|null $tanggal_kirim
 * @property string|null $catatan
 * @property float|null $subtotal
 * @property float $ppn_persen
 * @property float|null $ppn_nominal
 * @property float|null $total
 * @property string $status
 * @property int $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $kategori_po
 * @property string|null $pic
 * @property string|null $currency
 * @property float|null $diskon_persen
 * @property float|null $diskon_nominal
 * @property bool|null $is_history
 * 
 * @property PoCustomer|null $po_customer
 * @property Penawaran|null $penawaran
 * @property Customer|null $customer
 * @property Vendor|null $vendor
 * @property User $user
 * @property Collection|Invoice[] $invoices
 * @property Collection|PoAttachment[] $po_attachments
 * @property Collection|PoItem[] $po_items
 * @property Collection|SuratJalan[] $surat_jalans
 *
 * @package App\Models
 */
class PurchaseOrder extends Model
{
	protected $table = 'purchase_order';

	protected $casts = [
		'tanggal' => 'datetime',
		'penawaran_id' => 'int',
		'customer_id' => 'int',
		'po_customer_id' => 'int',
		'vendor_id' => 'int',
		'tanggal_kirim' => 'datetime',
		'subtotal' => 'float',
		'ppn_persen' => 'float',
		'ppn_nominal' => 'float',
		'total' => 'float',
		'created_by' => 'int',
		'diskon_persen' => 'float',
		'diskon_nominal' => 'float',
		'is_history' => 'bool'
	];

	protected $fillable = [
		'no_po',
		'no_po_customer',
		'jenis',
		'tanggal',
		'penawaran_id',
		'customer_id',
		'po_customer_id',
		'vendor_id',
		'nama_perusahaan',
		'term_of_delivery',
		'delivery_time',
		'term_of_payment',
		'alamat',
		'telepon',
		'tanggal_kirim',
		'catatan',
		'subtotal',
		'ppn_persen',
		'ppn_nominal',
		'total',
		'status',
		'created_by',
		'kategori_po',
		'pic',
		'currency',
		'diskon_persen',
		'diskon_nominal',
		'is_history'
	];

	public function po_customer()
	{
		return $this->belongsTo(PoCustomer::class);
	}

	public function penawaran()
	{
		return $this->belongsTo(Penawaran::class);
	}

	public function customer()
	{
		return $this->belongsTo(Customer::class);
	}

	public function vendor()
	{
		return $this->belongsTo(Vendor::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function invoices()
	{
		return $this->hasMany(Invoice::class, 'po_id');
	}

	public function po_attachments()
	{
		return $this->hasMany(PoAttachment::class, 'po_id');
	}

	public function po_items()
	{
		return $this->hasMany(PoItem::class, 'po_id');
	}

	public function surat_jalans()
	{
		return $this->hasMany(SuratJalan::class, 'po_id');
	}
}
