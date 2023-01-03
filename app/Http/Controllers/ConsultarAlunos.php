<?php

namespace App\Http\Controllers;

use App\Htpp\Controllers\ApiMoodleController;
use Illuminate\Htpp\Request;  
use Illuminate\Support\Facades\Http;


class ConsultarAlunos extends Controller
{
 
    public function consultar(){
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
        @set_time_limit(1800);
        error_reporting(1);
 
// CADASTRAR UM USUÁRIO DO RM NO MOODLE | VERIFICAR SE O USUÁRIO DO RM EXISTE NO MOODLE 

//Api Moodle

        $remotemoodle="http://localhost:9090/moodle"; //MOODLE_URL - endereço do Moodle
        $url=$remotemoodle . '/webservice/restjson/server.php?';
       
        //parametros a ser passado ao webservice
        $param =array();
        $param ['wstoken']="408f6663a10bc617b89e1c07a65322e0"; //token de acesso ao webservice
        $param ['wsfunction']="core_user_get_users";
       
        //filtro de usuário
        $param ['criteria'][0]['key']='';
        $param ['criteria'][0]['value']='';
       
        //converter array para json
        $paramjson = json_encode( $param );
       
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjson);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
   
        $response = json_decode( $result,true); 
        
        $response_mdl = $response['users'];

        return $response_mdl; 
    }

    public function excluir($id){
   
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
        @set_time_limit(1800);
        error_reporting(1);

        $remotemoodle="http://localhost:9090/moodle"; 
        $url=$remotemoodle . '/webservice/restjson/server.php?';
       
        $param =array();
        $param ['wstoken']="408f6663a10bc617b89e1c07a65322e0"; 
        $param ['wsfunction']="core_user_delete_users";
       
    
        $param ['userids'][0]= $id;
    
        $paramjson = json_encode( $param );
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjson);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        
        $response = json_decode( $result,true); 
         
        if($response == null){
           return response()->json('Aluno deletado com sucesso',200);
        }else{
            return response()->json('Aluno nao encontrado',200);
        }
}
}