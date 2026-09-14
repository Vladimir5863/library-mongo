<?php
namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        "startDate",
        "endDate",
        "price",
        "active",
        "accountNumber",
        "autoRenew",
    ];
}
