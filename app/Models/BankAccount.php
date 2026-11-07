<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BankAccount
 * 
 * @property int $id
 * @property string $nama_bank
 * @property string $no_rekening
 * @property string $atas_nama
 * @property bool|null $is_aktif
 * @property Carbon|null $created_at
 *
 * @package App\Models
 */
class BankAccount extends Model
{
	protected $table = 'bank_accounts';
	public $timestamps = false;

	protected $casts = [
		'is_aktif' => 'bool'
	];

	protected $fillable = [
		'nama_bank',
		'no_rekening',
		'atas_nama',
		'is_aktif'
	];
}
