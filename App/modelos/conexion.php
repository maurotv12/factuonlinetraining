<?php
class Conexion
{

	public static function conectar()
	{
		$link = new PDO(
			"mysql:host=localhost;dbname=cursoo_bd_ac",
			"root",
			""
		);
		$link->exec("set names utf8");
		return $link;
	}
}
