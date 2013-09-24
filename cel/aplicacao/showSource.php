<?php
$file = $_GET['file'];

if (isset($HTTP_GET_VARS["file"]))
{    
    show_source($file);
    echo "<br><input type='button' value='Voltar' onclick='javascript:history.back();'/>";
}

?>
