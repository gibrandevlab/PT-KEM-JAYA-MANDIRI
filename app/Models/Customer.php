<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Customer
 * 
 * @property int $id
 * @property string $kode_customer
 * @property string $nama_perusahaan
 * @property string|null $alamat
 * @property string|null $alamat_gudang
 * @property string|null $kota
 * @property string|null $telepon
 * @property string|null $email
 * @property string|null $npwp
 * @property string|null $pic
 * @property string|null $pic_gudang
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $pic_phone
 * @property string|null $pic_email
 * @property string|null $pic_gudang_phone
 * 
 * @property Collection|CustomerAddress[] $customer_addresses
 * @property Collection|CustomerPic[] $customer_pics
 * @property Collection|Penawaran[] $penawarans
 * @property Collection|PurchaseOrder[] $purchase_orders
 *
 * @package App\Models
 */
class Customer extends Model
{
	protected $table = 'customers';

	protected $fillable = [
		'kode_customer',
		'nama_perusahaan',
		'alamat',
		'alamat_gudang',
		'kota',
		'telepon',
		'email',
		'npwp',
		'pic',
		'pic_gudang',
		'pic_phone',
		'pic_email',
		'pic_gudang_phone'
	];

	public function customer_addresses()
	{
		return $this->hasMany(CustomerAddress::class);
	}

	public function customer_pics()
	{
		return $this->hasMany(CustomerPic::class);
	}

	public function penawarans()
	{
		return $this->hasMany(Penawaran::class);
	}

	public function purchase_orders()
	{
		return $this->hasMany(PurchaseOrder::class);
	}
}
