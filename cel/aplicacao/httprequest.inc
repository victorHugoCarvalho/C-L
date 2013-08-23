<?php
reset ($_GET);
while (list ($chave, $valor) = each ($_GET)) {
	$a = $chave;
	$$a = $valor;
}

reset ($_POST);
while (list ($chave, $valor) = each ($_POST)) {
	$a = $chave;
	$$a = $valor;
}

?>
