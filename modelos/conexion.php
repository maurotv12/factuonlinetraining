<?php
/**
@grcarvajal grcarvajal@gmail.com **Gildardo Restrepo Carvajal**
25/06/2020 CursosApp
 */
class Conexion
{
	static function conectar()
	{
		 $link = new PDO("mysql:host=localhost;dbname=cursoscalibelula",
		 				"root",
		 				"");
		 	$link->exec("set names utf8");
			return $link;

			// Datos de hosting
			 // $link = new PDO("mysql:host=localhost;dbname=calibelu_b3luFesC4l1",
			 // 			"calibelu_c4l1b3",
			 // 			"aB@E%yGVcos");
			//$link->exec("set names utf8");
			//return $link;
	}
}