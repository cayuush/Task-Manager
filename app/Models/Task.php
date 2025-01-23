<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    //fields
    protected $fillable = ['title', 'completed', 'external_id'];
}
