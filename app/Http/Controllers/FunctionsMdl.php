<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FunctionsMdl extends Controller
{
    
//Excluir Cursos

public function deletarcurso(){


    @set_time_limit(1800);
 
    $ids = [];

    foreach ($ids as $key => $value) {
                $remotemoodle='https://digital.fametro.edu.br'; 
                $url=$remotemoodle . '/webservice/restjson/server.php?';
 
                $param=array();
                $param['wstoken']="27f18ecb5d6bcbb9d0c4a51ffd1ad7ce"; //token de acesso ao webservice
                $param['wsfunction']="core_course_delete_courses";
 
                $param['courseids'][0] = $value;
              
                $paramjson = json_encode($param);
 
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 0);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjson);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result= curl_exec($ch);
            
             }
                echo( $result);

             
    }
    
    public function matriculados(){
        

                    $remotemoodle="http://localhost:9191/moodle";
                    $url=$remotemoodle .'/webservice/restjson/server.php';
                    
                    //parametros a ser passado ao webservice
                    $paramcourse=array();
                    $paramcourse['wstoken']="9649edb002bfda533e816259da2a4836"; 
                    $paramcourse['wsfunction']="core_enrol_get_enrolled_users";
                        
                    $paramcourse['courseid'] = 92;
            
                    //converter array para json
                    $paramjsoncourse = json_encode($paramcourse);
                    
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, 0);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjsoncourse);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($ch);
                    $responseenrolled = json_decode($response,true);
                    
           
                    dd($responseenrolled[0]); 
                  
    }
 
}
