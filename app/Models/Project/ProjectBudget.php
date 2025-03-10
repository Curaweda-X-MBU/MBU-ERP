<?php

namespace App\Models\Project;

use App\Models\DataMaster\Nonstock;
use App\Models\DataMaster\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectBudget extends Model
{
    use HasFactory;

    protected $table = 'project_budgets';

    protected $primaryKey = 'project_budget_id';

    protected $fillable = [
        'product_id',
        'nonstock_id',
        'qty',
        'price',
        'total',
        'project_id',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function nonstock()
    {
        return $this->belongsTo(Nonstock::class, 'nonstock_id');
    }
}
