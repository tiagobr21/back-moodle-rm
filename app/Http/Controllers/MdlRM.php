<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class MdlRM extends Controller
{
    
    public function criaralunos(){

        //RM 

        $url = 'https://h-tbc.fametro.edu.br/api/framework/v1/consultaSQLServer/RealizaConsulta/criaralunosmdl/0/S';

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
         $responserm = curl_exec($ch);

         $responsealunosrm = json_decode($responserm,true);

        //  dd($responsealunosrm);
         
         // MDL

         $remotemoodle="http://localhost:9191/moodle";
         $url=$remotemoodle . '/webservice/restjson/server.php';
   
         //parametros a ser passado ao webservice
         $paramcourse=array();
         $paramcourse['wstoken']="9649edb002bfda533e816259da2a4836"; 
         $paramcourse['wsfunction']="core_user_get_users";
            
         $paramcourse['criteria'][0]['key'] = '';
         $paramcourse['criteria'][0]['value'] = '';
   
         //converter array para json
         $paramjsoncourse = json_encode($paramcourse);
   
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_POST, 0);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjsoncourse);
         curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   
         $responsemdl = curl_exec($ch);

         $resultalunosmdl = json_decode($responsemdl,true);

         $resultalunosmdl =  $resultalunosmdl['users'];
        
         $usernames = array_column($resultalunosmdl,'username');
    
        foreach ($responsealunosrm as $key => $value) {

        $key = array_search( $value['USERNAME'],$usernames);
    
      
       if( $key ){
        
       }else{
         
        // echo "<pre>";
        // var_dump($value['EMAIL']);
        // echo "</pre>";   
        
        $remotemoodle="http://localhost:9191/moodle"; 
        $url=$remotemoodle . '/webservice/restjson/server.php?';

        $param_createUser=array();
        $param_createUser['wstoken']="9649edb002bfda533e816259da2a4836"; //token de acesso ao webservice
        $param_createUser['wsfunction']="core_user_create_users";
       
        
        $param_createUser['users'][0]['password'] = $value['PASSWORD'];
        $param_createUser['users'][0]['username']=$value["USERNAME"];
        $param_createUser['users'][0]['firstname']= $value['FIRSTNAME'];
        $param_createUser['users'][0]['lastname']= $value['LASTNAME'];
        $param_createUser['users'][0]['email']= $value['EMAIL'];
          
    
        $paramjson_create = json_encode($param_createUser);
                
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjson_create);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result_createUser= curl_exec($ch);

        printf($result_createUser);

        $resultsalunos []= [];
        $errors[]=[];
        $response_err=[];
    
        array_push($resultsalunos,$result_createUser); 
 
    
       }

    }

    $response['logs'] = [];
         
            date_default_timezone_set('America/Manaus');

            $hoje ='atualizacao-'.date('d-m-Y H;i');

            $file = fopen('C:/Users/tiago.souza/Documents/GitHub/moodle-rm/app/Http/Controllers/logs-alunos/'.$hoje.'.txt','w');
            foreach ( $resultsalunos as $value) {
                
                $value = str_replace(']', "", $value); 
                $value = str_replace('[', "Usuário criado:", $value); 
                
                array_push($response['logs'],$value);
                 
                if(gettype($value) == 'string'){
                   fwrite($file, $value . PHP_EOL); 
                }
                
            }
           fclose($file); 

          http_response_code(200);
       
        
} 

public function matricularalunos(){
     //RM 

     @set_time_limit(1800);

     $url = 'https://h-tbc.fametro.edu.br/api/framework/v1/consultaSQLServer/RealizaConsulta/inscralunosmdl/0/S';

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
      $responserm = curl_exec($ch);

      $responsealunosrm = json_decode($responserm,true); //Alunos do Rm 

      // dd($responsealunosrm);  


      foreach ($responsealunosrm as $key => $value) {

        
            // MDL - Filtro Curso

            $remotemoodle="http://localhost:9191/moodle";
            $url=$remotemoodle .'/webservice/restjson/server.php';
        
            //parametros a ser passado ao webservice
            $paramcourse=array();
            $paramcourse['wstoken']="9649edb002bfda533e816259da2a4836"; 
            $paramcourse['wsfunction']="core_course_get_courses_by_field";
            
            $paramcourse['field'] = 'id';
            $paramcourse['value'] = $value['IDMDL'];  

            //converter array para json
            $paramjsoncourse = json_encode($paramcourse);
        
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjsoncourse);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $responsecourse = json_decode($response,true);

            $responsecourse = $responsecourse['courses'];
            
            // dd($responsecourse);


            // MDL - Filtro Aluno


                $remotemoodle="http://localhost:9191/moodle"; 
                $url=$remotemoodle . '/webservice/restjson/server.php?';
            

                $param =array();
                $param['wstoken']="9649edb002bfda533e816259da2a4836"; 
                $param['wsfunction']="core_user_get_users";
            
                $param['criteria'][0]['key']='username';
                $param['criteria'][0]['value']= $value['USERNAME'];
                
                $paramjson = json_encode( $param );
            
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 0);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjson);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);        
                $resposealuno = json_decode($response,true);

                $resposealuno = $resposealuno['users'];

                // dd($resposealuno);

            foreach ($responsecourse as $key => $curso) {
        
                foreach ($resposealuno as $key => $aluno) {
                
                    // echo '<pre>';
                    // print_r($curso['id']);
                    // echo '</pre>';
                    
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
                        
                         echo '<pre>';
                         print_r($resultenroll);
                         echo '</pre>';
                    }

            }
          

    } //foreach geral     

}


public function desinscreveralunos(){

    //RM 

    $url = 'https://h-tbc.fametro.edu.br/api/framework/v1/consultaSQLServer/RealizaConsulta/desinscralunomdl/0/S';

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
    $responserm = curl_exec($ch);
    
    $responsealunosrm = json_decode($responserm,true); 
   
 
    if($responsealunosrm == []){
        echo 'Servidor indisponível';
        die;
    }

     dd($responsealunosrm); // Alunos matriculados do RM

        foreach ($responsealunosrm as $key => $value) {
                
                    
            $remotemoodle="http://localhost:9191/moodle";
            $url=$remotemoodle .'/webservice/restjson/server.php';
                            
        
            $param=array();
            $param['wstoken']="9649edb002bfda533e816259da2a4836"; 
            $param['wsfunction']="core_enrol_get_enrolled_users";   

            $param['courseid'] = 92;
      
            $paramjson = json_encode($param);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjson);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $responseenrolled = json_decode($response,true); // Alunos Matriculados do Moodle

            $username = array_column($responseenrolled,'username','id');
            
            $key_exist = array_search($value['RA'],$username);
            
            foreach ($username as $key => $value) {
            
                if($key_exist != $key){  // Se um aluno não estiver matriculado no Rm ele dever se desmatriculado do Moodle
             
                    $remotemoodle="http://localhost:9191/moodle";
                    $url=$remotemoodle .'/webservice/restjson/server.php';
                                    
                 
                    $param=array();
                    $param['wstoken']="9649edb002bfda533e816259da2a4836"; 
                    $param['wsfunction']="enrol_manual_unenrol_users";
                        
                    $param['enrolments'][0]['userid'] = $key;
                    $param['enrolments'][0]['courseid'] = 92; //$value['IDMDL']
                    $param['enrolments'][0]['roleid'] = 5;
                            
             
                    $paramjson = json_encode($param);
                    
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, 0);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjson);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($ch);
                    $responseenrolled = json_decode($response,true);
                    
                    echo '<pre>';
                    print_r($responseenrolled); 
                    echo '</pre>';  
                }
            }
        
    
        }
            
            
    }
}
      
    



