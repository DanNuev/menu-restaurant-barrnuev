<?php
    require_once '../clase.php';
    $obj = new DBControl();
    $id = $_GET['id'];
    $sql = "DELETE FROM productos WHERE id='$id'";
    $obj->query($sql);
    $obj->close();
    header("Location:./admin.php");
