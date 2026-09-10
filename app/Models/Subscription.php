<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $primaryKey = "subscriptionId";

    protected $fillable = [
        "userId",
        "startDate",
        "endDate",
        "price",
        "active",
        "accountNumber",
        "autoRenew",
    ];

    public function user()
    {
        return $this->belongsTo(User::class, "userId", "userId");
    }
}
