<?php
namespace App\Models;

use MongoDB\Laravel\Auth\User as Authenticatable;
use MongoDB\Laravel\Relations\EmbedsMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory;
    use SoftDeletes;

    protected $connection = "mongodb";
    protected $table = "users";

    protected $fillable = [
        "avatar",
        "name",
        "surname",
        "email",
        "password",
        "userType",
        "numberOfLoans",
        "accountNumber",
    ];

    protected $hidden = ["password"];

    // Ugnežđena istorija pretplata — deo istog dokumenta, ne posebna kolekcija
    public function subscriptions(): EmbedsMany
    {
        return $this->embedsMany(Subscription::class);
    }

    public function activeSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->get()
            ->first(
                fn($s) => $s->startDate <= Carbon::today() &&
                    $s->endDate >= Carbon::today(),
            );
    }

    public function hasActiveSubscription(): bool
    {
        return (bool) $this->activeSubscription();
    }

    // Referencirane kolekcije — nezavisne, neograničeno rastu
    public function loans()
    {
        return $this->hasMany(Loan::class, "userId", "_id");
    }

    public function sells()
    {
        return $this->hasMany(Sell::class, "userId", "_id");
    }

    public function uses()
    {
        return $this->hasMany(Uses::class, "userId", "_id");
    }
}
