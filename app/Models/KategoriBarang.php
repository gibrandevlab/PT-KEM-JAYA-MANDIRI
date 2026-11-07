<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class KategoriBarang
 * 
 * @property int $id
 * @property string $nama_kategori
 * @property Carbon|null $created_at
 * 
 * @property Collection|Barang[] $barangs
 *
 * @package App\Models
 */
class KategoriBarang extends Model
{
	protected $table = 'kategori_barang';
	public $timestamps = false;

	protected $fillable = [
		'nama_kategori'
	];

	public function barangs()
	{
		return $this->hasMany(Barang::class, 'kategori_id');
	}
}
