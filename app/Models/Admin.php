<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $table = 'admins'; 
    protected $primaryKey = 'admin_id';

    protected $fillable = [
        'admin_username',
        'admin_email',
        'admin_password',
    ];

    protected $hidden = [
        'admin_password',
        'remember_token',
    ];

    /**
     * Overrides Laravel's default 'password' column name.
     */
    public function getAuthPasswordName()
    {
        return 'admin_password';
    }
}
