<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'pais','regiao', 'cidade', 'latitude', 'longitude'
    ];

    public function documento()
    {
        return $this->belongsTo(Documento::class);
    }
}
