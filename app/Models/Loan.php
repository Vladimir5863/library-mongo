<?php
namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Loan extends Model
{
    protected $connection = "mongodb";
    protected $table = "loans";

    protected $fillable = [
        "bookId", "userId", "loanDate", "endReturnDate", "returnDate",
        "status", "deliveryType", "returnType", "remaining", "low_stock",
    ];

    public function user() { return $this->belongsTo(User::class, "userId", "_id"); }
    public function book() { return $this->belongsTo(Book::class, "bookId", "_id"); }
}
