<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'gig_id', 'gig_title', 'freelancer_name', 'quantity', 'price', 'line_total'];

    protected $casts = [
        'price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function gig(): BelongsTo
    {
        return $this->belongsTo(Gig::class);
    }
}
