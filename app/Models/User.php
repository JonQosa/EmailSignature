<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    const ROLE_ADMIN = 'admin';

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'id',
        'user_id',
        'is_admin',
        'name',
        'last_name',
        'email',
        'title',
        'company',
        'meeting_link',
        'address',
        'website',
        'linkedin_profile',
        'company_linkedin',
        'facebook',
        'feedback',
        'twitter',
        'instagram',
        'phone',
        'role',
        'description',
        'password',
    ];
    public function image(){
        return $this->hasMany(Image::class);
    }
    // public function isAdmin()
    // {
    // // Example logic to check if the user is an admin
    // return $this->role === 'admin'; // Adjust based on your actual role logic
    // }



    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
