<?php

namespace App\Models\DataMaster;

use App\Models\Expense\ExpenseKandang;
use App\Models\Marketing\MarketingProduct;
use App\Models\Ph\PhComplaint;
use App\Models\Ph\PhPerformance;
use App\Models\Project\Project;
use App\Models\UserManagement\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kandang extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'kandang';

    protected $primaryKey = 'kandang_id';

    protected $fillable = [
        'name',
        'capacity',
        'type',
        'pic',
        'location_id',
        'company_id',
        'project_status',
        'created_by',
    ];

    protected $appends = ['latest_period', 'latest_project'];

    public function getLatestPeriodAttribute()
    {
        return $this->latest_project ? $this->latest_project->period : 0;
    }

    public function getLatestProjectAttribute()
    {
        return $this->project->sortByDesc('period')->first() ?? null;
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'pic', 'user_id');
    }

    public function expense_kandang()
    {
        return $this->hasMany(ExpenseKandang::class, 'kandang_id');
    }

    public function ph_complaint()
    {
        return $this->hasMany(PhComplaint::class, 'kandang_id');
    }

    public function ph_performance()
    {
        return $this->hasMany(PhPerformance::class, 'kandang_id');
    }

    public function warehouse()
    {
        return $this->hasMany(Warehouse::class, 'kandang_id');
    }

    public function project()
    {
        return $this->hasMany(Project::class, 'kandang_id');
    }

    public function marketing_products()
    {
        return $this->hasMany(MarketingProduct::class, 'kandang_id', 'kandang_id');
    }
}
