<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guard = 'admin';
    protected $table = 'admins';
    protected $fillable = [
        'name', 'email', 'password', 'user_id',
    ];

    // Example relationship to access the associated user (if applicable)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scope to check if a user ID exists in the admins table
    public function scopeIsAdmin($query, $userId)
    {
        return $query->where('user_id', $userId)
                     ->where('is_admin', true); 
    }
}
