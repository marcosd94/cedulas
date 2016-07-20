<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>CONSULTAR</title>
        <link href="style.css" rel="stylesheet" type="text/css">
        <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
        <link rel="shortcut icon" href="favicon.ico">
    </head>
    <body style="font-family: sans-serif; color: #47626F ;background-color: #E5E5E5">
        <table style="background-color: #fff" align="center" width="30%">
            <tr>
		<td align="center">
        <div class="contenedor">
            <img src=" hola.png" alt="">
            <h1>CONSULTAS </h1>
            <div id= "resultado"></div>
            <div id=" formulario">
                <form method="POST" action="return false" onsubmit="return false">
                <p><input type="text" value="" id="user" name="user" placeholder="NUMERO DE CEDULA"></p>
                <p><button onclick="consultar(document.getElementById('user').value);" >CONSULTAR DATOS</button></p>
            </form>
        </div>
        <script>
           function consultar(user)
           {
           $.ajax({
               url: 'consultar.php',
               type: 'POST',
               data:'user='+user,
               success: function(resp){
                   $('#resultado').html(resp);
               }
           });
           } 
        </script>
        </div>
        <table align="center" style="border-top:#999999;border-top-style:groove;position:relative;" width="100%">
	<tr>
		<td>
			<p align="center" style="color: #002448;font-size:12px;">
                            Direcci&oacute;n: Constituci&oacute;n esq. 25 de Mayo - Telefax: 451 925 / 492 109 <br />
                            Asunci&oacute;n - Paraguay <br />
                            Cont&aacute;ctenos: sfp@sfp.gov.py
			</p>
		</td>
	</tr>
</table>
    </body>
</html>
