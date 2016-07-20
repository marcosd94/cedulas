<?php 

/*
 *  SCRIPT: wsPersonasSFP, Web Service WSDL para la busqueda de datos de personas en la
 *  base de datos de indentificaciones (ON-LINE) como primera opcion, y en la base de datos
 *  de cedulas de la SFP como segunda opcion o respaldo
 *  
 *  AUTOR: David Espinoza
 *  
 *  METODOS DISPONIBLES
 *  	- Obtener obtenerDatosPersona()
 *  		Parametro de Entrada --> nroCedula (tipo cadena)
 *  		Retorna 			 --> estado (Int), estadoMsg (Cadena), persona (estructura de datos) 
 * 
 */

require_once("nusoap/nusoap.php");
require_once("persona.php");
require_once("conBD.php");
require_once("funciones.php");

//Definimos el tratamiento de errores (Para desarrollo poner display_errors ON!!)
error_reporting(E_ALL);
ini_set('display_errors','Off');

//Definimos el namespace, con nuestra URL
//$namespace = "http://www.sfp.gov.py/wsIdentificacionesSFP/wsdl";
$namespace = "http://10.2.29.254/wsIdentificacionesSFP/wsdl";



//Comenzamos a configurar el servidor
$server = new soap_server();
$server->debug_flag = false;
$server->configureWSDL("wsIdentificacionesSFP", $namespace);
$server->wsdl->schemaTargetNamespace = $namespace;

//Registramos la estructura de datos "personas"
$server->wsdl->addComplexType(
  'personas',
  'complexType',
  'struct',
  'all',
  '',
  array(
    'nroDocumento' => array('name' => 'nroDocumento',
         'type' => 'xsd:string'),
    'nombre' => array('name' => 'nombre',
         'type' => 'xsd:string'),
    'apellido' => array('name' => 'apellido',
        'type' => 'xsd:string'),
  	'fechaNacimiento' => array('name' => 'fechaNacimiento',
        'type' => 'xsd:string'),
    'sexo' => array('name' => 'sexo',
        'type' => 'xsd:string'),
  	'nacionalidad' => array('name' => 'nacionalidad',
        'type' => 'xsd:string'),
  	'lugarNacimiento' => array('name' => 'lugarNacimiento',
        'type' => 'xsd:string'),
  	'domicilio' => array('name' => 'domicilio',
        'type' => 'xsd:string'),
  	'estadoCivil' => array('name' => 'estadoCivil',
        'type' => 'xsd:string')
  )
);

//Registramos la estructura de datos "respuestaObtenerDatosPersona"
$server->wsdl->addComplexType(
  'respuestaObtenerDatosPersona',
  'complexType',
  'struct',
  'all',
  '',
  array(
    'estado' => array('name' => 'estado',
         'type' => 'xsd:int'),
    'estadoMsg' => array('name' => 'estadoMsg',
         'type' => 'xsd:string'),
  	'origenDatos' => array('name' => 'origenDatos',
         'type' => 'xsd:string'),
    'persona' => array('name' => 'persona',
        'type' => 'tns:personas')
  )
);

//Definimos el metodo obtenerDatosPersona
function obtenerDatosPersona($nroCedula) {

	$paso = 1;
	$estado = 99;
	$estadoMsg = "Error Desconocido";
	
	//Creamos un array de persona vacio
	$personaVacio = array(
					"nroDocumento" => "",
					"nombre" => "",
					"apellido" => "",
					"fechaNacimiento" => "",
					"sexo" => "",
					"nacionalidad" => "",
					"lugarNacimiento" => "",
					"domicilio" => "",
					"estadoCivil" => ""
				);
	
	//Como primera opcion nos conectamos al web service de la SENATICS
	if ($paso == 1) {
             $estado = 0;
             $estadoMsg = "OK";
             $login = array(
             'login' => 'sfpconsulta',
             'password' => 'sfp.570Sfp',
             'exceptions' => 0);
             $url= 'http://sii.senatics.gov.py:8080/mbohape/PersonaWS?wsdl';
             $ch=curl_init($url);
             curl_setopt($ch, CURLOPT_TIMEOUT, 5);
             curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
             $data=curl_exec($ch);
 
             # capturamos el codigo HTTP devuelto por el servidor
             $httpcode=curl_getinfo($ch, CURLINFO_HTTP_CODE);
 
             curl_close($ch);
             if ($httpcode!=0){
                 if(strpos($nroCedula,'A') != NULL||strpos($nroCedula,'B') != NULL||strpos($nroCedula,'C') != NULL){
                    while( strlen($nroCedula)<8){
                    $nroCedula = '0'.$nroCedula;
                        }
                 }
                 $ClienteSOAP= new SoapClient($url, $login);
                 $Parametros = array('nroCedula'=> $nroCedula);
                 $Respuesta = $ClienteSOAP->findPersonaByCedula($Parametros);
                 if ($Respuesta->return->nombres){
                    $datosPersona = array(
					"nroDocumento" => $Respuesta->return->cedula,
					"nombre" => trim($Respuesta->return->nombres,' '),
					"apellido" => trim($Respuesta->return->apellido,' '),
					"fechaNacimiento" => substr($Respuesta->return->fechNacim, 0, 10),
					"sexo" => $Respuesta->return->sexo,
					"nacionalidad" => $Respuesta->return->nacionalidadBean,
					"lugarNacimiento" => "",
					"domicilio" => "",
					"estadoCivil" => $Respuesta->return->estadoCivil
				);
				
				return  array(
						"estado" => $estado,
						"estadoMsg" => $estadoMsg,
						"origenDatos" => "BDIdentificaciones",
						"persona" => $datosPersona
						);
                 }else{
                     if ($Respuesta->error == 'Persona no encontrada.'){
                     $estado = 1;
                     $estadoMsg = "El nro de documento ingresado no existe";
                     return array(
						"estado" => $estado,
						"estadoMsg" => $estadoMsg,
						"origenDatos" => "BDIdentificaciones",
						"persona" => $personaVacio
						);
                     }else{//Parametros invalidos? VALIDAR EN EL FORMULARIO DEL SICCA ANTES DE TRAER LOS DATOS
                        $paso = 2;
                     }
                }
        
             }else{
                 $paso = 2;
             }
        }
	
	//Como segunda opcion nos conectamos a la base de datos local de la SFP
	if ($paso == 2) {
		$conexion = new conBD();
		if ($conexion->conectar()) {
		
			//En base de datos, las cedulas se completan con ceros a la izq. hasta un maximo de 7 digitos
			$nroCedula = padIzquierda($nroCedula, 7, "0");
			
			$persona = $conexion->obtenerDatosPersona($nroCedula);
			$conexion->desconectar();
			
			if ($persona->getNroDocumento() == "") {
				$estado = 1;
				$estadoMsg = "El nro de documento ingresado no existe";
				
				return  array(
						"estado" => $estado,
						"estadoMsg" => $estadoMsg,
						"origenDatos" => "BDSFP",
						"persona" => $personaVacio
						);
			} else {
			
				$estado = 0;
				$estadoMsg = "OK";
				
				$datosPersona = array(
					"nroDocumento" => $persona->getNroDocumento(),
					"nombre" => $persona->getNombre(),
					"apellido" => $persona->getApellido(),
					"fechaNacimiento" => $persona->getFechaNacimiento(),
					"sexo" => $persona->getSexo(),
					"nacionalidad" => $persona->getNacionalidad(),
					"lugarNacimiento" => $persona->getLugarNacimiento(),
					"domicilio" => $persona->getDomicilio(),
					"estadoCivil" => $persona->getEstadoCivil()
				);
				
				return  array(
						"estado" => $estado,
						"estadoMsg" => $estadoMsg,
						"origenDatos" => "BDSFP",
						"persona" => $datosPersona
						);
			}
			
		
		} else {
			$estado = 98;
			$estadoMsg = "Fallo general del sistema. Ha fallado la consulta a BD local. Por favor intente nuevamente mas tarde";
		
			return  array(
						"estado" => $estado,
						"estadoMsg" => $estadoMsg,
						"origenDatos" => "",
						"persona" => $personaVacio
						);
		}
	}
 	
	

	return $estado;
}

//Registramos la funcion ObtenerDatosPersona en el servidor
$server->register('obtenerDatosPersona',        // nombre del metodo
  array('nroCedula' => 'xsd:string'),          // parametros de entrada
  array('return' => 'tns:respuestaObtenerDatosPersona'),    // parametros de salida
  $namespace,                         // namespace
  $namespace . '#obtenerDatosPersona',       // soap action
  'rpc',                                    // style
  'literal',                                // use
  'Obtiene los datos de la persona a partir del nro de documento'  // Alguna observacion
);

//Levantamos el servidor WSDL
$HTTP_RAW_POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA'])
  ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';
$server->service($HTTP_RAW_POST_DATA);
exit();

?>
