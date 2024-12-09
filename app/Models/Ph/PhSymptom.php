<?php

namespace App\Models\Ph;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhSymptom extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'ph_symptoms';

    protected $primaryKey = 'ph_symptom_id';

    protected $fillable = [
        'name',
        'created_at',
        'created_by',
    ];
}
