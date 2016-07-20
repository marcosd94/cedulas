<?php 

	require_once("persona.php");

	class conBD {
	
		private $idConexion;

		function conectar() {
			$servidor = "10.2.29.182";
			$usuariodb = "wssfp";
			$passdb = "w3bs3rv!c3";
			$db = "identificaciones";
			
			try {
			 
			 $this->idConexion = pg_connect("host=" . $servidor . " port=5432 user=" . $usuariodb . " password=" . $passdb . " dbname=" . $db);
			 
			 return $this->idConexion;
			}
			catch (Exception $e) {
				return 0;
			}
		}
		
		function desconectar() {
			pg_close($this->idConexion);
		}
		
		function obtenerDatosPersona($nroCedula) {
			$persona = new persona();
			
			$sql = "select cedula, nombres, apellido, fech_nacim, sexo, nacionalidad, lugar_nacim, domicilio, estado_civil from cedulas where cedula = '$nroCedula'";
			$datos = pg_query($this->idConexion, $sql);
			if (pg_num_rows($datos) > 0) {
			
				//la fecha de nacimiento la pasamos en formato ISO 8601 YYY-MM-DD 
				$registro = pg_fetch_array($datos);
				$persona->setNroDocumento($registro['cedula']);
				$persona->setNombre(utf8_decode($registro['nombres']));
				$persona->setApellido(utf8_decode($registro['apellido']));
				$persona->setFechaNacimiento($registro['fech_nacim']);
				$persona->setSexo($registro['sexo']);
				$persona->setNacionalidad($registro['nacionalidad']);
				$persona->setLugarNacimiento($registro['lugar_nacim']);
				$persona->setEstadoCivil($registro['estado_civil']);
				$persona->setDomicilio(utf8_decode($registro['domicilio']));
			} else {
				$persona->setNroDocumento("");
				$persona->setNombre("");
				$persona->setApellido("");
				$persona->setFechaNacimiento("");
				$persona->setSexo("");
				$persona->setNacionalidad("");
				$persona->setLugarNacimiento("");
				$persona->setEstadoCivil("");
				$persona->setDomicilio("");
			}
			
			return $persona;
			
		}
	
	}

?>