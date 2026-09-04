<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Enums\TransactionType;

class Transaction extends Model
{
    protected $fillable = [
        'account_id',
        'type',
        'amount',
    ];

    protected $casts = [
        'type' => TransactionType::class,
        'amount' => 'decimal:2',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function securityDetail(): HasOne
    {
        return $this->hasOne(SecurityTransactionDetail::class);
    }
}