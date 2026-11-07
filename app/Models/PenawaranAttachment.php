<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PenawaranAttachment
 * 
 * @property int $id
 * @property int $penawaran_id
 * @property string $file_name
 * @property string $file_path
 * @property Carbon|null $created_at
 * 
 * @property Penawaran $penawaran
 *
 * @package App\Models
 */
class PenawaranAttachment extends Model
{
	protected $table = 'penawaran_attachments';
	public $timestamps = false;

	protected $casts = [
		'penawaran_id' => 'int'
	];

	protected $fillable = [
		'penawaran_id',
		'file_name',
		'file_path'
	];

	public function penawaran()
	{
		return $this->belongsTo(Penawaran::class);
	}
}
