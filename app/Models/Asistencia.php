<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    protected $table = 'asistencias';

    protected $fillable = [
        'fecha',
        'beneficiario_id',
        'nombre_visitante',
        'presente',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
        'presente' => 'boolean',
    ];

    public function beneficiario()
    {
        return $this->belongsTo(Beneficiario::class);
    }

    public function getNombreAttribute(): string
    {
        return $this->beneficiario?->nombre_completo
            ?? $this->nombre_visitante
            ?? '—';
    }
}
