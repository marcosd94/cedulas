<?php 

	class persona {
	
		private $nroDocumento;
		private $nombre;
		private $apellido;
		private $fechaNacimiento;
		private $sexo;
		private $nacionalidad;
		private $lugarnacimiento;
		private $domicilio;
		private $estadocivil;
		
		function setNroDocumento($nroCedula){
			$this->nroDocumento = $nroCedula;
		}
		
		function getNroDocumento(){
			return $this->nroDocumento;
		}
		
		function setNombre($nombre){
			$this->nombre = $nombre;
		}
		
		function getNombre(){
			return $this->nombre;
		}
		
		function setApellido($apellido){
			$this->apellido = $apellido;
		}
		
		function getApellido(){
			return $this->apellido;
		}
		
		function setFechaNacimiento($fechaNacimiento){
			$this->fechaNacimiento = $fechaNacimiento;
		}
		
		function getFechaNacimiento(){
			return $this->fechaNacimiento;
		}
		
		function setSexo($sexo){
			$this->sexo = $sexo;
		}
		
		function getSexo(){
			return $this->sexo;
		}
		
		function setNacionalidad($nacionalidad){
			$this->nacionalidad = $nacionalidad;
		}
		
		function getNacionalidad(){
			return $this->nacionalidad;
		}
		
		function setLugarNacimiento($lugarnacimiento){
			$this->lugarnacimiento = $lugarnacimiento;
		}
		
		function getLugarNacimiento(){
			return $this->lugarnacimiento;
		}
		
		function setDomicilio($domicilio){
			$this->domicilio = $domicilio;
		}
		
		function getDomicilio(){
			return $this->domicilio;
		}
		
		function setEstadoCivil($estadocivil){
			$this->estadocivil = $estadocivil;
		}
		
		function getEstadoCivil(){
			return $this->estadocivil;
		}
		
	
	}

?>