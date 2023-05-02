<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GradesMoodleRm extends Controller
{
    public function sendGrades(){
     
      header("Access-Control-Allow-Origin: *");
      header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
      @set_time_limit(1800);
      error_reporting(1);


      $remotemoodle="http://localhost:9090/moodle"; 
      $url=$remotemoodle . '/webservice/restjson/server.php?';


      $param=array();
      $param['wstoken']="4f92db8681694f63fae899d38dfba2c6";
      $param['wsfunction']="gradereport_user_get_grade_items";
      
    
      $param['courseid'] ='6510';
      
    
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
      
      $nome = [];
      $notas = [];

   

      foreach ($response as $key => $value) {

        array_push($nome,$value['userfullname']);
        
        array_push($notas,$value['gradeitems'][0]['gradeformatted']);
      } 
    
       $aluno = [];

      foreach ($nome as $key => $value) {
          $aluno['aluno '.$key+1]['nome'] = $nome[$key];
        
      }

      foreach($notas as $key => $value){
          $aluno['aluno '.$key+1]['nota'] = $notas[$key];
      }
    

      dd($aluno);
      exit; 



      $mdlresponse['EduNotas']['SNotas']['CODCOLIGADA']='1';
      $mdlresponse['EduNotas']['SNotas']['CODPROVA']='1';
      $mdlresponse['EduNotas']['SNotas']['CODETAPA']='1';
      $mdlresponse['EduNotas']['SNotas']['TIPOETAPA']='N';
      $mdlresponse['EduNotas']['SNotas']['IDTURMADISC']='70913';
      $mdlresponse['EduNotas']['SNotas']['RA']= '1906793';
      $mdlresponse['EduNotas']['SNotas']['NOTA']= '4.0000';



   function array_to_xml( $mdlresponse, $xml = null ) {
        if ( is_array( $mdlresponse ) ) {
          foreach( $mdlresponse as $key => $value ) {

            if ( is_int( $key ) ) {
              if ( $key == 0 ) {
                $node = $xml;
              } else {
                $parent = $xml->xpath("..")[0];
              }
            } else {
              $node = $xml->addChild( $key );
            }
            array_to_xml( $value, $node );
          }
        } else {
          $xml[0] = $mdlresponse;
        }
      } 
      
      $xml = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"iso-8859-1\"?><root></root>");
      
      array_to_xml( $mdlresponse, $xml);
      
      $responseXml = $xml->asXML();


    ////////////////////////////////////////////////////////////////////////////////////////////////////////



      $WsdlRM = ('https://h-tbc.fametro.edu.br/wsdataserver/MEX?wsdl');
       
        
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
            "DataServerName"=>"EduNotasData",
            "XML"=>  $responseXml,
            "Contexto"=>"CODCOLIGADA=1",
  
        );

        $ClientSoap = new \SoapClient($WsdlRM, $ParametrosAutenticarSoap);
        $result = $ClientSoap->SaveRecord($RealizarConsultaSql);

        $code_response = http_response_code();  
       
        $response = json_encode($result,true); 

         dd($response);

      }
}



// Modele de Xml para ser enviado para o RM (Soap)

/*
     &lt;EduNotas&gt;
		    &lt;SNotas&gt;
		         &lt;CODCOLIGADA&gt;1 &lt;/CODCOLIGADA&gt;
		         &lt;CODPROVA&gt;1 &lt;/CODPROVA&gt;
		         &lt;CODETAPA&gt;1 &lt;/CODETAPA&gt;
		         &lt;TIPOETAPA&gt;N&lt;/TIPOETAPA&gt;
		         &lt;IDTURMADISC&gt;70913&lt;/IDTURMADISC&gt;
		         &lt;RA&gt;1906793&lt;/RA&gt;
		         &lt;NOTA&gt;4.0000&lt;/NOTA&gt;
		    &lt;/SNotas&gt;
	  &lt;/EduNotas&gt;


*/

// Modele de Xml para ser enviado para o RM (Código)

/*<EduNotas> 
            <SNotas>
            <CODCOLIGADA>1 </CODCOLIGADA>
            <CODPROVA>1 </CODPROVA>
            <CODETAPA>1 </CODETAPA>
            <TIPOETAPA>N</TIPOETAPA>
            <IDTURMADISC>70913</IDTURMADISC>
            <RA>1906793</RA>
            <NOTA>3.0000</NOTA>
            </SNotas>
          </EduNotas>
 

    