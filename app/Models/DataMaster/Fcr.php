<?php

namespace App\Models\DataMaster;

use App\Models\Project\Project;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fcr extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'fcr';

    protected $primaryKey = 'fcr_id';

    protected $fillable = [
        'name',
        'value',
        'product_id',
        'uom_id',
        'created_at',
        'created_by',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class, 'uom_id');
    }

    // public function project()
    // {
    //     return $this->hasMany(Project::class, 'kandang_id');
    // }
}
