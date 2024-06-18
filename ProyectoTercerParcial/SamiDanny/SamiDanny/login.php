<?php
session_start();
include('clase.php');
if (isset($_SESSION['user'])) {
    header('location: index.php');
} else {
    session_destroy();
}
?>

<html>

<head>
    <title>Document</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <div class="FormCajaLogin">

        <div class="FormLogin">
            <!-- formulario login -->
            <form method="POST" id="frmlogin" class="grupo-entradas" action="usuario_login.php">
                <h1>Iniciar sesión</h1>

                <div class="TextoCajas">• Ingresar usuario</div>
                <input type="text" name="usuario" class="CajaTexto" autocomplete="off" required>

                <div class="TextoCajas">• Ingresar contraseña</div>
                <input type="password" name="txtpassword" class="CajaTexto" autocomplete="off" required>

                <div>
                    <input type="submit" value="Iniciar sesión" class="BtnLogin" name="btningresar">
                </div>

            </form>
        </div>
    </div>

</body>
</html>