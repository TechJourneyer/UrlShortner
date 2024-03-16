<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Url extends Model
{
    use HasFactory,SoftDeletes;


    protected $fillable = [
        'original_url',
        'shortened_url',
        'user_id'
    ];

    
}
