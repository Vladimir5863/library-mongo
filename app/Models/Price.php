<?php
namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Price extends Model
{
    protected $fillable = ["startDate", "endDate", "price"];
}
