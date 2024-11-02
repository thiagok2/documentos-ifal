<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Unidade extends Model
{

    public const TIPO_CONSELHO = 'Conselho';
    public const TIPO_ASSESSORIA = 'Assessoria';
    public const ESFERA_MUNICIPAL = 'Municipal';
    public const ESFERA_ESTADUAL = 'Estadual';
    public const ESFERA_FEDERAL = 'Federal';

    use SoftDeletes;

    public $cascadeDelete = [
        'hasMany' => Documento::class,
    ];

    public static function boot() {
        parent::boot();

        static::deleting(function($unidade) { 
             $unidade->documentos()->delete();
        });
    }

    protected $fillable = [
        'nome', 'tipo', 'esfera','sigla','url','email','contato','telefone','endereco','contato2','friendly_url','estado_id', 'municipio_id','user_id','responsavel_id'
    ];

    public $timestamps = true;

    public $rules = [
        'nome' => 'required|string|max:255',
        'tipo' => 'required|string|max:20',
        'esfera' => 'required|string|max:20',
        'sigla' => 'required|string|max:30',
        'url' => 'nullable|url',
        'friendly_url' => 'required',
        'email' => 'required',
        'telefone' => 'required|string|max:100'
    ];

    public $messages = [
        'required' => 'O campo :attribute é obrigatório',
        'nome.max' => 'O campo :attribute deve ter no máximo 255 caracteres',
        'sigla.max' => 'O campo :attribute deve ter no máximo 10 caracteres',
        'telefone.max' => 'O campo :attribute deve ter no máximo 100 caracteres',
    ];

    public function responsavel(){
        return $this->belongsTo(User::class,'responsavel_id')->withTrashed();
    }

    public function estado(){
        return $this->belongsTo(Estado::class,'estado_id');
    }

    public function municipio(){
        return $this->belongsTo(Municipio::class,'municipio_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function usuarios(){
        return $this->hasMany(User::class);
    }

    public function local(){
        if($this->esfera === Unidade::ESFERA_MUNICIPAL)
            return $this->municipio->nome;
        elseif($this->esfera === Unidade::ESFERA_ESTADUAL)
            return $this->estado->nome;
        else   
            return 'Brasil';
    }

    public function documentos(){
        return $this->hasMany(Documento::class);
    }

    // Lazy model documents
    public function documentosCount()
    {
    return $this->hasOne(Documento::class)
        ->selectRaw('unidade_id, count(*) as total')
        ->groupBy('unidade_id');
    }
    
    public function getDocumentosCountAttribute()
    {
    // if relation is not loaded already, let's do it first
    if ( ! array_key_exists('documentosCount', $this->relations)) 
        $this->load('documentosCount');
    
    $related = $this->getRelation('documentosCount');
    
    // then return the count directly
    return ($related) ? (int) $related->total : 0;
    }



    public function generateFriendlyUrl(){
        $friendlyUrl = Str::slug($this->nome, '-');
        return $friendlyUrl;
    }
}
