<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory;

    use SoftDeletes;

    protected $primaryKey = "userId";

    protected $fillable = [
        "avatar",
        "name",
        "surname",
        "email",
        "password",
        "userType",
        "numberOfLoans",
    ];

    protected $hidden = ["password"];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, "userId", "userId");
    }

    // Aktivna pretplata — između startDate i endDate
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class, "userId", "userId")
            ->whereDate("startDate", "<=", Carbon::today())
            ->whereDate("endDate", ">=", Carbon::today());
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, "userId", "userId");
    }

    public function sells()
    {
        return $this->hasMany(Sell::class, "userId", "userId");
    }

    public function uses()
    {
        return $this->hasMany(Uses::class, "userId", "userId");
    }
}
