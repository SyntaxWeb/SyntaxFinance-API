<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cartao extends Model
{
    use HasFactory;

    protected $table = 'cartoes';

    protected $fillable = [
        'user_id',
        'nome',
        'bandeira',
        'limite',
        'dia_fechamento',
        'dia_vencimento',
    ];

    protected $casts = [
        'limite' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parcelamentos(): HasMany
    {
        return $this->hasMany(Parcelamento::class, 'cartao_id');
    }

    public function dividas(): HasMany
    {
        return $this->hasMany(Divida::class, 'cartao_id');
    }
}
