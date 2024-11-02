<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Documento;
use Elasticsearch\ClientBuilder;

class EnvController extends Controller
{

    /**
     * @var \Elasticsearch\Client
     */
    private $client;

    public function __construct(){

        $hosts = [        
            getenv('ELASTIC_URL')
        ];
        $this->client = ClientBuilder::create()->setHosts($hosts)->build();
    }

    
    public function getenv(){
        $APP_DEBUG = getenv('APP_DEBUG');

        $URL_BASE = getenv('URL_BASE');
        $URL_UNIDADE_API = getenv('URL_UNIDADE_API');
        $DIR_UPLOAD = getenv('DIR_UPLOAD');
        $PDI_ROOT = getenv('PDI_ROOT');
        $ETL_ROOT = getenv('ETL_ROOT');

        $APP_ENV = getenv('APP_ENV');
        $APP_URL = getenv('APP_URL');
        $ELASTIC_URL = getenv('ELASTIC_URL');
        $DATABASE_URL = getenv('DATABASE_URL');
        $ETL_DIR = getenv('ETL_DIR');

        $STORAGE_PATH = Storage::disk('public')->path('uploads');
        $STORAGE_PATH_EXISTS = is_dir( $STORAGE_PATH );
        $STORAGE_PATH_PERMISSION = substr(sprintf('%o', fileperms( $STORAGE_PATH )), -4);

        $MAIL_USERNAME = getenv('MAIL_USERNAME');
        $MAIL_PASSWORD = getenv('MAIL_PASSWORD');

        $MAIL_DRIVER=getenv('MAIL_DRIVER');
        $MAIL_HOST=getenv('MAIL_HOST');
        $MAIL_PORT=getenv('MAIL_PORT');
        $MAIL_ENCRYPTION=getenv('MAIL_ENCRYPTION');

        try{
            Log::warning('testando acesso ao log');
            $LOG_STATUS = "OK";
        }catch(\Exception $e){
            $LOG_STATUS = "FALHA: ".$e->getMessage();
        }

        #Testar escrita no diretório
        try{
            Storage::delete('uploads/test-env-001.pdf');
            if(Storage::copy('tests/test-env-001.pdf', 'uploads/test-env-001.pdf'))
                $RESULT_MOVE = "OK";
        }catch(\Exception $e){
            $RESULT_MOVE = "FALHA: ".$e->getMessage();
        }

        try{
            $documento = Documento::where('completed',true)->first();
            
            if(isset($documento)){
                $bodyDocumentElastic = $documento->toElasticObject();
                $arquivoData = Storage::get('uploads/'.$documento->arquivo);
                $bodyDocumentElastic["data"] = base64_encode($arquivoData);
                $params = [
                    'index' => 'documentos_ifal',
                    'type'  => '_doc',
                    'id'    => $documento->arquivo,
                    'pipeline' => 'attachment', 
                    'body'  => $bodyDocumentElastic
                    
                ];
                $result = $this->client->index($params);
                if($result['result'] == 'created' || $result['result'] == 'updated'){
                    $ELASTIC_STATUS = "OK";
                }else{
                    //dd($result);   
                    $ELASTIC_STATUS = "FALHA. ".$result;
                }
            } else{
                $result = "NENHUM DOCUMENTO PRESENTE NO BANCO";
                //dd($result);   
                $ELASTIC_STATUS = "NAO FOI POSSIVEL TESTAR. ".$result;
            }
            
        }catch(\Exception $e){
            $ELASTIC_STATUS = "FALHA. ".$e->getMessage();
        }

        return view('admin.env', compact('APP_DEBUG','DIR_UPLOAD','URL_BASE','URL_UNIDADE_API','PDI_ROOT','ETL_ROOT',
        'APP_ENV','APP_URL','ELASTIC_URL','ETL_DIR'
            ,'MAIL_USERNAME','MAIL_PASSWORD','DATABASE_URL','STORAGE_PATH',
            'MAIL_DRIVER','MAIL_HOST','MAIL_PORT','MAIL_ENCRYPTION',
            'LOG_STATUS', 'STORAGE_PATH_EXISTS', 'STORAGE_PATH_PERMISSION','RESULT_MOVE', 'ELASTIC_STATUS'));
    }
}
