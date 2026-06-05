<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apoyo extends Model
{
    protected $fillable = [
        'beneficiario_id',
        'tipo_apoyo',
        'descripcion',
        'fecha_apoyo',
        'monto',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_apoyo' => 'date',
        'monto'       => 'decimal:2',
    ];

    public function beneficiario()
    {
        return $this->belongsTo(Beneficiario::class);
    }
}