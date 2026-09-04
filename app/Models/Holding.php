<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Holding extends Model
{
    protected $fillable = [
        'account_id',
        'ticker',
        'quantity',
        'current_price',
        'current_value',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'current_price' => 'decimal:2',
        'current_value' => 'decimal:2',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}