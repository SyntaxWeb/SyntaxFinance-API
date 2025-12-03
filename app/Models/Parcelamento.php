<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Parcelamento extends Model
{
    use HasFactory;

    protected $table = 'parcelamentos';

    protected $fillable = [
        'user_id',
        'cartao_id',
        'descricao',
        'valor_total',
        'numero_parcelas',
        'parcela_atual',
        'mes_inicio',
    ];

    protected $casts = [
        'valor_total' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cartao(): BelongsTo
    {
        return $this->belongsTo(Cartao::class, 'cartao_id');
    }

    public function dividas(): HasMany
    {
        return $this->hasMany(Divida::class, 'parcelamento_id');
    }
}
