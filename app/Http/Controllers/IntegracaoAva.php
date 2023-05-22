<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class IntegracaoAva extends Controller
{
    
    public function criaralunos(){

        //RM 

        $urlrm = 'https://h-tbc.fametro.edu.br/api/framework/v1/consultaSQLServer/RealizaConsulta/criaralunosmdl/0/S';

        $authHeaders = array(
            'login' => 'thiago.souzaa',
            'password' => 'Bondade07!'
         );
         
         $authHeaders[] = 'Authorization:Basic dGhpYWdvLnNvdXphYTpCb25kYWRlMDch';
              
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, $urlrm);
         curl_setopt($ch, CURLOPT_POST, 0);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $authHeaders);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         $responserm = curl_exec($ch);

         $responsealunosrm = json_decode($responserm,true);

         //dd($responsealunosrm);
  
         
         // MDL

         $criateriakey = '';
         $criateriavalue = '';

         $urlmdl="https://avapos.fametro.edu.br/webservice/rest/server.php?wstoken=68bc649ff1bd5459da65722dd1a9fc13&wsfunction=core_user_get_users&moodlewsrestformat=json&criteria[0][key]=$criateriakey&criteria[0][value]=$criateriavalue";
 
        
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, $urlmdl);
         curl_setopt($ch, CURLOPT_POST, 0);
         curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         $responsemdl = curl_exec($ch);

         $resultalunosmdl = json_decode($responsemdl,true);
        
        //  dd($resultalunosmdl);


         $resultalunosmdl =  $resultalunosmdl['users'];
        
         $usernames = array_column($resultalunosmdl,'username');
    
        foreach ($responsealunosrm as $key => $value) {

        $key = array_search( $value['USERNAME'],$usernames);
    
      
       if( !$key ){
   
        // echo "<pre>";
        // var_dump($value['PASSWORD']);
        // echo "</pre>";  
        
        
        $url = 'https://avapos.fametro.edu.br/webservice/rest/server.php';
        $token = '68bc649ff1bd5459da65722dd1a9fc13';
        $params = array(
        'wstoken' => $token,
        'wsfunction' => 'core_user_create_users',
        'moodlewsrestformat'=>'json',
        'users[0][password]'=> $value['PASSWORD'],
        'users[0][username]'=> $value["USERNAME"],
        'users[0][firstname]'=> $value['FIRSTNAME'],
        'users[0][lastname]'=> $value['LASTNAME'],
        'users[0][email]'=> $value['EMAIL']
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result,true);

        echo '<pre>';
        print_r($response);
        echo '</pre>';
       }       

    }


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

    //    dd($responsealunosrm); 


      foreach ($responsealunosrm as $key => $value) {

        
        $url = 'https://avapos.fametro.edu.br/webservice/rest/server.php';
        $token = '68bc649ff1bd5459da65722dd1a9fc13';
        $params = array(
        'wstoken' => $token,
        'wsfunction' => 'core_course_get_courses_by_field',
        'moodlewsrestformat'=>'json',
        'field'=> 'id',
        'value'=> $value["IDMDL"]

        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result,true);


        $responsecourse = $response['courses'];

        // dd($responsecourse);


        //    echo '<pre>';
        //    print_r($responsecourse);
        //     echo '</pre>';
            
        
            // MDL - Filtro Aluno


            $url= 'https://avapos.fametro.edu.br/webservice/rest/server.php';
            $token = '68bc649ff1bd5459da65722dd1a9fc13';

            $param =array(
              'wstoken'=> $token,
              'wsfunction'=>'core_user_get_users',
              'moodlewsrestformat'=>'json',
              'criteria[0][key]'=> 'username',
              'criteria[0][value]'=> $value['USERNAME']
            );
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);

          
            $response = json_decode($result,true);   

            $resposealuno = $response['users'];
  

            foreach ($responsecourse as $key => $curso) {
        
                foreach ($resposealuno as $key => $aluno) {
                
                    // echo '<pre>';
                    // print_r($curso['id']);
                    // echo '</pre>';
                    
                // Matricula
                            
                
                $url= 'https://avapos.fametro.edu.br/webservice/rest/server.php';
                $token = '68bc649ff1bd5459da65722dd1a9fc13';
    
                $param =array(
                  'wstoken'=> $token,
                  'wsfunction'=>'enrol_manual_enrol_users',
                  'moodlewsrestformat'=>'json',
                  'enrolments[0][roleid]'=> 5,
                  'enrolments[0][userid]'=> $aluno['id'],
                  'enrolments[0][courseid]'=> $curso['id'],
                  'enrolments[0][timestart]'=> time()
                );
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $resultenroll = curl_exec($ch);
                curl_close($ch);
                
                        
                echo '<pre>';
                print_r($resultenroll);
                echo '</pre>';
            }

        }
          

    } //foreach geral     

}


public function desinscreveralunos(){

    set_time_limit(3000);

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

    // dd($responsealunosrm);
    
 
    if($responsealunosrm == []){
        echo 'Servidor indisponível';
        die;
    }

    // dd($responsealunosrm); // Alunos matriculados do RM

        foreach ($responsealunosrm as $key => $value) {

                
            $url= 'https://avapos.fametro.edu.br/webservice/rest/server.php';
            $token = '68bc649ff1bd5459da65722dd1a9fc13';

            $param =array(
              'wstoken'=> $token,
              'wsfunction'=>'core_enrol_get_enrolled_users',
              'moodlewsrestformat'=>'json',
              'courseid'=> $value['IDMDL'], //$value['IDMDL']
            );
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);        
        
            $responseenrolled = json_decode($result,true); // Alunos Matriculados do Moodle

            echo '<pre>';
            print_r($responseenrolled);
            echo '</pre>';

            /* $usernamemdl = array_column($responseenrolled,'username','id');
            
            
            if(in_array($value['RA'],$usernamemdl)){
               $key =  array_search($value['RA'],$usernamemdl);
               
              if( $key != false){
                unset($usernamemdl[$key]);
              }
              
              foreach ($usernamemdl as $key => $value2) {
            
             
             
                $url= 'https://avapos.fametro.edu.br/webservice/rest/server.php';
                $token = '68bc649ff1bd5459da65722dd1a9fc13';
    
                $param =array(
                  'wstoken'=> $token,
                  'wsfunction'=>'enrol_manual_unenrol_users',
                  'moodlewsrestformat'=>'json',
                  'enrolments[0][userid]'=> $key,
                  'enrolments[0][courseid]'=> $value['IDMDL'], //$value['IDMDL']
                  'enrolments[0][roleid]' => 5
                );
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $responseenrolled = curl_exec($ch);
                curl_close($ch);        
        

                echo '<pre>';
                print_r($responseenrolled); 
                echo '</pre>';  
            
             }
        } */

        
    
        }

        
            
    }


    // SOMENTE PARA O DIGITAL

    // CRIAR CURSO

    public function criarcursos(){

        @set_time_limit(1800);
         
        $url = 'https://h-tbc.fametro.edu.br/api/framework/v1/consultaSQLServer/RealizaConsulta/criarcursomdl/0/S';
        
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
   
        $responsecoursesrm = json_decode($result,true);

        // dd($responsecoursesrm);
      
        //MDL
      
        foreach ($responsecoursesrm as $key => $value) {

            //Get Category
  
            $url= 'https://avapos.fametro.edu.br/webservice/rest/server.php';
            $token = '68bc649ff1bd5459da65722dd1a9fc13';

            $param =array(
              'wstoken'=> $token,
              'wsfunction'=>'core_course_get_categories',
              'moodlewsrestformat'=>'json',
              'criteria[0][key]'=> 'idnumber', 
              'criteria[0][value]'=> $value['CATEGORY_IDNUMBER']
            );
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);      

            $responsecategory = json_decode($result,true);

            // dd($responsecategory);

 
            //Create Course

            $url = "https://avapos.fametro.edu.br/webservice/restjson/server.php";
            $token = "68bc649ff1bd5459da65722dd1a9fc13";
     
            $param = array(
                'wstoken' => $token,
                'wsfunction'=>'core_course_create_courses',
                'moodlewsrestformat'=>'json',
                'courses[0][fullname]'=> $value['FULLNAME'],
                'courses[0][shortname]'=> $value['SHORTNAME'],
                'courses[0][categoryid]'=>$responsecategory[0]['id']
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);  
            
            
            $response = json_decode($result,true);
            
            echo '<pre>';
            print_r($response);
            echo '<pre>';
        }

    }   

    // CRIAR CURSO

    public function criarprofessor(){

        @set_time_limit(1800);
         
        $url = 'https://h-tbc.fametro.edu.br/api/framework/v1/consultaSQLServer/RealizaConsulta/criarprofmdl/0/S';
        
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
   
        $responsecoursesrm = json_decode($result,true);

        dd($responsecoursesrm);

    }
}
      
    



