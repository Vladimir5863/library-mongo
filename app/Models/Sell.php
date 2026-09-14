<?php
namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Sell extends Model
{
    protected $connection = "mongodb";

    protected $table = "sells";

    protected $fillable = [
        "bookId",
        "userId",
        "sellDate",
        "deliveryType",
        "price",
        "remaining",
        "low_stock",
    ];

    public function user()
    {
        return $this->belongsTo(User::class, "userId", "_id");
    }

    public function book()
    {
        return $this->belongsTo(Book::class, "bookId", "_id");
    }
}
