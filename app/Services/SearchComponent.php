<?php

namespace App\Services;

use App\Models\Consulta;
use App\Models\Download;
use Illuminate\Http\Request;

class SearchComponent{
    public static function logging($termos, $request){

        $consulta = new Consulta();

        $ip = $request->ip();
        $dataIp = \Location::get($ip);
        
        if( $dataIp ){
            $consulta->fill($dataIp);
        }
        
        $consulta->termos = $termos;
        $consulta->save();
    }

    public static function loggingDownload($request, $documento){
        if($documento){
            $download = new Download();
            $ip = $request->ip();
            
            $dataIp = \Location::get($ip);
        
            if( $dataIp ){
                $download->fill($dataIp);
            }

            $download->documento()->associate($documento);
            $download->save();
        }
    }

    public function likeDocumentos($documento){

    }

    public function aggResults($queryArray){

    }

    public function search(){
        
    }

    
}