<?php
class DBControl {
	private $conn;

	function __construct() {
		$this->conn = $this->conectarDB();
	}

	function conectarDB() {
		$conn = mysqli_connect("localhost", "id22199869_sada1725", "caNijo17@/", "id22199869_vaicarrito1");
		return $conn;
	}

	function vaiquery($query) {
		$resultado = mysqli_query($this->conn, $query);
		while ($fila = mysqli_fetch_assoc($resultado)) {
			$obtener_resultado[] = $fila;
		}
		if (!empty($obtener_resultado)) {
			return $obtener_resultado;
		}
	}

	function query($query) {
		mysqli_query($this->conn, $query);
	}

	function nfilas($query)	{
		$resultado  = mysqli_query($this->conn, $query);
		$totalfilas = mysqli_num_rows($resultado);
		return $totalfilas;
	}

	function close() {
		mysqli_close($this->conn);
	}
}
