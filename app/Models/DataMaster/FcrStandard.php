<?php

namespace App\Models\DataMaster;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FcrStandard extends Model
{
    use HasFactory;

    protected $table = 'fcr_standards';

    protected $primaryKey = 'fcr_standard_id';

    public $timestamps = false;

    protected $fillable = [
        'fcr_id',
        'day',
        'weight',
        'daily_gain',
        'avg_daily_gain',
        'daily_intake',
        'cum_intake',
        'fcr',
    ];

    /**
     * Get the fcr that owns the FcrStandard
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fcr()
    {
        return $this->belongsTo(Fcr::class, 'fcr_id');
    }
}
