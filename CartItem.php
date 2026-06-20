<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = ['user_id', 'gig_id', 'gig_title', 'freelancer_name', 'unit_price', 'quantity', 'line_total'];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function gig(): BelongsTo
    {
        return $this->belongsTo(Gig::class);
    }
}
