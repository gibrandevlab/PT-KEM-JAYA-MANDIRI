<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class InvoiceAttachment
 * 
 * @property int $id
 * @property int $invoice_id
 * @property string $file_name
 * @property string $file_path
 * @property Carbon|null $created_at
 * 
 * @property Invoice $invoice
 *
 * @package App\Models
 */
class InvoiceAttachment extends Model
{
	protected $table = 'invoice_attachments';
	public $timestamps = false;

	protected $casts = [
		'invoice_id' => 'int'
	];

	protected $fillable = [
		'invoice_id',
		'file_name',
		'file_path'
	];

	public function invoice()
	{
		return $this->belongsTo(Invoice::class);
	}
}
