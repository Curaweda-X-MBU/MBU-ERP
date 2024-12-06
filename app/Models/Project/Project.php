<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DataMaster\Product;
use App\Models\DataMaster\Kandang;
use App\Models\Purchase\PurchaseItem;
use App\Models\DataMaster\Fcr;
use App\Models\UserManagement\User;


class Project extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'projects';
    protected $primaryKey = 'project_id';

    protected $fillable = [
        'product_id',
        'kandang_id',
        'capacity',
        'farm_type',
        'period',
        'pic',
        'fcr_id',
        'target_depletion',
        'total_budget',
        'chickin_status',
        'project_status',
        'approval_date',
        'chickin_approval_date',
        'created_by'
    ];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function kandang() {
        return $this->belongsTo(Kandang::class, 'kandang_id');
    }

    public function fcr() {
        return $this->belongsTo(Fcr::class, 'fcr_id');
    }

    public function createdby() {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function project_phase() {
        return $this->hasMany(ProjectPhase::class, 'project_id');
    }

    public function project_budget() {
        return $this->hasMany(ProjectBudget::class, 'project_id');
    }

    public function purchase_item() {
        return $this->hasMany(PurchaseItem::class, 'project_id');
    }

    public function project_recording() {
        return $this->hasMany(ProjectRecording::class, 'project_id');
    }

    public function project_chick_in() {
        return $this->hasMany(ProjectChickIn::class, 'project_id');
    }
}
