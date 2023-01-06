<?php



namespace App\Http\Controllers;

use App\Htpp\Controllers\ApiMoodleController;
use Illuminate\Htpp\Request;  
use Illuminate\Support\Facades\Http;


class ApiRmController extends Controller{    
 
     public function cadastrar(){

       header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
        @set_time_limit(1800);
        error_reporting(1);
 

        $remotemoodle="http://localhost:9090/moodle"; 
        $url=$remotemoodle . '/webservice/restjson/server.php?';
       
        $param =array();
        $param ['wstoken']="408f6663a10bc617b89e1c07a65322e0";
        $param ['wsfunction']="core_user_get_users";
      
        $param ['criteria'][0]['key']='';
        $param ['criteria'][0]['value']='';
       
  
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



     /////////////////////////////////////////////////////////////////////////////////////////////////////

     //Api do RM
 
        $WSDL= ('https://h-tbc.fametro.edu.br/wsdataserver/MEX?wsdl');
 
        $ParametrosAutenticarSoap = array(
            'cache_wsdl' => WSDL_CACHE_NONE,
            'soap_version' => SOAP_1_1,
            'style' => SOAP_RPC,
            'use' => SOAP_ENCODED,
            'login' => 'diploma',
            'password' => 'F@m3tr022',
            'authentication' => SOAP_AUTHENTICATION_BASIC,
            'trace' => 1,
            'exceptions' => 1,
            'stream_context' => stream_context_create(array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'crypto_method' => STREAM_CRYPTO_METHOD_TLS_CLIENT,
            )
            ))
        );

        $RealizarConsultaSql = array(
            "DataServerName"=>"EduConsultaRapidaAlunoSecretariaData",
            "Filtro"=>"1=1",
            "Contexto"=>"CODCOLIGADA=1",
    
        );
        
  
        $ClientSoap = new \SoapClient($WSDL, $ParametrosAutenticarSoap);
       
        $result = $ClientSoap->ReadView($RealizarConsultaSql);
   
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

        $response_rm = $response_rm['SCONSULTARAPIDAALUNOSECRETARIA'];
      

        /////////////////////////////////////////////////////////////////////////

          $novos = '';
          $nomerm =[];
        
          foreach ($response_rm as $value_rm){
            
            array_push($nomerm,$value_rm["NOME"]);
            $nomemdl = array_column($response_mdl,"fullname");
        
            $RA = $value_rm["RA"];

            $username = array_column($response_mdl,"username");

            $key = array_search($RA,$username);

            if($key){
       
                
            }else{
                 
                $novos .= $RA.' ';
                
                $param_createUser=array();
                $param_createUser['wstoken']="408f6663a10bc617b89e1c07a65322e0"; //token de acesso ao webservice
                $param_createUser['wsfunction']="core_user_create_users";
                        
        
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
                            
             
                $paramjson_create = json_encode($param_createUser);
                        
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 0);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjson_create);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result_createUser= curl_exec($ch);
                
               
                $results[]= [];
                $errors[]=[];
                $response_err=[];
            
                array_push($results,$result_createUser);

                 unset($results['users']);
                unset($results['warnings']);
                 
              
            }    
        }    
            
            $response['logs'] = [];
            
            date_default_timezone_set('America/Manaus');

            $hoje ='atualizacao-'.date('d-m-Y H;i');

            $file = fopen('C:/Users/tiago.souza/Documents/GitHub/moodle-rm/app/Http/Controllers/logs/'.$hoje.'.txt','w');
            foreach ( $results as $key => $value) {
                $value = str_replace('"exception":"invalid_parameter_exception","errorcode":"invalidparameter","message":"Valor inv\u00e1lido de par\u00e2metro detectado","debuginfo":"Email address is invalid:', "Email Inválido:", $value);
                $value = str_replace(']', "", $value); 
                $value = str_replace('[', "Usuário criado:", $value); 
                array_push($response['logs'],$value);

                fwrite($file, $value . PHP_EOL);
            }
            fclose($file); 

            // header("Refresh: 100");
        
          return $response;
             
    }


    public function consultar(){
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
        @set_time_limit(1800);
        error_reporting(1);
 
        $remotemoodle="http://localhost:9090/moodle"; 
        $url=$remotemoodle . '/webservice/restjson/server.php?';
       

        $param =array();
        $param ['wstoken']="408f6663a10bc617b89e1c07a65322e0"; 
        $param ['wsfunction']="core_user_get_users";
       
        $param ['criteria'][0]['key']='';
        $param ['criteria'][0]['value']='';
       
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
 


public function criarcurso(){

    header("Access-Control-Allow-Origin: *");
     header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
     @set_time_limit(1800);
     error_reporting(1);


     $remotemoodle="http://localhost:9090/moodle"; 
     $url=$remotemoodle . '/webservice/restjson/server.php?';
    
     $param =array();
     $param ['wstoken']="408f6663a10bc617b89e1c07a65322e0";
     $param ['wsfunction']="core_course_get_courses";
   
     $param ['options']['ids'][0]= '*';
   
     $paramjson = json_encode( $param );
    
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_POST, 0);
     curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjson);
     curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     $result = curl_exec($ch);
      
     $cursosmdl = json_decode( $result,true); 

     //////////////////////////////////////////////////////////////////////////////
     
     $WSDL= ('https://h-tbc.fametro.edu.br/wsdataserver/MEX?wsdl');
 
     $ParametrosAutenticarSoap = array(
         'cache_wsdl' => WSDL_CACHE_NONE,
         'soap_version' => SOAP_1_1,
         'style' => SOAP_RPC,
         'use' => SOAP_ENCODED,
         'login' => 'diploma',
         'password' => 'F@m3tr022',
         'authentication' => SOAP_AUTHENTICATION_BASIC,
         'trace' => 1,
         'exceptions' => 1,
         'stream_context' => stream_context_create(array(
         'ssl' => array(
             'verify_peer' => false,
             'verify_peer_name' => false,
             'crypto_method' => STREAM_CRYPTO_METHOD_TLS_CLIENT,
         )
         ))
     );

     $RealizarConsultaSql = array(
         "DataServerName"=>"EduCursoData",
         "Filtro"=>"1=1",
         "Contexto"=>"CODCOLIGADA=1",
 
     );
     
     $ClientSoap = new \SoapClient($WSDL, $ParametrosAutenticarSoap);
    
     $result = $ClientSoap->ReadView($RealizarConsultaSql);

     $result_rm = json_decode(json_encode($result),true);

     $separaCursos = implode('-', $result_rm);
             
     $string = <<<XML
     <?xml version='1.0' encoding='utf-8'?>$separaCursos
     XML;
    
     $xml = simplexml_load_string($string);
    
     $json = json_encode($xml,JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); // converte a string XML  para JSON 
    
     $response_rm = json_decode($json,true);
  
     $response_rm = $response_rm['SCurso'];
     

     /////////////////////////////////////////////////
        $nomecursomdl = array_column($cursosmdl,"fullname");
         
        dd($nomecursomdl);
        exit;
     foreach ($response_rm as $value_rm){
            
     
        $RA = $value_rm["RA"];

        $username = array_column($response_mdl,"username");

        $key = array_search($RA,$username);

        if($key){
   
            
        }else{
             
            $novos .= $RA.' ';
            
            $param_createUser=array();
            $param_createUser['wstoken']="408f6663a10bc617b89e1c07a65322e0"; //token de acesso ao webservice
            $param_createUser['wsfunction']="core_user_create_users";
                    
    
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
                        
         
            $paramjson_create = json_encode($param_createUser);
                    
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjson_create);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result_createUser= curl_exec($ch);
            
           
            $results[]= [];
            $errors[]=[];
            $response_err=[];
        
            array_push($results,$result_createUser);

             unset($results['users']);
            unset($results['warnings']);
             
          
        }    
    }    

    }
}



  


 


