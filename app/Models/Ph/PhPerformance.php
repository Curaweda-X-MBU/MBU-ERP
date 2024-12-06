<?php

namespace App\Models\Ph;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\UserManagement\User;
use App\Models\DataMaster\Kandang;
use App\Models\DataMaster\Supplier;

class PhPerformance extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'ph_performances';
    protected $primaryKey = 'ph_performance_id';

    protected $fillable = [
        'kandang_id',
        'chick_in_date',
        'population',
        'supplier_id',
        'hatchery',
        'death',
        'culling',
        'depletion',
        'percentage_depletion',
        'bw',
        'created_by',
    ];

    public function createdby() {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
    
    public function kandang() {
        return $this->belongsTo(Kandang::class, 'kandang_id');
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
