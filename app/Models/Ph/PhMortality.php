<?php

namespace App\Models\Ph;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhMortality extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'ph_mortalities';
    protected $primaryKey = 'ph_mortality_id';
    public $timestamps = false;

    protected $fillable = [
        'ph_complaint_id',
        'day',
        'death',
        'culling'
    ];

    public function ph_complaint() {
        return $this->belongsTo(PhComplaint::class, 'ph_complaint_id');
    }
}
