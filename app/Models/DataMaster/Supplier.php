<?php

namespace App\Models\DataMaster;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Ph\PhComplaint;
use App\Models\Ph\PhPerformance;
use App\Models\Project\ProjectChickIn;
use App\Models\Purchase\Purchase;

class Supplier extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'suppliers';
    protected $primaryKey = 'supplier_id';

    protected $fillable = [
        'name',
        'alias',
        'pic_name',
        'type',
        'hatchery',
        'phone',
        'email',
        'address',
        'tax_num',
        'created_at',
        'created_by'
    ];

    public function product_components() {
        return $this->hasMany(ProductComponent::class, 'supplier_id');
    }

    public function ph_complaint() {
        return $this->hasMany(PhComplaint::class, 'supplier_id');
    }

    public function ph_performance() {
        return $this->hasMany(PhPerformance::class, 'supplier_id');
    }

    public function purchase() {
        return $this->hasMany(Purchase::class, 'supplier_id');
    }

    public function project_chick_in() {
        return $this->hasMany(ProjectChickIn::class, 'supplier_id');
    }
}
