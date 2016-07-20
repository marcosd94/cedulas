<?php 

function padIzquierda($texto, $cantCaracteres, $relleno) {

	if (strlen($texto) >= $cantCaracteres) {
		return $texto;
	} else {
		while(strlen($texto) < $cantCaracteres) {
			$texto = $relleno . $texto;
		}
		
		return $texto;
	}
 
}

?>