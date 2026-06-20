<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gig extends Model
{
    protected $fillable = ['freelancer_id', 'title', 'description', 'price', 'delivery_days', 'status'];

    protected $casts = ['price' => 'decimal:2'];

    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }
}
