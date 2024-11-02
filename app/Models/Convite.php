<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\User;
use Mail;

class Convite extends Model
{

    protected $fillable = [
        'contato', 'telefone', 'email','destinatario','mensagem'
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function user(){
        return $this->hasOne(User::class);
    }

    public function enviarNovoUsuario($userNovo,$passwordGerado){
        $to_name = $userNovo->name;
        
        if(getenv('APP_ENV') == 'local'){
            $to_email = getenv('MAIL_USERNAME');
        }else {
            $to_email = $userNovo->email;
        }

        $tipoLabel = "";
        if($userNovo->isAdmin()){
            $tipoLabel = "ADMINISTRADOR(A)";
        }else if($userNovo->isGestor()){
            $tipoLabel  = "GESTOR(A)";
        }else if($userNovo->isColaborador()){
            $tipoLabel  = "COLABORADOR(A)";
        }else if($userNovo->isAssessor()){
            $tipoLabel  = "ASSESSOR(A)";
        }

        DB::beginTransaction(); 
        $userNovo->convidado_em = date("Y-m-d H:i:s");
        $userNovo->save();
        DB::commit();
        
        $data = array(
            "name"      =>  $userNovo->name, 
            "password"  =>  $passwordGerado,
            "unidade"   =>  $userNovo->unidade->nome,
            "tipo"      =>  $tipoLabel,
            "email"     =>  $userNovo->email
        );

        Mail::send('emails.acesso', $data, function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)
                ->subject('Acesso a plataforma documentos_ifal');
                $message->from('normativas@nees.com.br','Normativas - NEES');
        });
    }


}
