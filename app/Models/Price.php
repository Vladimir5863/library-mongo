<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $primaryKey = "priceId";

    protected $fillable = ["bookId", "startDate", "endDate", "price"];

    public function book()
    {
        return $this->belongsTo(Book::class, "bookId", "bookId");
    }
}
