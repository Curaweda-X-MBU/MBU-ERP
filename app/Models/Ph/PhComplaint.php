<?php

namespace App\Models\Ph;

use App\Models\DataMaster\Kandang;
use App\Models\DataMaster\Product;
use App\Models\DataMaster\Supplier;
use App\Models\UserManagement\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhComplaint extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'ph_complaints';

    protected $primaryKey = 'ph_complaint_id';

    protected $fillable = [
        'product_id',
        'type',
        'population',
        'investigation_date',
        'description',
        'symptoms',
        'total_deaths',
        'total_culling',
        'culling_pic',
        'images',
        'kandang_id',
        'supplier_id',
        'hatchery',
        'created_by',
    ];

    public function ph_chick_in()
    {
        return $this->hasMany(PhChickIn::class, 'ph_complaint_id');
    }

    public function ph_mortality()
    {
        return $this->hasMany(PhMortality::class, 'ph_complaint_id');
    }

    public function cullingpic()
    {
        return $this->belongsTo(User::class, 'culling_pic', 'user_id');
    }

    public function createdby()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function kandang()
    {
        return $this->belongsTo(Kandang::class, 'kandang_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
