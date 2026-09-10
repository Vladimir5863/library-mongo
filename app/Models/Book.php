<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    protected $primaryKey = "bookId";

    use HasFactory;

    protected $fillable = [
        "preview_image",
        "title",
        "author",
        "genre",
        "description",
        "numberOfPages",
        "language",
        "publicationDate",
        "publisher",
        "remainingForLoan",
        "remainingForSell",
        "preview",
    ];

    public function prices()
    {
        return $this->hasMany(Price::class, "bookId", "bookId");
    }

    public function currentPrice()
    {
        return $this->hasOne(Price::class, "bookId", "bookId")
            ->whereDate("startDate", "<=", Carbon::today())
            ->whereDate("endDate", ">=", Carbon::today());
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, "bookId", "bookId");
    }

    public function sells()
    {
        return $this->hasMany(Sell::class, "bookId", "bookId");
    }

    public function uses()
    {
        return $this->hasMany(Uses::class, "bookId", "bookId");
    }
}
