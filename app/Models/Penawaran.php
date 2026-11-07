<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Penawaran
 * 
 * @property int $id
 * @property string $no_penawaran
 * @property Carbon $tanggal
 * @property int|null $customer_id
 * @property string $nama_customer
 * @property string|null $alamat_customer
 * @property string|null $alamat_customer_2
 * @property string|null $telepon_customer
 * @property string|null $npwp_customer
 * @property string|null $perihal
 * @property string|null $term_of_delivery
 * @property string|null $delivery_time
 * @property string|null $term_of_payment
 * @property string|null $catatan
 * @property Carbon|null $berlaku_sampai
 * @property float|null $subtotal
 * @property float $diskon_persen
 * @property float|null $diskon_nominal
 * @property float $ppn_persen
 * @property float|null $ppn_nominal
 * @property float|null $total
 * @property string $status
 * @property int $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $kode_sdk
 * @property bool|null $use_sdk
 * @property string|null $com_contact
 * @property bool|null $is_revisi
 * @property string|null $revisi_ke
 * @property string|null $up
 * @property string|null $mobile
 * @property string|null $email_up
 * @property bool|null $is_history
 * 
 * @property Customer|null $customer
 * @property User $user
 * @property Collection|Invoice[] $invoices
 * @property Collection|PenawaranAttachment[] $penawaran_attachments
 * @property Collection|PenawaranItem[] $penawaran_items
 * @property Collection|PurchaseOrder[] $purchase_orders
 * @property Collection|SuratJalan[] $surat_jalans
 *
 * @package App\Models
 */
class Penawaran extends Model
{
	protected $table = 'penawaran';

	protected $casts = [
		'tanggal' => 'datetime',
		'customer_id' => 'int',
		'berlaku_sampai' => 'datetime',
		'subtotal' => 'float',
		'diskon_persen' => 'float',
		'diskon_nominal' => 'float',
		'ppn_persen' => 'float',
		'ppn_nominal' => 'float',
		'total' => 'float',
		'created_by' => 'int',
		'use_sdk' => 'bool',
		'is_revisi' => 'bool',
		'is_history' => 'bool'
	];

	protected $fillable = [
		'no_penawaran',
		'tanggal',
		'customer_id',
		'nama_customer',
		'alamat_customer',
		'alamat_customer_2',
		'telepon_customer',
		'npwp_customer',
		'perihal',
		'term_of_delivery',
		'delivery_time',
		'term_of_payment',
		'catatan',
		'berlaku_sampai',
		'subtotal',
		'diskon_persen',
		'diskon_nominal',
		'ppn_persen',
		'ppn_nominal',
		'total',
		'status',
		'created_by',
		'kode_sdk',
		'use_sdk',
		'com_contact',
		'is_revisi',
		'revisi_ke',
		'up',
		'mobile',
		'email_up',
		'is_history'
	];

	public function customer()
	{
		return $this->belongsTo(Customer::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function invoices()
	{
		return $this->hasMany(Invoice::class);
	}

	public function penawaran_attachments()
	{
		return $this->hasMany(PenawaranAttachment::class);
	}

	public function penawaran_items()
	{
		return $this->hasMany(PenawaranItem::class);
	}

	public function purchase_orders()
	{
		return $this->hasMany(PurchaseOrder::class);
	}

	public function surat_jalans()
	{
		return $this->hasMany(SuratJalan::class);
	}
}
