<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;
    protected $table = 'image_user';
    protected $fillable = [
    'id',
    'user_id',
    'image',
    'company_logo',
    'company_logo1',
    'company_logo2',
    'gif',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
