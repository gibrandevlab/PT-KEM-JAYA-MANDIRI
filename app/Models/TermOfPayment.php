<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TermOfPayment
 * 
 * @property int $id
 * @property string $nama_term
 * @property Carbon|null $created_at
 *
 * @package App\Models
 */
class TermOfPayment extends Model
{
	protected $table = 'term_of_payments';
	public $timestamps = false;

	protected $fillable = [
		'nama_term'
	];
}
