<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PoAttachment
 * 
 * @property int $id
 * @property int $po_id
 * @property string $file_name
 * @property string $file_path
 * @property Carbon|null $uploaded_at
 * 
 * @property PurchaseOrder $purchase_order
 *
 * @package App\Models
 */
class PoAttachment extends Model
{
	protected $table = 'po_attachments';
	public $timestamps = false;

	protected $casts = [
		'po_id' => 'int',
		'uploaded_at' => 'datetime'
	];

	protected $fillable = [
		'po_id',
		'file_name',
		'file_path',
		'uploaded_at'
	];

	public function purchase_order()
	{
		return $this->belongsTo(PurchaseOrder::class, 'po_id');
	}
}
