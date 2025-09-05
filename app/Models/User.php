<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Documento;
use App\Models\Unidade;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property \App\Models\Unidade $unidade
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Documento[] $documentos
 */
class User extends Authenticatable
{
    use Notifiable;

    public const TIPO_ADMIN = 'admin';
    public const TIPO_ASSESSOR = 'assessor';
    public const TIPO_GESTOR= 'gestor';
    public const TIPO_COLABORADOR = 'colaborador';
    public const TIPO_EXTRATOR = 'extrator';

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','tipo','created_at','updated_at', 'cpf'
    ];

    public $timestamps = true;


    public function isAdmin(){
        return $this->tipo == User::TIPO_ADMIN || $this->tipo == 'administrador(a)';
    }

    public function isGestor(){
        return $this->tipo == User::TIPO_GESTOR;
    }

    public function isColaborador(){
        return $this->tipo == User::TIPO_COLABORADOR;
    }

    public function isConselho(){
        return $this->isGestor() || $this->isColaborador();
    }

    public function isAssessor(){
        return $this->tipo == User::TIPO_ASSESSOR;
    }

    public function isExtrator(){
        return $this->tipo == User::TIPO_EXTRATOR;
    }

    public function isResponsavel(){
        return $this->id == $this->unidade->responsavel_id;
    }


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the documents associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documentos(){
        return $this->hasMany(Documento::class);
    }

    /**
     * Get the unidade that the user belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unidade() {
		return $this->belongsTo(Unidade::class,'unidade_id')->withTrashed();
    }

    public function firstName(){
        return explode(" ",$this->name)[0];
    }
}
