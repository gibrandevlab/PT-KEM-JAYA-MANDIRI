<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CustomerPic
 * 
 * @property int $id
 * @property int $customer_id
 * @property string $tipe
 * @property string $nama
 * @property string|null $telepon
 * @property string|null $email
 * 
 * @property Customer $customer
 *
 * @package App\Models
 */
class CustomerPic extends Model
{
	protected $table = 'customer_pics';
	public $timestamps = false;

	protected $casts = [
		'customer_id' => 'int'
	];

	protected $fillable = [
		'customer_id',
		'tipe',
		'nama',
		'telepon',
		'email'
	];

	public function customer()
	{
		return $this->belongsTo(Customer::class);
	}
}
