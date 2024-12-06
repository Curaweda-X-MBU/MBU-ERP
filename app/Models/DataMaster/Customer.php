<?php

namespace App\Models\DataMaster;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\UserManagement\User;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'customers';
    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'name',
        'assign_to',
        'type',
        'phone',
        'email',
        'address',
        'tax_num',
        'created_at',
        'created_by'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'assign_to', 'user_id');
    }
}
