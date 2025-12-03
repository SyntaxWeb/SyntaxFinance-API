<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Divida extends Model
{
    use HasFactory;

    protected $table = 'dividas';

    protected $fillable = [
        'user_id',
        'mes',
        'valor',
        'motivo',
        'categoria',
        'data',
        'status',
        'cartao_id',
        'parcelamento_id',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cartao(): BelongsTo
    {
        return $this->belongsTo(Cartao::class, 'cartao_id');
    }

    public function parcelamento(): BelongsTo
    {
        return $this->belongsTo(Parcelamento::class, 'parcelamento_id');
    }
}
