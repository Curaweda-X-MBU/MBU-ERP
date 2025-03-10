<?php

namespace App\Models\DataMaster;

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
        'company_id',
        'name',
        'created_at',
        'created_by',
    ];

    /**
     * Get the company that owns the Fcr
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Get all of the fcr_standard for the Fcr
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fcr_standard()
    {
        return $this->hasMany(FcrStandard::class, 'fcr_id');
    }
}
