<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Invoice
 * 
 * @property int $id
 * @property string $no_invoice
 * @property Carbon $tanggal
 * @property Carbon|null $jatuh_tempo
 * @property int|null $sj_id
 * @property int|null $po_id
 * @property int|null $penawaran_id
 * @property int|null $customer_id
 * @property string $nama_customer
 * @property string|null $alamat_customer
 * @property string|null $telepon_customer
 * @property string|null $npwp
 * @property string|null $no_faktur_pajak
 * @property string|null $term_of_payment
 * @property string|null $catatan
 * @property int|null $bank_account_id
 * @property float|null $subtotal
 * @property float $diskon_persen
 * @property float|null $diskon_nominal
 * @property float $ppn_persen
 * @property float|null $ppn_nominal
 * @property bool $is_dpp
 * @property float $dpp_nominal
 * @property float|null $total
 * @property string $status
 * @property int $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property bool|null $is_revisi
 * @property string|null $revisi_ke
 * @property string|null $tipe_invoice
 * @property string|null $po_number
 * @property Carbon|null $tanggal_po
 * @property string|null $shipment_number
 * @property float|null $dp_persen
 * @property float|null $dp_nominal
 * @property bool|null $is_history
 * @property int|null $dp_ref_invoice_id
 * @property string|null $status_payment
 * @property Carbon|null $tanggal_payment
 * @property Carbon|null $tanggal_kirim_dokumen
 * 
 * @property SuratJalan|null $surat_jalan
 * @property PurchaseOrder|null $purchase_order
 * @property Penawaran|null $penawaran
 * @property User $user
 * @property Collection|InvoiceAttachment[] $invoice_attachments
 * @property Collection|InvoiceItem[] $invoice_items
 *
 * @package App\Models
 */
class Invoice extends Model
{
	protected $table = 'invoice';

	protected $casts = [
		'tanggal' => 'datetime',
		'jatuh_tempo' => 'datetime',
		'sj_id' => 'int',
		'po_id' => 'int',
		'penawaran_id' => 'int',
		'customer_id' => 'int',
		'bank_account_id' => 'int',
		'subtotal' => 'float',
		'diskon_persen' => 'float',
		'diskon_nominal' => 'float',
		'ppn_persen' => 'float',
		'ppn_nominal' => 'float',
		'is_dpp' => 'bool',
		'dpp_nominal' => 'float',
		'total' => 'float',
		'created_by' => 'int',
		'is_revisi' => 'bool',
		'tanggal_po' => 'datetime',
		'dp_persen' => 'float',
		'dp_nominal' => 'float',
		'is_history' => 'bool',
		'dp_ref_invoice_id' => 'int',
		'tanggal_payment' => 'datetime',
		'tanggal_kirim_dokumen' => 'datetime'
	];

	protected $fillable = [
		'no_invoice',
		'tanggal',
		'jatuh_tempo',
		'sj_id',
		'po_id',
		'penawaran_id',
		'customer_id',
		'nama_customer',
		'alamat_customer',
		'telepon_customer',
		'npwp',
		'no_faktur_pajak',
		'term_of_payment',
		'catatan',
		'bank_account_id',
		'subtotal',
		'diskon_persen',
		'diskon_nominal',
		'ppn_persen',
		'ppn_nominal',
		'is_dpp',
		'dpp_nominal',
		'total',
		'status',
		'created_by',
		'is_revisi',
		'revisi_ke',
		'tipe_invoice',
		'po_number',
		'tanggal_po',
		'shipment_number',
		'dp_persen',
		'dp_nominal',
		'is_history',
		'dp_ref_invoice_id',
		'status_payment',
		'tanggal_payment',
		'tanggal_kirim_dokumen'
	];

	public function surat_jalan()
	{
		return $this->belongsTo(SuratJalan::class, 'sj_id');
	}

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

	public function invoice_attachments()
	{
		return $this->hasMany(InvoiceAttachment::class);
	}

	public function invoice_items()
	{
		return $this->hasMany(InvoiceItem::class);
	}
}
