<?php

namespace App\Models\UserManagement;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\ResetPassword;
use Spatie\Permission\Traits\HasRoles;

use App\Models\DataMaster\Department;
use App\Models\DataMaster\Customer;
use App\Models\DataMaster\Kandang;
use App\Models\Ph\PhComplaint;
use App\Models\Project\Project;
use App\Models\Purchase\Purchase;

class User extends Authenticatable implements CanResetPassword
{
    use HasRoles;
    use Notifiable;
    use HasFactory;
    use SoftDeletes;
    protected $table = 'users';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'npk',
        'name',
        'email',
        'image',
        'password',
        'phone',
        'department_id',
        'role_id',
        'is_active',
        'created_at',
        'created_by'
    ];

    protected $hidden = [
        'password',
    ];

    public function scopeActive($query) {
        return $query->where('is_active', true);
    }

    public function department() {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function role() {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function customers() {
        return $this->hasMany(Customer::class, 'assign_to', 'user_id');
    }

    public function kandangs() {
        return $this->hasMany(Kandang::class, 'pic', 'user_id');
    }

    public function ph_complaint() {
        return $this->hasMany(PhComplaint::class, 'created_by', 'user_id');
    }

    public function project() {
        return $this->hasMany(Project::class, 'created_by', 'user_id');
    }

    public function purchase() {
        return $this->hasMany(Purchase::class, 'created_by', 'user_id');
    }

    public function cullingpic() {
        return $this->hasMany(PhComplaint::class, 'culling_pic', 'user_id');
    }

    public static function getData($all, $active, $whereClause = false) {
        $user = self::query();
        if ($active) {
            $user->active();
        } 

        $user->with([
            'department' => function($query) {
                $query->with([ 'company', 'location' ]);
            },
            'role'
        ]);

        if ($whereClause) {
            return $all ? $user->where($whereClause)->get() : $user->where($whereClause)->first();
        }
        
        return $user->get();
    }

    public static function authUser($email) {
        return self::with('role')
            ->where('email', $email)->first();
    }

    public function sendPasswordResetNotification($token) {
        $this->notify(new ResetPassword($token));
    }

}
