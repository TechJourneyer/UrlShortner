<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserSubscription;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'urls_limit',
    ];

    // Relationship with user subscriptions
    public function userSubscriptions()
    {
        return $this->hasMany(UserSubscription::class , 'plan_id' , 'id');
    }
}