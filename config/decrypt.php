<?php
require_once("encrypt.php");

if(isset($_POST['encrypted'])) {
    $encrypted = $_POST['encrypted'];
    echo Encryption::decrypt($encrypted);
}
?>