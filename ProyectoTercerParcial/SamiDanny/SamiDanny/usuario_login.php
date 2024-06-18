<?php
session_start();
include('clase.php');

$obj = new DBControl();


$usuario = $_POST["usuario"];
$pass     = $_POST["txtpassword"];

$sql =  "SELECT * FROM usuarios WHERE user = '" . $usuario . "' AND PASSWORD = '" . $pass . "'";

if ($obj->nfilas($sql) >= 1) {
    $_SESSION['usuario'] = $usuario;
    header("Location: admin/admin.php");
} else {
    echo "<script> alert('Usuario no existe');window.location= 'index.php' </script>";
}
