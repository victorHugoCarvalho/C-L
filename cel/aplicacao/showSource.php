<?php
$file = $_GET['file'];

if (isset($_GET["file"]))
{    
    show_source($file);
    echo "<br><input type='button' value='Voltar' onclick='javascript:history.back();'/>";
}

?>
