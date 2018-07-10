<?php

namespace creditocofrem;

use Caffeinated\Shinobi\Traits\ShinobiTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use creditocofrem\Notifications\MyResetPassword;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;


class User extends Authenticatable implements AuditableContract
{
    use Notifiable, ShinobiTrait, Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $auditInclude = [
        'name',
        'email',
        'password',
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MyResetPassword($token));
    }

    public function getRoles()
    {
        $roles = \DB::table('roles')->join('role_user','roles.id','role_user.role_id')->join('users','users.id','role_user.user_id')->where('users.id',$this->id)->select(['roles.*'])->get();
        return $roles;

    }
}
