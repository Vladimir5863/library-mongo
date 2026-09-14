<?php
namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class News extends Model
{
    protected $connection = "mongodb";

    protected $table = "news";
    protected $fillable = [
        "type",
        "logo",
        "title",
        "startDate",
        "endDate",
        "text",
        "multimedia",
    ];
}
