<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Signature;


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
    // public function images()
    // {
    //     return $this->hasOne(Image::class, 'user_id', 'id');
    // }
    // public function isAdmin()
    // {
    // // Example logic to check if the user is an admin
    // return $this->role === 'admin'; // Adjust based on your actual role logic
    // }
    // public function signatures()
    // {
    //     return $this->hasMany(Signature::class, 'user_id');
    // }
    public function signatures()
    {
        return $this->hasMany(Signature::class, 'user_id');
    }

    public function images(){
        return $this-hasMany(Image::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
  

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
