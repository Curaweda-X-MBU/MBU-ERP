<?php

namespace App\Models\Ph;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhChickIn extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'ph_chick_in';

    protected $primaryKey = 'ph_chick_in_id';

    public $timestamps = false;

    protected $fillable = [
        'ph_complaint_id',
        'date',
        'travel_letter_number',
        'delivery_time',
        'reception_time',
        'duration',
        'hatchery',
        'grade',
        'total_box',
        'total_heads',
    ];

    public function ph_complaint()
    {
        return $this->belongsTo(PhComplaint::class, 'ph_complaint_id');
    }
}
