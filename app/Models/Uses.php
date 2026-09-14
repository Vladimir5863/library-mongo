<?php
namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Uses extends Model
{
    protected $connection = "mongodb";

    protected $table = "uses";

    protected $fillable = [
        "userId",
        "bookId",
        "type",
        "points",
        "date",
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
