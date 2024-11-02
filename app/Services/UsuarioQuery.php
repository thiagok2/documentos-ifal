<?php

namespace App\Services;

use App\Models\Unidade;
use Illuminate\Support\Facades\DB;



class UsuarioQuery{
    
    public function countAcessos30Dias(){
        $sql = "SELECT count(*) as total from users
        INNER JOIN unidades ON unidades.id = users.unidade_id
        where (CURRENT_DATE - DATE(ultimo_acesso_em)) between 0 and 30";

    if( auth()->user()->isAssessor()){
        $unidade = Unidade::find(auth()->user()->unidade_id);
        $sql = $sql." and un.estado_id = ".$unidade->estado_id;
    }
        
        $result = DB::select(
            $sql
        );
    
        return $result[0]->total;
    }

    public function countConsultaMes(){
        $sql = "SELECT * FROM
        (SELECT
            upper(termos) termo, 
            to_char(data_consulta, 'MM/YYYY') data_label, 
            to_char(data_consulta, 'YYYY/MM') data_order, 
            count(*) quantidade,
            ROW_NUMBER() OVER (PARTITION BY to_char(data_consulta, 'YYYY/MM') ORDER BY count(*)) AS ranking
            FROM consultas 
            WHERE termos is not null and trim(termos) != '' 
                     and DATE_PART('day', CURRENT_DATE - data_consulta) < 90
        GROUP BY to_char(data_consulta, 'MM/YYYY'),to_char(data_consulta, 'YYYY/MM'), upper(termos)
        HAVING count(*) > 200
        ORDER BY to_char(data_consulta, 'YYYY/MM') desc, quantidade desc
        ) as t WHERE ranking <= 20;
        ";

        $result = DB::select(
            $sql
        );

        return $result;

    }
}