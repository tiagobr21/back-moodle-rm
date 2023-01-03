<?php

namespace App\Http\Controllers;

use App\Http\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiMoodleController extends Controller {

 //TESTES WEBSERVICE MOODLE 

    public function mood(){ 
      
      //OBTER OS IDS

      $remotemoodle="localhost:9090/moodle"; //MOODLE_URL - endereço do Moodle
      $url=$remotemoodle . '/webservice/rest/server.php';
     
      //parametros a ser passado ao webservice
      $param_getUser=array();
      $param_getUser['wstoken']="9d9da5b8866e17a91ccd5987b92b24b5"; //token de acesso ao webservice
      $param_getUser['wsfunction']="core_user_get_users";
     
      //filtro de usuário
      $param_getUser['criteria'][0]['key']='';
      $param_getUser['criteria'][0]['value']='';
     
      //converter array para json
      $paramjson_getUser = json_encode($param_getUser);
      
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, 0);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjson_getUser);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $result_getUser = curl_exec($ch);
     
      dd($result_getUser);
      exit 

      

     //CONSULTANDO AS NOTAS POR ID
     
      //parametros a ser passado ao webservice
      $param_getGrade=array();
      $param_getGrade['wstoken']="b64eb1b5fb4ecdfe05014ddd679c7362"; //token de acesso ao webservice
      $param_getGrade['wsfunction']="gradereport_user_get_grade_items";
     
      //filtro de usuário
      
      $param_getGrade['courseid'] ='2';
      $param_getGrade['userid'] ='2396';
      $param_getGrade['groupid'] ='1';
      
     
      //converter array para json
      $paramjson_getGrade = json_encode($param_getGrade);
      echo($paramjson_getGrade);
      exit;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, 0);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjson_getGrade);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $result_getGrade = curl_exec($ch);
 ;
         
      //converter json para array
      $response_getGrades = json_decode( $result_getGrade,true); 
       
   


    function array_to_xml( $response_getGrades, $xml = null ) {
        if ( is_array( $response_getGrades ) ) {
          foreach( $response_getGrades as $key => $value ) {
            if ( is_int( $key ) ) {
              if ( $key == 0 ) {
                $node = $xml;
              } else {
                $parent = $xml->xpath("..")[0];
                $node = $parent->addChild( $xml->getName() );
              }
            } else {
              $node = $xml->addChild( $key );
            }
            array_to_xml( $value, $node );
          }
        } else {
          $xml[0] = $response_getGrades;
        }
      }
      
      $xml = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\"?><root>\n</root>");
      
      array_to_xml( $response_getGrades, $xml);
      dd($xml->asXML());       
     
    
    }
  }

