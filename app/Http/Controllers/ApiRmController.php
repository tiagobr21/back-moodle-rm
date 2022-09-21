<?php



namespace App\Http\Controllers;

use App\Htpp\Controllers\ApiMoodleController;
use Illuminate\Htpp\Request;  
use Illuminate\Support\Facades\Http;


class ApiRmController extends Controller{    
 
     public function index(){
        @set_time_limit(1800);
        error_reporting(1);
 
// CADASTRAR UM USUÁRIO DO RM NO MOODLE | VERIFICAR SE O USUÁRIO DO RM EXISTE NO MOODLE 

//Api Moodle
        $remotemoodle="localhost/moodle"; //MOODLE_URL - endereço do Moodle
        $url=$remotemoodle . '/webservice/restjson/server.php';
       
        //parametros a ser passado ao webservice
        $param_getUsers=array();
        $param_getUsers['wstoken']="b64eb1b5fb4ecdfe05014ddd679c7362"; //token de acesso ao webservice
        $param_getUsers['wsfunction']="core_user_get_users";
       
        //filtro de usuário
        $param_getUsers['criteria'][0]['key']='';
        $param_getUsers['criteria'][0]['value']='';
       
        //converter array para json
        $paramjson_get = json_encode( $param_getUsers);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjson_get);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result_getUser = curl_exec($ch);
         //$result =json_decode($result);
       
        $response_getUserss = json_decode( $result_getUser,true); 
         
        $response_getUsers = $response_getUserss['users'];
    
     /////////////////////////////////////////////////////////////////////////////////////////////////////

     //Api do RM
 
        $WsdlRM = ('https://teste.portaledu.com.br/TOTVSBusinessConnect/wsDataServer.asmx?wsdl');
       
        
        $soapParams = array(
            "login" => "moodle_rm",
            "password" => "Bondade23!",
            "authentication" => SOAP_AUTHENTICATION_BASIC,
            "trace" => 1,
            "exceptions" => 0
        );
        //EduConsultaRapidaAlunoSecretariaData
        $RealizarConsultaSql = array(
            "DataServerName"=>"EduConsultaRapidaAlunoSecretariaData",
            "Filtro"=>"1=1",
            "Contexto"=>"CODCOLIGADA=1",
            "Usuario"=>"moodle_rm",
            "Senha"=>"Bondade23!"
        );
        
        $client = new \SoapClient($WsdlRM, $soapParams);
        $result = $client->ReadViewAuth($RealizarConsultaSql);
        $code_response = http_response_code();
         

        $result_rm = json_decode(json_encode($result),true);
        $separaAlunos = implode('-', $result_rm);
        $aluno = explode(", ",$separaAlunos);
      
        $alunos = str_replace("\n","",$aluno);
        
        
         $string = <<<XML
         <?xml version='1.0' encoding='utf-8'?>$alunos[0]
         XML;
         $xml = simplexml_load_string($string);
        

        $json = json_encode($xml,JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); // converte a string XML  para JSON 
    

        $response_rm = json_decode($json,true);
       
        $response_rms = $response_rm['SCONSULTARAPIDAALUNOSECRETARIA'];
        ;

        exit;
        /*    echo '<pre>';
            var_dump( $response_rms);
            echo '<pre>';
            exit;     
  */
        /////////////////////////////////////////////////////////////////////////

        $novos = '';

     
        foreach($response_rms as $value_rm){
            
            $RA = $value_rm["RA"];

            $columns = array_column($response_getUsers,"username");

             $key = array_search($RA,$columns);
            

            if($key){
                echo('O usuário '.$RA.' já existe');
                echo '<br>';
            
            }else{
                 
                $novos .= $RA.' ';
                
                $param_createUser=array();
                $param_createUser['wstoken']="b64eb1b5fb4ecdfe05014ddd679c7362"; //token de acesso ao webservice
                $param_createUser['wsfunction']="core_user_create_users";
                        
                // $value_rm = $response_rms[$key];
                //filtro de usuário
                $param_createUser['users'][0]['createpassword']='1';
                $param_createUser['users'][0]['username']=$value_rm["RA"];
                $nome_completo = $param_createUser['users'][0]['firstname']= $value_rm["NOME"];
                
                $sobrenome_nome = explode(" ",$nome_completo);
                $nome = $sobrenome_nome[0];
                
                $primiro_sobrenome =  $sobrenome_nome[1];
                $segundo_sobrenome =  $sobrenome_nome[2];
                $terceiro_sobrenome = $sobrenome_nome[3];
                $quarto_sobrenome =   $sobrenome_nome[4]; 
                $quinto_sobrenome =   $sobrenome_nome[5]; 
                $sexto_sobrenome =    $sobrenome_nome[6];
                $setimo_sobrenome =   $sobrenome_nome[7];

                $sobrenome =  $primiro_sobrenome.' '.$segundo_sobrenome.' '.$terceiro_sobrenome.' '.$quarto_sobrenome.' '.$quinto_sobrenome.' '. $sexto_sobrenome.' '.$setimo_sobrenome;
                 
                $param_createUser['users'][0]['firstname']= $nome;
                $param_createUser['users'][0]['lastname']= $sobrenome;
               
                
                if($value_rm["EMAIL"] == null){
                    $param_createUser['users'][0]['email']=$value_rm["RA"].'@default.com.br';
                }else{
                    $param_createUser['users'][0]['email']=$value_rm["EMAIL"];
                }
                            
                //converter array para json
                $paramjson_create = json_encode($param_createUser);
                        
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 0);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjson_create);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result_createUser= curl_exec($ch);
                    //$result =json_decode($result);
                        
                $response_createUser = json_decode($result_createUser,true);  
                
                echo('O usuário '.$RA.' criado ');
                echo '<br>';
            }    
        }   
    }
}



  


 


