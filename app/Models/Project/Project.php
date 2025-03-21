<?php

namespace App\Models\Project;

use App\Models\DataMaster\Fcr;
use App\Models\DataMaster\Kandang;
use App\Models\DataMaster\ProductCategory;
use App\Models\Expense\ExpenseKandang;
use App\Models\Marketing\MarketingProduct;
use App\Models\Purchase\PurchaseItem;
use App\Models\UserManagement\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'projects';

    protected $primaryKey = 'project_id';

    protected $fillable = [
        'product_category_id',
        'kandang_id',
        'capacity',
        'farm_type',
        'period',
        'pic',
        'fcr_id',
        'standard_mortality',
        'total_budget',
        'chickin_status',
        'project_status',
        'approval_date',
        'chickin_approval_date',
        'first_day_old_chick',
        'closing_date',
        'closing_by',
        'created_by',
    ];

    protected static function boot()
    {
        parent::boot();

        static::updated(function(Project $project) {
            if ($project->isDirty('approval_date') && $project->approval_date !== null) {
                DB::transaction(function() use ($project) {
                    ActiveProjectLog::create([
                        'period'     => (Carbon::parse($project->approval_date)->year * 12) + Carbon::parse($project->approval_date)->month,
                        'project_id' => $project->project_id,
                    ]);
                });
            }

            if ($project->isDirty('closing_date') && $project->closing_date !== null) {
                DB::transaction(function() use ($project) {
                    $lastPeriod = ActiveProjectLog::where('project_id', $project->project_id)
                        ->value('period');

                    $closingDate = $project->closing_date;
                    $newPeriod   = (Carbon::parse($closingDate)->year * 12) + Carbon::parse($closingDate)->month;

                    if ($lastPeriod < $newPeriod) {
                        for ($period = $lastPeriod + 1; $period <= $newPeriod; $period++) {
                            ActiveProjectLog::create([
                                'period'     => $period,
                                'project_id' => $project->project_id,
                            ]);
                        }
                    }
                });
            }
        });
    }

    public function product_category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function kandang()
    {
        return $this->belongsTo(Kandang::class, 'kandang_id');
    }

    public function fcr()
    {
        return $this->belongsTo(Fcr::class, 'fcr_id');
    }

    public function createdby()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function closingby()
    {
        return $this->belongsTo(User::class, 'closing_by', 'user_id');
    }

    public function project_phase()
    {
        return $this->hasMany(ProjectPhase::class, 'project_id');
    }

    public function project_budget()
    {
        return $this->hasMany(ProjectBudget::class, 'project_id');
    }

    public function purchase_item()
    {
        return $this->hasMany(PurchaseItem::class, 'project_id');
    }

    public function project_recording()
    {
        return $this->hasMany(ProjectRecording::class, 'project_id');
    }

    public function project_chick_in()
    {
        return $this->hasMany(ProjectChickIn::class, 'project_id');
    }

    public function recording()
    {
        return $this->hasMany(Recording::class, 'project_id');
    }

    public function marketing_products()
    {
        return $this->hasMany(MarketingProduct::class, 'project_id');
    }

    public function expense_kandangs()
    {
        return $this->hasMany(ExpenseKandang::class, 'project_id');
    }

    public function active_projects_log()
    {
        return $this->hasMany(ActiveProjectLog::class, 'project_id');
    }
}
