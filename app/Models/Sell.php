<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sell extends Model
{
    protected $primaryKey = "sellId";

    protected $fillable = [
        "bookId",
        "userId",
        "sellDate",
        "deliveryType",
        "remaining",
        "low_stock",
    ];

    public function user()
    {
        return $this->belongsTo(User::class, "userId", "userId");
    }

    public function book()
    {
        return $this->belongsTo(Book::class, "bookId", "bookId");
    }
}
