<?php
session_destroy();
$ruta = ControladorGeneral::ctrRuta();
echo '<script>
		window.location = "'.$ruta.'";
	</script>';
	  	 	