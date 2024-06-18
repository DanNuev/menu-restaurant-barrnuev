<?php
    require_once("../clase.php");

    $db = new DBControl();

    $name = $_POST['name'];
    $pre = $_POST['preci'];

    $rutacompleta = "./img/";
    $fileimg = opendir(".".$rutacompleta);
    if (isset($_FILES['img']) && $_FILES['img']['size'] > 0) {
        $sqlCode = "SELECT cod FROM productos ORDER BY cod DESC LIMIT 1";
        if ($result = $db->nfilas($sqlCode) > 0) {
            $result = $db->vaiQuery($sqlCode);
            $cod = $result[0]["cod"];

            $number = (int)substr($cod, 1);
            $new_number = $number + 1;
            $new_code = 'a' . str_pad($new_number, 6, '0', STR_PAD_LEFT);

            $_FILES['img']['name'] = $new_code . '.png';
        } else {
            $_FILES['img']['name'] = 'a000001.png';
            $new_code = "a000001";
        }

        $archivo         = $_FILES['img']['tmp_name'];
        $nombrearchivo     = $_FILES['img']['name'];
        $tipoarchivo     = GetImageSize($archivo);
        // 1=>'GIF'
        // 2=>'JPEG'
        // 3=>'PNG'	

        if (!move_uploaded_file($archivo, ".".$rutacompleta . $nombrearchivo)) {
            echo "<script> alert('Error.\\nNo se ha podido cargar el archivo.');</script>";
        }

        $code = $new_code;
        $img     = $rutacompleta . $_FILES['img']['name'];
        $sql = "INSERT INTO productos (cod, nom, pre, img) VALUES ('$code', '$name', '$pre', '$img')";

        $db->query($sql);
    } else {

    }

    // Consulta para actualizar la tabla


    // validar si la consulta dio un resultado

    // Cerrar la conexion
    $db->close();
    echo "<script> window.location.href= 'admin.php' </script>";
    exit();