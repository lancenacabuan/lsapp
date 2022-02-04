<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prepare extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = "prepared_items";
}
