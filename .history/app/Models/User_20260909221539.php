<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasRoles;
     use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    // Fungsi ini yang menentukan siapa yang boleh masuk ke /admin
    public function canAccessPanel(Panel $panel): bool
    {
        // Cek permission
        return $this->hasPermissionTo('access_panel');
    }
}
