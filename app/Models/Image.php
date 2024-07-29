<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;
    protected $table = 'user_image';
    protected $fillable = [
    'id',
    'user_id',
    'image',
    'company_logo',
    'company_logo1',
    'company_logo2',
    'gif',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function signature()
    {
        return $this->hasOne(Signature::class, 'user_id', 'user_id');
    }

}
