<?php
$user = strtoupper($_POST['user']);
//$user = trim($user,' ');
//$user=  (string)$user;
//$pos = strpos($user, 'A');
//if($pos != NULL){
if(strpos($user,'A') != NULL||strpos($user,'B') != NULL||strpos($user,'C') != NULL){

    while( strlen($user)<8){
                 $user = '0'.$user;
}
}
if ($user == null)
{
    echo '<span>Por favor completa todos los campos.</pan>';
echo'<script type="text/javascript">alert("POR FAVOR COMPLETA TODOS LOS CAMPOS");</script>';
}
else
{
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        require_once("conBD.php");
//        require_once("nusoap/nusoap.php");
        require_once("persona.php");
        require_once("conBD.php");
        require_once("funciones.php");
         $paso=1;
         $login = array(
         'login' => 'sfpconsulta',
         'password' => 'sfp.570Sfp',
         'exceptions' => 0);
         
         	$personaVacio = array(
					"nroDocumento" => "vacio",
					"nombre" => "vacio",
					"apellido" => "vacio",
					"fechaNacimiento" => "vacio",
					"sexo" => "vacio",
					"nacionalidad" => "vacio",
					"lugarNacimiento" => "vacio",
					"domicilio" => "vacio",
					"estadoCivil" => "vacio"
				);
         $url= 'http://sii.senatics.gov.py:8080/mbohape/PersonaWS?wsdl';
         $ch=curl_init($url);
         curl_setopt($ch, CURLOPT_TIMEOUT, 5);
         curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         $data=curl_exec($ch);
 
         # capturamos el codigo HTTP devuelto por el servidor
         $httpcode=curl_getinfo($ch, CURLINFO_HTTP_CODE);
 
         curl_close($ch);
         if ( $httpcode == 0){
         $objClienteSOAP= new SoapClient($url, $login);
         $aParametros = array('nroCedula'=> $user);
         $objRespuesta= $objClienteSOAP->findPersonaByCedula($aParametros);
//         echo '<pre>';print_r($objRespuesta); echo '</pre>';
                if ( $objRespuesta->return->nombres){
//                        echo '<pre><strong> Numero de Cedula:   '.$datosPersona['nroDocumento'].'</strong></pre>';
//                        echo '<pre><strong> Numero de Cedula:   ';print_r($objRespuesta->return->cedula);'</strong></pre>';
                        echo '<pre><strong>                         "DATOS"</strong></pre>';
                        echo '<pre><strong>       Numero de Cedula:       ';print_r($objRespuesta->return->cedula); echo '</strong></pre>';
                        echo '<pre><strong>       Nombres:                ';print_r(str_replace(" ","*",trim($objRespuesta->return->nombres, ' '))); echo '</strong></pre>';
                        echo '<pre><strong>       Apellidos:              ';print_r(str_replace(" ","*",trim($objRespuesta->return->apellido,' '))); echo '</strong></pre>';
                        echo '<pre><strong>       Fecha de Nacimiento:    ';print_r(substr($objRespuesta->return->fechNacim, 0, 10)); echo '</strong></pre>';
                        echo '<pre><strong>       Sexo:                   ';print_r($objRespuesta->return->sexo); echo '</strong></pre>';
                        echo '<pre><strong>       Nacionalidad:           ';print_r($objRespuesta->return->nacionalidadBean); echo '</strong></pre>';
                        echo '<pre><strong>       Lugar de Nacimiento:    SIN REGISTROS</strong></pre>';
                        echo '<pre><strong>       Domicilio:              SIN REGISTROS</strong></pre>';
                        echo '<pre><strong>       Estado Civil:           ';print_r($objRespuesta->return->estadoCivil); echo '</strong></pre>';
                        echo '<pre><strong>       (*)Espacio</strong></pre>';
                }else{
                     if ($objRespuesta->return->error == 'Persona no encontrada.'){
                        echo '<pre><strong>               "PERSONA NO ENCONTRADA"</strong></pre>';
                        echo '<pre><strong>       Numero de Cedula:       SIN REGISTROS</strong></pre>';
                        echo '<pre><strong>       Nombres:                SIN REGISTROS</strong></pre>';
                        echo '<pre><strong>       Apellidos:              SIN REGISTROS</strong></pre>';
                        echo '<pre><strong>       Fecha de Nacimiento:    SIN REGISTROS</strong></pre>';
                        echo '<pre><strong>       Sexo:                   SIN REGISTROS</strong></pre>';
                        echo '<pre><strong>       Nacionalidad:           SIN REGISTROS</strong></pre>';
                        echo '<pre><strong>       Lugar de Nacimiento:    SIN REGISTROS</strong></pre>';
                        echo '<pre><strong>       Domicilio:              SIN REGISTROS</strong></pre>';
                        echo '<pre><strong>       Estado Civil:           SIN REGISTROS</strong></pre>';
                     }else{
                     $paso=2;
                     }
                
                     }
         
         
         }else{
             $paso=2;
         }
         if($paso==2){
             //Prueba conex DBlocal
             $conexion = new conBD();
		if ($conexion->conectar()) {
			//En base de datos, las cedulas se completan con ceros a la izq. hasta un maximo de 7 digitos
			
                        $nroCedula = padIzquierda($user, 7, "0");
			
			$persona = $conexion->obtenerDatosPersona($nroCedula);
			$conexion->desconectar();
			
			if ($persona->getNroDocumento() == "") {
				 $estado = 1;
				 $estadoMsg = "El nro de documento ingresado no existe";
				 echo '<pre>';print_r($estadoMsg); echo '</pre>';
                                 echo '<pre><strong>               "PERSONA NO ENCONTRADA"</strong></pre>';
                                 echo '<pre><strong>       Numero de Cedula:       SIN REGISTROS</strong></pre>';
                                 echo '<pre><strong>       Nombres:                SIN REGISTROS</strong></pre>';
                                 echo '<pre><strong>       Apellidos:              SIN REGISTROS</strong></pre>';
                                 echo '<pre><strong>       Fecha de Nacimiento:    SIN REGISTROS</strong></pre>';
                                 echo '<pre><strong>       Sexo:                   SIN REGISTROS</strong></pre>';
                                 echo '<pre><strong>       Nacionalidad:           SIN REGISTROS</strong></pre>';
                                 echo '<pre><strong>       Lugar de Nacimiento:    SIN REGISTROS</strong></pre>';
                                 echo '<pre><strong>       Domicilio:              SIN REGISTROS</strong></pre>';
                                 echo '<pre><strong>       Estado Civil:           SIN REGISTROS</strong></pre>';
			} else {
			
				$estado = 0;
				$estadoMsg = "OK";
				
                                 echo '<pre><strong>                         "DATOS"</strong></pre>';
                                 echo '<pre><strong>       Numero de Cedula:       '.$persona->getNroDocumento().'</strong></pre>';
                                 echo '<pre><strong>       Nombres:                '.utf8_encode($persona->getNombre()).'</strong></pre>';
                                 echo '<pre><strong>       Apellidos:              '.utf8_encode($persona->getApellido()).'</strong></pre>';
                                 echo '<pre><strong>       Fecha de Nacimiento:    '.$persona->getFechaNacimiento().'</strong></pre>';
                                 echo '<pre><strong>       Sexo:                   '.$persona->getSexo().'</strong></pre>';
                                 echo '<pre><strong>       Nacionalidad:           '.$persona->getNacionalidad().'</strong></pre>';
                                 echo '<pre><strong>       Lugar de Nacimiento:    '.$persona->getLugarNacimiento().'</strong></pre>';
                                 echo '<pre><strong>       Domicilio:              '.$persona->getNacionalidad().'</strong></pre>';
                                 echo '<pre><strong>       Estado Civil:           '.$persona->getEstadoCivil().'</strong></pre>';
                                 
                        }
         }else{
             $estado = 98;
			$estadoMsg = "Fallo general del sistema. Ha fallado la consulta a BD local. Por favor intente nuevamente mas tarde";
//		echo '<pre>';print_r($personaVacio); echo '</pre>';
                echo '<pre>';print_r($estadoMsg); echo '</pre>';              
         }
}
//         }
         
         ?>
    </body>
</html>
<?php
}
?>
