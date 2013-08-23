<?php

//	@session_destroy();
//	session_unset();
	include("script_bd2.php");
	session_start();

	if( isset( $_SESSION['id_projeto_corrente']))
	{
		$_SESSION['id_projeto'] = $_SESSION['id_projeto_corrente'];
	}
	else 
	{
		print("<b> PROJETO NÃO SELECIONADO </b><br>");
		exit();
	}
	
	unset( $_SESSION["conceito"] );
	unset( $_SESSION["disjoint"] );
	unset( $_SESSION["exist"] );
	unset( $_SESSION["finish_insert"] );
	unset( $_SESSION["finish_relation"] );
	unset( $_SESSION["funcao"] );
	unset( $_SESSION["impact"] );
	unset( $_SESSION["ind"] );
	unset( $_SESSION["index1"] );
	unset( $_SESSION["index2"] );
	unset( $_SESSION["index3"] );
	unset( $_SESSION["index4"] );
	unset( $_SESSION["index5"] );
	unset( $_SESSION["index6"] );
	unset( $_SESSION["index7"] );
	unset( $_SESSION["insert"] );
	unset( $_SESSION["insert_relation"] );
	unset( $_SESSION["job"] );
	unset( $_SESSION["lista"] );
	unset( $_SESSION["lista_de_axiomas"] );
	unset( $_SESSION["lista_de_conceitos"] );
	unset( $_SESSION["lista_de_estado"] );
	unset( $_SESSION["lista_de_objeto"] );
	unset( $_SESSION["lista_de_relacoes"] );
	unset( $_SESSION["lista_de_sujeito"] );
	unset( $_SESSION["lista_de_verbo"] );
	unset( $_SESSION["main_subject"] );
	unset( $_SESSION["nome1"] );
	unset( $_SESSION["nome2"] );
	unset( $_SESSION["nome3"] );
	unset( $_SESSION["reference"] );
	unset( $_SESSION["salvar"] );
	unset( $_SESSION["tipos"] );
	unset( $_SESSION["translate"] );
	unset( $_SESSION["verbos_selecionados"]);
	unset( $_SESSION["predicados_selecionados"]);
	unset( $_SESSION['lista_de_sujeito_e_objeto']);

	session_unregister( "conceito" );
	session_unregister( "disjoint" );
	session_unregister( "exist" );
	session_unregister( "finish_insert" );
	session_unregister( "finish_relation" );
	session_unregister( "funcao" );
	session_unregister( "impact");
	session_unregister( "ind");
	session_unregister( "index1" );
	session_unregister( "index2" );
	session_unregister( "index3" );
	session_unregister( "index4" );
	session_unregister( "index5" );
	session_unregister( "index6" );
	session_unregister( "index7" );
	session_unregister( "insert" );
	session_unregister( "insert_relation" );
	session_unregister( "job");
	session_unregister( "lista" );
	session_unregister( "lista_de_axiomas" );
	session_unregister( "lista_de_conceitos" );
	session_unregister( "lista_de_estado" );
	session_unregister( "lista_de_objeto" );
	session_unregister( "lista_de_relacoes" );
	session_unregister( "lista_de_sujeito" );
	session_unregister( "lista_de_verbo" );
	session_unregister( "main_subject" );
	session_unregister( "nome1");
	session_unregister( "nome2");
	session_unregister( "nome3");
	session_unregister( "reference" );
	session_unregister( "salvar" );
	session_unregister( "tipos" );
	session_unregister( "translate" );
	session_unregister( "verbos_selecionados" );
	session_unregister( "predicados_selecionados" );
	session_unregister( "lista_de_sujeito_e_objeto" );
	
	?> 
	<SCRIPT language='javascript'>
		document.location = "auxiliar_interface.php";
	</SCRIPT>
	<?php
	
?>