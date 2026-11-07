<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PoCustomer
 * 
 * @property int $id
 * @property string|null $kode_po_customer
 * @property string $nama_perusahaan
 * @property string|null $alamat
 * @property string|null $kota
 * @property string|null $telepon
 * @property string|null $email
 * @property string|null $pic
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|PurchaseOrder[] $purchase_orders
 *
 * @package App\Models
 */
class PoCustomer extends Model
{
	protected $table = 'po_customers';

	protected $fillable = [
		'kode_po_customer',
		'nama_perusahaan',
		'alamat',
		'kota',
		'telepon',
		'email',
		'pic'
	];

	public function purchase_orders()
	{
		return $this->hasMany(PurchaseOrder::class);
	}
}
