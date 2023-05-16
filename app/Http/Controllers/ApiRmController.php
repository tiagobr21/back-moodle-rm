<?php



namespace App\Http\Controllers;

use App\Htpp\Controllers\ApiMoodleController;
use Illuminate\Htpp\Request;  
use Illuminate\Support\Facades\Http;




class ApiRmController extends Controller{    
 



// Main Functions

public function criaralunos(){
     
    @set_time_limit(2000);

     //Api do RM
      
     $url = 'https://h-tbc.fametro.edu.br/api/framework/v1/consultaSQLServer/RealizaConsulta/alunos/0/S';
     
     $authHeaders = array(
        'login' => 'thiago.souzaa',
        'password' => 'Bondade07!'
     );
     $authHeaders[] = 'Authorization:Basic dGhpYWdvLnNvdXphYTpCb25kYWRlMDch';
        
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_POST, 0);
     curl_setopt($ch, CURLOPT_HTTPHEADER, $authHeaders);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     $result = curl_exec($ch);

     $response_rm = json_decode($result,true);


        /////////////////////////////////////////////////////////////////////////

          foreach ($response_rm as $value_rm){
               
      
                $remotemoodle="http://localhost:9191/moodle"; 
                $url=$remotemoodle . '/webservice/restjson/server.php?';

                $param_createUser=array();
                $param_createUser['wstoken']="9649edb002bfda533e816259da2a4836"; //token de acesso ao webservice
                $param_createUser['wsfunction']="core_user_create_users";
               
                
                $CPF =  substr($value_rm['CPF'],0,6);
                $param_createUser['users'][0]['password'] = $CPF;
                $param_createUser['users'][0]['username']=$value_rm["RA"];
                $nome_completo = $value_rm["NOMEALUNO"];
                
                $sobrenome_nome = explode(" ",$nome_completo);
                $nome = $sobrenome_nome[0];
                unset($sobrenome_nome[0]);
                

                $sobrenome = implode(" ",$sobrenome_nome);

            
                $param_createUser['users'][0]['firstname']= $nome;
                $param_createUser['users'][0]['lastname']= $sobrenome;
                   
                   if(empty($value_rm["EMAIL"])){
                    $param_createUser['users'][0]['email']=$value_rm["RA"].'@default.com.br';
                   }else{
                        $param_createUser['users'][0]['email']= trim($value_rm["EMAIL"]);
                    }
                  
            
                $paramjson_create = json_encode($param_createUser);
                        
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 0);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjson_create);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result_createUser= curl_exec($ch);


                $resultsalunos []= [];
                $errors[]=[];
                $response_err=[];
            
                array_push($resultsalunos,$result_createUser); 

              
            }    

            $response['logs'] = [];
         
            date_default_timezone_set('America/Manaus');

            $hoje ='atualizacao-'.date('d-m-Y H;i');

            $file = fopen('C:/Users/tiago.souza/Documents/GitHub/moodle-rm/app/Http/Controllers/logs-alunos/'.$hoje.'.txt','w');
            foreach ( $resultsalunos as $value) {
         
                $value = str_replace('exception":"invalid_parameter_exception","errorcode":"invalidparameter","message":"Valor inv\u00e1lido de par\u00e2metro detectado","debuginfo":"Username already exists:', "Usuário Já Existe:", $value);
                $value = str_replace('exception":"invalid_parameter_exception","errorcode":"invalidparameter","message":"Valor inv\u00e1lido de par\u00e2metro detectado","debuginfo":"Email address is invalid:', "Email Inválido:", $value);
                $value = str_replace(']', "", $value); 
                $value = str_replace('[', "Usuário criado:", $value); 
                
                array_push($response['logs'],$value);
                 
                if(gettype($value) == 'string'){
                   fwrite($file, $value . PHP_EOL); 
                }
                
            }
           fclose($file); 

          http_response_code(200);
            
          return $response;    
             
    }


    public function consultaralunos(){
        
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
        @set_time_limit(1800);
        error_reporting(1);
 
        $remotemoodle="http://localhost:9191/moodle"; 
        $url=$remotemoodle . '/webservice/restjson/server.php?';
       

        $param =array();
        $param['wstoken']="9649edb002bfda533e816259da2a4836"; 
        $param['wsfunction']="core_user_get_users";
       
        $param['criteria'][0]['key']='';
        $param['criteria'][0]['value']='';
        
        $paramjson = json_encode( $param );
       
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjson);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        
      
        return $result; 
    }


    public function excluir($id){
   
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
        @set_time_limit(1800);
        error_reporting(1);

        $remotemoodle="http://localhost:9191/moodle"; 
        $url=$remotemoodle . '/webservice/restjson/server.php?';
       
        $param =array();
        $param ['wstoken']="9649edb002bfda533e816259da2a4836"; 
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
 


public function criarcursos(){

     header("Access-Control-Allow-Origin: *");
     header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
     @set_time_limit(3000);
     error_reporting(1);
      
     $url = 'https://h-tbc.fametro.edu.br/api/framework/v1/consultaSQLServer/RealizaConsulta/disciplinas/0/S';
     
     $authHeaders = array(
        'login' => 'thiago.souzaa',
        'password' => 'Bondade07!'
     );
     $authHeaders[] = 'Authorization:Basic ZGlwbG9tYTpGQG0zdHIwMjI=';
        
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_POST, 0);
     curl_setopt($ch, CURLOPT_HTTPHEADER, $authHeaders);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     $result = curl_exec($ch);

     $response = json_decode($result,true);
   
 
     //Criar Categorias
        
        //COLIGADA

   foreach ($response as $key => $value) {

        
        //url
        $remotemoodle="http://localhost:9191/moodle"; 
        $url=$remotemoodle . '/webservice/restjson/server.php?';

        $paramcolig = array();
        $paramcolig['wstoken']="9649edb002bfda533e816259da2a4836"; //token de acesso ao webservice
        $paramcolig['wsfunction']="core_course_create_categories";
        
        $paramcolig ['categories'][0]['idnumber']= 'C'.$value['CODCOLIGADA'];
        $paramcolig ['categories'][0]['name']= $value['COLIGADA'];
    
        $paramjsoncolig = json_encode($paramcolig);
                
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjsoncolig);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resultcolig = curl_exec($ch);
        $resultcolig = json_decode($resultcolig,true);  

 
       //FILIAL

        $paramfilial = array();
        $paramfilial['wstoken']="9649edb002bfda533e816259da2a4836"; //token de acesso ao webservice
        $paramfilial['wsfunction']="core_course_create_categories";
        
        $paramfilial ['categories'][0]['idnumber']='C'.$value['CODCOLIGADA'].'F'.$value['CODFILIAL'];
        $paramfilial ['categories'][0]['name']= $value['NOMEFILIAL']; 

           // Consultar Categorias Coligada
                
             $paramconsultafil = array();
             $paramconsultafil['wstoken']="9649edb002bfda533e816259da2a4836"; //token de acesso ao webservice
             $paramconsultafil['wsfunction']="core_course_get_categories";
             
             $paramconsultafil['criteria'][0]['key']= 'idnumber';
             $paramconsultafil['criteria'][0]['value']= 'C'.$value['CODCOLIGADA'];
     
             $paramjsonconsultafil = json_encode($paramconsultafil);
                     
             $ch = curl_init();
             curl_setopt($ch, CURLOPT_URL, $url);
             curl_setopt($ch, CURLOPT_POST, 0);
             curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjsonconsultafil);
             curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
             $resultconsultafil = curl_exec($ch);
             $consultafil =  json_decode($resultconsultafil,true);


             foreach( $consultafil as $idnumber){

                if($idnumber['idnumber'] == 'C'.$value['CODCOLIGADA']){
                   $idcolig = $idnumber['id'];
                }
             }
     
        $paramfilial['categories'][0]['parent']= $idcolig;

        $paramjsonfilial = json_encode($paramfilial);
                
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjsonfilial);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resultfilial = curl_exec($ch);
        $resultfilial = json_decode($resultfilial,true); 
         
   
        // NIVEL DE ENSINO

        $paramensino = array();
        $paramensino['wstoken']="9649edb002bfda533e816259da2a4836"; //token de acesso ao webservice
        $paramensino['wsfunction']="core_course_create_categories";
        
        $paramensino ['categories'][0]['idnumber']='C'.$value['CODCOLIGADA'].'F'.$value['CODFILIAL'].'E'.$value['CODNIVEL'];
        $paramensino ['categories'][0]['name']= $value['NIVEL_ENSINO']; 

           // Consultar Categorias Filial
                
             $paramconsultaens = array();
             $paramconsultaens['wstoken']="9649edb002bfda533e816259da2a4836"; //token de acesso ao webservice
             $paramconsultaens['wsfunction']="core_course_get_categories";
             
             $paramconsultaens['criteria'][0]['key']= 'idnumber';
             $paramconsultaens['criteria'][0]['value']= 'C'.$value['CODCOLIGADA'].'F'.$value['CODFILIAL'];
     
             $paramjsonconsultaens = json_encode($paramconsultaens);
                     
             $ch = curl_init();
             curl_setopt($ch, CURLOPT_URL, $url);
             curl_setopt($ch, CURLOPT_POST, 0);
             curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjsonconsultaens);
             curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
             $resultconsultaens = curl_exec($ch);
             $consultaens =  json_decode($resultconsultaens,true);

             foreach( $consultaens as $idnumber){

                if($idnumber['idnumber'] == 'C'.$value['CODCOLIGADA'].'F'.$value['CODFILIAL']){
                   $idfilial = $idnumber['id'];
                }
             }
     
         
        $paramensino['categories'][0]['parent']= $idfilial;

        $paramjsonensino = json_encode($paramensino);
                
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjsonensino);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resultensino = curl_exec($ch);
        $resultensino = json_decode($resultensino,true);   

        
        // CURSO
 
        $paramperiodo = array();
        $paramperiodo['wstoken']="9649edb002bfda533e816259da2a4836"; //token de acesso ao webservice
        $paramperiodo['wsfunction']="core_course_create_categories";
        
        $paramperiodo ['categories'][0]['idnumber']='C'.$value['CODCOLIGADA'].'F'.$value['CODFILIAL'].'E'.$value['CODNIVEL'].'-'.$value['CODCURSO'];
        $paramperiodo ['categories'][0]['name']= $value['CURSO']; 

           // Consultar Categorias Filial
                
             $paramconsultcurso = array();
             $paramconsultcurso['wstoken']="9649edb002bfda533e816259da2a4836"; //token de acesso ao webservice
             $paramconsultcurso['wsfunction']="core_course_get_categories";
             
             $paramconsultcurso['criteria'][0]['key']= 'idnumber';
             $paramconsultcurso['criteria'][0]['value']= 'C'.$value['CODCOLIGADA'].'F'.$value['CODFILIAL'].'E'.$value['CODNIVEL'];
     
             $paramjsonconsultcurso = json_encode($paramconsultcurso);
                     
             $ch = curl_init();
             curl_setopt($ch, CURLOPT_URL, $url);
             curl_setopt($ch, CURLOPT_POST, 0);
             curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjsonconsultcurso);
             curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
             $resultconsultaperiodo = curl_exec($ch);
             $consultacurso =  json_decode($resultconsultaperiodo,true);
             
       

             foreach( $consultacurso as $idnumber){

                if($idnumber['idnumber'] == 'C'.$value['CODCOLIGADA'].'F'.$value['CODFILIAL'].'E'.$value['CODNIVEL']){
                   $idensino = $idnumber['id'];
                }
             }
     
        $paramperiodo['categories'][0]['parent']= $idensino;

        $paramjsoncurso = json_encode($paramperiodo);
                
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjsoncurso);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resultcurso = curl_exec($ch);
        $resultcurso = json_decode($resultcurso,true);  



        // PERÍODO LETÍVO

        $paramperiodo = array();
        $paramperiodo['wstoken']="9649edb002bfda533e816259da2a4836"; //token de acesso ao webservice
        $paramperiodo['wsfunction']="core_course_create_categories";
        
        $paramperiodo ['categories'][0]['idnumber']='C'.$value['CODCOLIGADA'].'F'.$value['CODFILIAL'].'E'.$value['CODNIVEL'].'-'.$value['CODCURSO'].'-'.$value['PERIODO'];
        $paramperiodo ['categories'][0]['name']= $value['PERIODO']; 

           // Consultar Categorias Filial
                
             $paramconsultperiodo = array();
             $paramconsultperiodo['wstoken']="9649edb002bfda533e816259da2a4836"; //token de acesso ao webservice
             $paramconsultperiodo['wsfunction']="core_course_get_categories";
             
             $paramconsultperiodo['criteria'][0]['key']= 'idnumber';
             $paramconsultperiodo['criteria'][0]['value']= 'C'.$value['CODCOLIGADA'].'F'.$value['CODFILIAL'].'E'.$value['CODNIVEL'].'-'.$value['CODCURSO'];
     
             $paramjsonconsultperiodo = json_encode($paramconsultperiodo);
                     
             $ch = curl_init();
             curl_setopt($ch, CURLOPT_URL, $url);
             curl_setopt($ch, CURLOPT_POST, 0);
             curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjsonconsultperiodo);
             curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
             $resultconsultacurso = curl_exec($ch);
             $consultaperiodo =  json_decode($resultconsultacurso,true);
             
             foreach( $consultaperiodo as $idnumber){

                if($idnumber['idnumber'] == 'C'.$value['CODCOLIGADA'].'F'.$value['CODFILIAL'].'E'.$value['CODNIVEL'].'-'.$value['CODCURSO']){
                   $idcurso = $idnumber['id'];
                }
             }
     
        $paramperiodo['categories'][0]['parent']= $idcurso;

        $paramjsonperiodo = json_encode($paramperiodo);
                
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjsonperiodo);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resultperiodo = curl_exec($ch);
        $resultperiodo = json_decode($resultperiodo,true);  
  
        // Criar Disciplina
        
        $paramdiscip = array();
        $paramdiscip['wstoken']="9649edb002bfda533e816259da2a4836"; //token de acesso ao webservice
        $paramdiscip['wsfunction']="core_course_create_courses";

        $paramdiscip['courses'][0]['fullname']= $value['DISCIPLINA'];
        $paramdiscip['courses'][0]['shortname']= $value['CODCOLIGADA'].'-'.$value['CODCURSO'].'-'.$value['IDTURMA'];
        $paramdiscip['courses'][0]['idnumber']= 'C'.$value['CODCOLIGADA'].'F'.$value['CODFILIAL'].'E'.$value['CODNIVEL'].'-'.$value['CODCURSO'].'-'.$value['PERIODO'].'-'.$value['IDTURMA'];

            // Consultar Categorias Filial
                    
            $paramconsultdiscip = array();
            $paramconsultdiscip['wstoken']="9649edb002bfda533e816259da2a4836"; //token de acesso ao webservice
            $paramconsultdiscip['wsfunction']="core_course_get_categories";
            
            $paramconsultdiscip['criteria'][0]['key']= 'idnumber';
            $paramconsultdiscip['criteria'][0]['value']= 'C'.$value['CODCOLIGADA'].'F'.$value['CODFILIAL'].'E'.$value['CODNIVEL'].'-'.$value['CODCURSO'].'-'.$value['PERIODO'];
    
            $paramjsonconsultdiscip = json_encode($paramconsultdiscip);
                    
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjsonconsultdiscip);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $resultconsultadiscip = curl_exec($ch);
            $consultadiscip =  json_decode($resultconsultadiscip,true);
            
            foreach( $consultadiscip as $idnumber){

                if($idnumber['idnumber'] == 'C'.$value['CODCOLIGADA'].'F'.$value['CODFILIAL'].'E'.$value['CODNIVEL'].'-'.$value['CODCURSO'].'-'.$value['PERIODO']){
                $idperiodo = $idnumber['id'];
                }
            }

        $paramdiscip['courses'][0]['categoryid']= $idperiodo;

        $paramjsondiscip = json_encode($paramdiscip);
                
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjsondiscip);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resultdiscip = curl_exec($ch);

        $results[] = [];
        
        array_push($results,$resultdiscip);
    }
   

    
    $responselogs = [];
                    
    date_default_timezone_set('America/Manaus');

    $hoje ='atualizacao-'.date('d-m-Y H;i');

    $file = fopen('C:/Users/tiago.souza/Documents/Github/moodle-rm/app/Http/Controllers/logs-cursos/'.$hoje.'.txt','w');
    
    foreach ( $results as $key => $value) {
    
        $value = str_replace('exception":"moodle_exception","errorcode":"shortnametaken","message":"Nome breve j\u00e1 \u00e9 usado em um outro curso', "Curso já existe", $value);
        $value = str_replace(']', "", $value); 
        $value = str_replace('[', "Curso criado:", $value); 
          
          if(gettype($value) == 'string'){
            array_push($responselogs, $value);
            fwrite($file, $value. PHP_EOL);
          }
       
    }

    $response = response()->json([
      'logs'=> $responselogs      
   ]);

    fclose($file); 
    return $response;

}



public function matricular(){
   
   @set_time_limit(2000);

   //Alunos RM
       
   $url = 'https://h-tbc.fametro.edu.br/api/framework/v1/consultaSQLServer/RealizaConsulta/alunos/0/S';
     
   $authHeaders = array(
      'login' => 'thiago.souzaa',
      'password' => 'Bondade07!'
   );
   
   $authHeaders[] = 'Authorization:Basic dGhpYWdvLnNvdXphYTpCb25kYWRlMDch';
        
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_POST, 0);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $authHeaders);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   $result = curl_exec($ch);

   $responsealunosrm = json_decode($result,true);
     

   $alunosMdl= array(
      'id'=> '',
      'nome'=> '',
      'codcoligada'=>'',
      'codfilial'=>'',
      'nomefilial'=>'',
      'nivel_ensino'=>'',
      'curso'=> '',
      'periodo'=>'',
      'disciplina'=>'',
      'idturma'=>''
   );


   $alunosMdlOrder=[];
   $alunocurso = [];


   foreach( $responsealunosrm as $key => $responsealunoRm){
   
      //Alunos Moodle
      
      $remotemoodle="http://localhost:9191/moodle";
      $url=$remotemoodle . '/webservice/restjson/server.php';

      //parametros a ser passado ao webservice
      $paramcourse=array();
      $paramcourse['wstoken']="9649edb002bfda533e816259da2a4836"; 
      $paramcourse['wsfunction']="core_user_get_users_by_field";
         
      $paramcourse['field'] = 'username';
      $paramcourse['values'][0] = $responsealunoRm['RA'];

      //converter array para json
      $paramjsoncourse = json_encode($paramcourse);

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, 0);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjsoncourse);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      $resultenroll = curl_exec($ch);
      $resultalunosmdl = json_decode($resultenroll,true);
    

      foreach ($resultalunosmdl as $key => $aluno) {   
         
         $alunosMdl['id'] = $aluno['id'];
         $alunosMdl['nome'] = $aluno['fullname'];
         $alunosMdl['codcoligada'] = $responsealunoRm['CODCOLIGADA'];
         $alunosMdl['codfilial'] = $responsealunoRm['CODFILIAL'];
         $alunosMdl['nomefilial'] = $responsealunoRm['NOMEFILIAL'];
         $alunosMdl['nivel_ensino'] = $responsealunoRm['NIVEL_ENSINO'];
         $alunosMdl['curso'] = $responsealunoRm['CURSO'];
         $alunosMdl['periodo'] = $responsealunoRm['PERIODO'];
         $alunosMdl['disciplina'] = $responsealunoRm['DISCIPLINA'];
         $alunosMdl['idturma'] = $responsealunoRm['IDTURMA'];
      }

      array_push($alunocurso,$alunosMdl); 
   }

   // Cursos Rm 
      
   $url = 'https://h-tbc.fametro.edu.br/api/framework/v1/consultaSQLServer/RealizaConsulta/disciplinas/0/S';
     
   $authHeaders = array(
      'login' => 'thiago.souzaa',
      'password' => 'Bondade07!'
   );
      
   $authHeaders[] = 'Authorization:Basic ZGlwbG9tYTpGQG0zdHIwMjI=';
            
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_POST, 0);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $authHeaders);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   $result = curl_exec($ch);
 
   $responsecursosrm = json_decode($result,true);
   


   $cursosaluno[]=[];
      
     
   foreach( $responsecursosrm as $key => $responsecursorm){
      
      $shortname = $responsecursorm['CODNIVEL'] . '-' . $responsecursorm['CODCURSO'] . '-' . $responsecursorm['IDTURMA'];

      //Cursos Moodle

      $remotemoodle="http://localhost:9191/moodle";
      $url=$remotemoodle .'/webservice/restjson/server.php';
   
      //parametros a ser passado ao webservice
      $paramcourse=array();
      $paramcourse['wstoken']="9649edb002bfda533e816259da2a4836"; 
      $paramcourse['wsfunction']="core_course_get_courses_by_field";
         
      $paramcourse['field'] = 'shortname';
      $paramcourse['value'] = $shortname;

      //converter array para json
      $paramjsoncourse = json_encode($paramcourse);
   
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_POST, 0);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjsoncourse);
         curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   
         $result = curl_exec($ch);
         $resultcourse = json_decode($result,true);
         array_push($cursosaluno,$resultcourse['courses']);
 
      }


      $idturma = '';
      $alunosmatriculados = array();


      foreach ($cursosaluno as $value) {
        
         foreach ($value as $curso) {
          
            foreach ($alunocurso as  $aluno) {

               $idturma = substr($curso['idnumber'],13,20);    

               $key1 = array_search($aluno['disciplina'],$curso);

               if( $key1 ){
                  
                  $idturmaaluno = $aluno['periodo'].'-'.$aluno['idturma'];

                  if($idturmaaluno  == $idturma){
               
                     array_push($alunosmatriculados,$aluno);
                  
                       // Matricula
                  
                        $remotemoodle="http://localhost:9191/moodle";
                        $url=$remotemoodle . '/webservice/restjson/server.php';
            
                        //parametros a ser passado ao webservice
                        $paramenroll=array();
                        $paramenroll['wstoken']="9649edb002bfda533e816259da2a4836"; 
                        $paramenroll['wsfunction']="enrol_manual_enrol_users";
                                    
                        $paramenroll['enrolments'][0]['roleid']= 5; 
                        $paramenroll['enrolments'][0]['userid']= $aluno['id'];
                        $paramenroll['enrolments'][0]['courseid']= $curso['id'];
                        $paramenroll['enrolments'][0]['timestart']= time(); 
            
                        //converter array para json
                        $paramjsonenroll = json_encode($paramenroll);
            
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_POST, 0);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjsonenroll);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
                        $resultenroll = curl_exec($ch);  
                  
                  }         
               }
            } 
         }
      }
     
   

    //Configurações para a criação do arquivo .txt

    // Obter a configuração de data/tempo de Manaus
    date_default_timezone_set('America/Manaus');
    
    // Data e hora 
    $hoje ='atualizacao-'.date('d-m-Y H;i');

    // Diretório onde será salvo os .txt  
    $file = fopen('C:/Users/tiago.souza/Documents/Github/moodle-rm/app/Http/Controllers/logs-matriculados/'.$hoje.'.txt','w');
    

          if($resultenroll == 'null'){

            foreach ($alunosmatriculados as $key => $value) {
               
               // Resposta que será exibida no .txt
               $log = 'Aluno(a) '.$value['nome'].' foi matriculado no curso '.$value['disciplina'].' '.$value['periodo'].'-'.$value['idturma'].' com sucesso';
          
               
               fwrite($file, $log. PHP_EOL);
            }
         
          }else{
            return response()->json([
             "message"=> "Algo deu errado tente novamente"
            ]);
          }
    
          fclose($file); 


       $responselogs = response()->json([
            'message'=>[
               'alunos matriculados em suas respectivas turmas'=> $alunosmatriculados
               ]
         ]);

         return $responselogs; 
}  
    


    
public function consultcateg(){
    // Consultar Categorias 

    $remotemoodle="http://localhost:9191/moodle"; 
    $url=$remotemoodle . '/webservice/restjson/server.php?';
           
    $paramconsulta = array();
    $paramconsulta['wstoken']="9649edb002bfda533e816259da2a4836"; //token de acesso ao webservice
    $paramconsulta['wsfunction']="core_course_get_categories";
    
    $paramconsulta['criteria'][0]['key']= 'idnumber';
    $paramconsulta['criteria'][0]['value']= 'C1';

    $paramjsonconsulta = json_encode($paramconsulta);
            
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjsonconsulta);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $resultconsulta = curl_exec($ch);
    $consulta =  json_decode($resultconsulta,true);
    

}

public function grades(){
   
   
   header("Access-Control-Allow-Origin: *");
   header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
   @set_time_limit(1800);
   error_reporting(1);


   $remotemoodle="http://localhost:9191/moodle"; 
   $url=$remotemoodle . '/webservice/restjson/server.php?';


   $param=array();
   $param['wstoken']="9649edb002bfda533e816259da2a4836";
   $param['wsfunction']="gradereport_user_get_grade_items";
   
 
   $param['courseid'] ='11580';
   
 
   $paramjson = json_encode($param);
   
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_POST, 0);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjson);
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   $result = curl_exec($ch);

   $response = json_decode($result,true);
   $response = $response['usergrades'];
   $gradeitems = array();
   $notas = array();


   foreach ($response as $key => $value) {
 /*      array_push($gradeitems,$value['gradeitems']);
      array_push($gradeitems, $response[$key]['fullname']) ; */

      echo $key;
 
}
exit;
      $notas[3]['idnumber'] = $notas[3][0]; //nome
      unset($notas[3][0]);
      
      
   $data = array(
         "NOTA"=> $notas[3]['graderaw'],
         "NOMECIVIL"=> $notas[3]['idnumber'],
         "DESCPROVA"=>  $notas[3]['itemname']
   );

  
      
   //API RM

   //GET 
   $headers = [
      'Authorization: Basic '. base64_encode("diploma:F@m3tr022"),
      'CODCOLIGADA:1',
      'CODTIPOCURSO:1',
      "CODSISTEMA:'G'",
   ];

   $curl = curl_init();
   curl_setopt_array($curl, [
      CURLOPT_URL => 'https://h-tbc.fametro.edu.br/rmsrestdataserver/rest/EduNotasData',
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => $headers,
   ]);

   $result = curl_exec($curl);

   curl_close($curl);

   $response = json_decode($result,true);
      
   $response =  $response['data'];


    foreach($response as $value){
       if($value['NOMECIVIL'] == $data['NOMECIVIL'] && $value['DESCPROVA'] == $data['DESCPROVA'] && $value['id'] == '1$_$1$_$1$_$N$_$70913$_$1906793'){
         
               //PATCH
           
                  $headers = [
                     'Authorization: Basic '. base64_encode("diploma:F@m3tr022"),
                     'CODCOLIGADA:1',
                     'CODTIPOCURSO:1',
                     'CODFILIAL:'.$value['CODFILIAL'],
                     "CODSISTEMA:'G'",
                  ];
                  
                  
                  $curl = curl_init();
                  curl_setopt_array($curl, [
                     CURLOPT_URL => 'https://h-tbc.fametro.edu.br/rmsrestdataserver/rest/EduNotasData/'.$value['id'],
                     CURLOPT_CUSTOMREQUEST => 'PATCH',
                     CURLOPT_RETURNTRANSFER => true,
                     CURLOPT_HTTPHEADER => $headers,
                     CURLOPT_POSTFIELDS => json_encode($data,JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                  ]);
         
       }
    }
         
         
 
      
      $result = curl_exec($curl);

      curl_close($curl);

      $response = json_decode($result,true);

      dd($response ); 
      
}


}




/*  $curl = curl_init();

   curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://h-tbc.fametro.edu.br/rmsrestdataserver/rest/EduNotasData/1$_$1$_$1$_$N$_$70913$_$1906793',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'PATCH',
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
          'cache-control: no-cache',
          'Accept: application/json',
          'Content-Type: application/json',
          'Accept-Encoding:gzip, deflate, br',
          'Connection:keep-alive',
          'Authorization: Basic '. base64_encode("diploma:F@m3tr022"),
          'CODCOLIGADA'=>'1',
          'CODTIPOCURSO'=>'1',
          'CODSISTEMA'=>'G'
      ),
   ));

   
   $result = curl_exec($curl);

   curl_close($curl);

   $response = json_decode($result,true);

   dd($response ); 
   exit; */

//Requisição antigo
    
     /*      $WSDL= ('https://h-tbc.fametro.edu.br/wsdataserver/MEX?wsdl');
 
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
         "Filtro"=>"ADM01",
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
  
     $cursosrm = $response_rm['SCurso']; 
    
     dd($cursosrm);
     exit;
     */
  


 

