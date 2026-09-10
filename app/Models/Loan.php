<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $primaryKey = "loanId";

    protected $fillable = [
        "bookId",
        "userId",
        "loanDate",
        "endReturnDate",
        "returnDate",
        "status",
        "deliveryType",
        "returnType",
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
