<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Earning extends Model
{
    protected $fillable = ['freelancer_id', 'project_id', 'amount', 'status'];

    protected $casts = ['amount' => 'decimal:2'];
}
