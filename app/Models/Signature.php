<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'user_id',
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

    public function user(){
        return $this->belongsTo(User::class);
    }

}
