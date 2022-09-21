<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GradesMoodleRm extends Controller
{
    public function sendGrades(){
     
    @set_time_limit(1800);
    error_reporting(1);
   
     //OBTER OS IDS

      $remotemoodle="localhost/moodle"; //MOODLE_URL - endereço do Moodle
      $url=$remotemoodle . '/webservice/restjson/server.php';
     
      //parametros a ser passado ao webservice
      $param_getUser=array();
      $param_getUser['wstoken']="b64eb1b5fb4ecdfe05014ddd679c7362"; //token de acesso ao webservice
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
       //$result =json_decode($result);
     
      $response_getUsers = json_decode( $result_getUser,true); 
       
      $response_getUsers = $response_getUsers['users'];

      $userId = array_column($response_getUsers,"id");
      $userUserName = array_column($response_getUsers,"username");
      
      unset($userId[0],$userId[1]);
      unset($userUserName[0],$userUserName[1]);


      


    ///////////////////////////////////////////////////////////////////////////////////////////////


        
     //CONSULTANDO AS NOTAS POR ID
    foreach($userId as $valueId){

   //parametros a ser passado ao webservice
      $param_getGrade=array();
      $param_getGrade['wstoken']="b64eb1b5fb4ecdfe05014ddd679c7362"; //token de acesso ao webservice
      $param_getGrade['wsfunction']="gradereport_user_get_grade_items";
     
      //filtro de usuário
      
    
      $param_getGrade['courseid'] ='2';
      $param_getGrade['userid'] = $valueId;
      $param_getGrade['groupid'] ='1';
      
     
      //converter array para json
      $paramjson_getGrade = json_encode($param_getGrade);
      
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, 0);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $paramjson_getGrade);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $result_getAllGrade = curl_exec($ch);
       //$result =json_decode($result);
     
      $response_getGrade = json_decode($result_getAllGrade,true); 

      $response_getGrades = $response_getGrade['usergrades'];
      
      echo '<pre>';
      print_r( $response_getGrade);
      echo '</pre>';   
 


     
    function array_to_xml( $response_getGrades, $xml = null ) {
        if ( is_array( $response_getGrades ) ) {
          foreach( $response_getGrades as $key => $value ) {
            if ( is_int( $key ) ) {
              if ( $key == 0 ) {
                $node = $xml;
              } else {
                $parent = $xml->xpath("..")[0];
                $node = $parent->addChild( $xml->getName());
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
      
      $xml = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"iso-8859-1\"?><root>\n</root>");
      
      array_to_xml( $response_getGrades, $xml);
      
      $responseXml = $xml->asXML();
      
      dd($responseXml); 



    ////////////////////////////////////////////////////////////////////////////////////////////////////////

    //Api rm 

     /*    $WsdlRM = ('https://teste.portaledu.com.br/TOTVSBusinessConnect/wsDataServer.asmx?wsdl');
       
        
        $soapParams = array(
            "login" => "moodle_rm",
            "password" => "Bondade23!",
            "authentication" => SOAP_AUTHENTICATION_BASIC,
            "trace" => 1,
            "exceptions" => 0
        );

        $RealizarConsultaSql = array(
            "DataServerName"=>"EduNotasData",
            "Xml"=>$responseXml,
            "Contexto"=>"CODCOLIGADA=1",
            "Usuario"=>"moodle_rm",
            "Senha"=>"Bondade23!"
        );

        $client = new \SoapClient($WsdlRM, $soapParams);
        $result = $client->SaveRecordAuth($RealizarConsultaSql);
        $code_response = http_response_code();  

        dd($responseXml); */
     
      }
}
}

