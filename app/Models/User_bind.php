<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User_bind extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

  
   
}

