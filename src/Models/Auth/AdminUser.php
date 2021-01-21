<?php

namespace Jmhc\Admin\Models\Auth;

use Jmhc\Admin\Traits\SerializeDate;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminUser extends Authenticatable implements JWTSubject
{
    use HasRoles, SerializeDate;

    protected $guard_name = 'admin';

    protected $fillable = [
        'username',
        'name',
        'avatar',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function getTable()
    {
        return config('admin.table_names.admin_users', parent::getTable());
    }

    public function findForPassport($username)
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * 密码修改器
     *
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

}
