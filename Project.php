<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Project extends Model
{
    protected $fillable = ['client_id', 'category_id', 'title', 'description', 'budget', 'attachment', 'attachment_name', 'hired_freelancer_id', 'status', 'completed_at'];

    protected $casts = [
        'budget' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function hiredFreelancer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hired_freelancer_id');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}
