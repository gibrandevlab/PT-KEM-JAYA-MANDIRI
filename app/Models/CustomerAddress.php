<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CustomerAddress
 * 
 * @property int $id
 * @property int $customer_id
 * @property string $label_alamat
 * @property string $alamat_lengkap
 * @property Carbon|null $created_at
 * 
 * @property Customer $customer
 *
 * @package App\Models
 */
class CustomerAddress extends Model
{
	protected $table = 'customer_addresses';
	public $timestamps = false;

	protected $casts = [
		'customer_id' => 'int'
	];

	protected $fillable = [
		'customer_id',
		'label_alamat',
		'alamat_lengkap'
	];

	public function customer()
	{
		return $this->belongsTo(Customer::class);
	}
}
