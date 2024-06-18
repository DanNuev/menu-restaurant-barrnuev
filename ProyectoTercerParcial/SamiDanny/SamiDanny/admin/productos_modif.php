<?php
    require_once("../clase.php");

    $db = new DBControl();

    $id = $_POST['id'];
    $name = $_POST['nom'];
    $pre = $_POST['pre'];

    $rutacompleta = "./img/";
    $fileimg = opendir($rutacompleta);

    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $sql = "SELECT cod, img FROM productos WHERE id = '$id'";
        $res = $db->vaiQuery($sql);

        $oldRut = $res[0]['img'];
        $oldCod = $res[0]['cod'];

        /*$sqlCode = "SELECT cod FROM productos ORDER BY cod DESC LIMIT 1";
        if ($result = $db->nfilas($sqlCode) > 0) {
            $result = $db->vaiQuery($sqlCode);
            $cod = $result[0]["cod"];

            $number = (int)substr($cod, 1);
            $new_number = $number + 1;
            $new_code = 'a' . str_pad($new_number, 6, '0', STR_PAD_LEFT);

            $_FILES['img']['name'] = $new_code . '.png';
        } else {
            $_FILES['img']['name'] = 'a000001.png';
        }*/

        $_FILES['image']['name'] = $oldCod . ".png";

        $archivo         = $_FILES['image']['tmp_name'];
        $nombrearchivo     = $_FILES['image']['name'];
        $tipoarchivo     = GetImageSize($archivo);
        // 1=>'GIF'
        // 2=>'JPEG'
        // 3=>'PNG'	

        if (move_uploaded_file($archivo, ".".$rutacompleta . $nombrearchivo)) {
            echo "La imagen " . $nombrearchivo . " ha sido subida.";

            if (file_exists($oldRut)) {
                if (unlink($oldRut)) {
                    echo "<script>alert('La imagen antigua ha sido eliminada.')</script>";
                } else {
                    echo "<script>alert('No se pudo eliminar la imagen antigua.')</script>";
                }
            }
        } else {
            echo "<script> alert('Error.\\nNo se ha podido cargar el archivo.');</script>";
        }
    }

    $sql = " UPDATE productos SET nom = '$name', pre = '$pre' WHERE id = '$id'";
    $db->query($sql);

    // Consulta para actualizar la tabla


    // validar si la consulta dio un resultado

    // Cerrar la conexion
    $db->close();

    echo "<script> window.location.href= 'admin.php' </script>";
    exit();

