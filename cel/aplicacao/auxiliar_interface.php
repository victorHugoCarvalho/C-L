<?php
include_once "estruturas.php";
include_once 'auxiliar_algoritmo.php';
//include "auxiliar_bd.php";
session_start();
?>
<html>
  <head>
     <title>Algoritmo de Gera&ccedil;&atilde;o de Ontologias</title>
    <style>

    </style>
  </head>


<body>
<table height=100% width=100% border=0>
<tr>

<td width='200'>
<iframe src='arv_frameset.htm' height="100%" width='200'>
</iframe>
</td>

<td align='left' valign='top'>
<?php

/*
Cenario:     Verificar com o usuario a existencia de um nome numa lista.
Objetivo:    Verificar com o usuario a existencia de um nome numa lista.
Contexto:    Algoritmo de traducão iniciado.
Atores:      Usuario.
Recursos:    Sistema, nome, lista.
Episodios:
- Posta true ou false, caso true posta o indice junto, caso false o indice e ignorado.
*/  


function exist($name, $list)
{
	$indice = -1;
	foreach ($list as $key=>$palavra)
	{
		if( strstr($name, $palavra) )
		{
			$indice = $key;
			break;
		}
	}
        ?>
<form method="POST" action="algoritmo.php" id="exist_form">

			<?php
			if (count($_SESSION["verbos_selecionados"])!=0)
			{
				echo "Propriedades já cadastradas para esse impacto:<p>";
				foreach($_SESSION["verbos_selecionados"] as $verbo)
				{
					echo "   - " . $verbo . "<br>";
				}
			}
			?>
			<b>Impacto:</b> <code>"<?=$name?>"</code><br><br>
			<b>A propriedade já está cadastrada na lista abaixo?</b><br>
			<SELECT id='indice' name='indice' size=10 width="300">
			 <?php
			 foreach ($list as $key=>$termo)
			 {
				?>
				<OPTION value='<?=$key ?>' <?php if($indice==$key) echo "selected" ?> > <?php echo $termo ?></OPTION>
				<?php
			 }
			 ?>
			<OPTION value="-1"></OPTION>
			</SELECT><br>
			<input type="radio" onClick="seExiste('TRUE')" value="TRUE" id="existe" name="existe" size="20" <?php if($indice != -1) echo"checked" ?>> sim
			<input type="radio" onClick="seExiste('FALSE')" value="FALSE" id="existe" name="existe" size="20" <?php if($indice == -1) echo"checked" ?> > não <BR>
			<input type="text" value="<?php print strip_tags($name) ?>" id="nome" name="nome" size="20">
			<input type="submit" value="Inserir Propriedade" id="B1" name="B1" disabled><BR>
			<INPUT type="button" value="Próximo Passo >>" name="B2" onClick="fim()">
			</p>
            <script language="JavaScript">
				function seExiste(valor)
				{
					var nome = document.all.nome;
					var indice = document.all.indice;
					if(valor=='TRUE')
					{
						nome.disabled = true;
						indice.disabled = false;
					}
					else
					{
						nome.disabled = false;
						indice.disabled = true;
					}
				}
				<?php
				if($indice != -1)
					echo "seExiste('TRUE');";
				else
					echo "seExiste('FALSE');";
				?>

				function fim()
				{
					if(<?=count($_SESSION["verbos_selecionados"])?>!=0)
					{
						document.all.indice.disabled = false;
						document.all.indice.selectedIndex=<?=count($list)?>;
						document.all.existe[0].checked=true;
						var form = document.getElementById("exist_form");
						form.submit();
					}
					else
						alert("É necessario ao menos um verbo para cada impacto.");

				}


            </script>
         	<SCRIPT language="JavaScript">
         		var form = document.getElementById("exist_form");
                	form.B1.disabled = false;
         	</SCRIPT>
            </form>
         <?php
         //Registra a variavel de controle.
         $_SESSION["exist"] = 1;

}

/*
Cenario:    Verificar a importancia de um termo com ajuda do usuario.
Objetivo:    Verificar a importancia de um termo com ajuda do usuario.
Contexto:    Algoritmo de traducão iniciado.
Atores:        Usuario.
Recursos:    Sistema, termo.
Episodios:
- Retorna TRUE se a importancia e central.
*/
function importancia_central($termo, $impactos)
{

	?>
	<h3>Termo: <?=$termo?></h3><br>
	<?php
	print("Impactos:<br>");

	foreach ($impactos as $impacto)
	{
		if( trim($impacto) == "" )
		continue;
		print(" - $impacto <br>");
	}
	print("O termo $termo vai transformar-se em:<br>");
        ?>
            <form method="POST" action="algoritmo.php">
              <input type="radio" value="TRUE" name="main_subject" size="20" checked> Conceito
              <input type="radio" value="FALSE" name="main_subject" size="20"> Propriedade
              <input type="submit" value="OK" name="B1" size="20">
              </p>
            </form>
         <?php
         $_SESSION["main_subject"] = 1;
}


/* -------- INCERTO --------
Cenario:    Verificar se um conceito referencia outro.
Objetivo:   Verificar se um conceito referencia outro.
Contexto:   Algoritmo de traducão iniciado.
Atores:     Usuario.
Recursos:   Conceito1, conceito2.
Episodios:
            - Retorna TRUE se referencia.
*/
//function faz_referencia($con1, $con2)
function faz_referencia($conceitos, $subconceitos)
{
	?>



        <form method="POST" action="algoritmo.php" id="reference_form" name="reference_form">
        <table border="1" cellspacing="1" width="100%" id="AutoNumber1">
  		<tr>
    	<td width="100%" colspan="2"><?php print("Organizar Hierarquia <br>") ?></td>
  		</tr>
  		<tr>
    	<td width="50%">
    		<?php

    			foreach ($conceitos as $conc)
    			{
					$concsel = $conc; //trocar
    				print("<INPUT type='radio' name='pai' id='pai' value='$conc->nome' onFocus='Salvar()'> <b> $conc->nome </b> <br>\n");
					foreach($conc->subconceitos as $subc)
					{
						print("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-$subc<br>");
					}
    			}
    		?>
    	</td>
    	<td width="50%" align="left" valign="top">
    		<?php

    			foreach ($conceitos as $key=>$conc)
    			{
					$sel = false;
					$existe = array_search($conc->nome, $concsel->subconceitos);
    				if( $existe !== false )
						$sel = true;

    				print("<INPUT type='checkbox' id='$key' name='$key' value='$conc->nome' if($sel)checked> <b> $conc->nome </b> <br>\n");
    			}
    		?>
    	</td>
  		</tr>
		</table>
		<input type="submit" value="Incluir Relacao" name="B1" size="20">
		<input type="button" value="Finalizar Algoritmo" name="B2" onClick='Sair()'>
		</FORM>

		<script>
		document.reference_form.pai[0].checked = true;
		function Sair()
		{
			var salvar = window.confirm("Voce deseja salvar os subconceitos selecionados?");
			if(!salvar)
			{
				for (var i=0; i < document.reference_form.pai.length; i++)
				{
					if (document.reference_form.pai[i].checked)
					{
						document.reference_form.pai[i].checked = false;
					}
				}
				var form = document.getElementById("reference_form");
				form.submit();
			}
		}

		function Salvar()
		{
		/*
			var radio = document.getElementById('pai');
			var i = 0;
			var checked = false;
			alert(radio.checked);

			for( i = 0; i < radio.length; ++i )
			{
				if( radio[i].selected == true )
				{
					checked = true;
				}
			}

			if( checked )
			{
				var salvar = window.confirm("Voce deseja salvar os subconceitos selecionados?");
				if(salvar)
				{
					var form = document.getElementById("reference_form");
					form.submit();
				}
				else
				{*/
					<?php
						foreach ($conceitos as $key=>$conc)
						{
							print("var check = document.getElementById('$key');\n");
							print("check.checked = false;\n");
						}
					?>
				/*}
			}
			*/
		}
		</script>

        <?php
        $_SESSION["reference"] = 1;
}


/* -------- INCERTO --------
Cenario:    Insere tipo.
Objetivo:    Deixar o bd consistente.
Contexto:    .
Atores:        Usuario.
Recursos:    lista de termos sem tipo.
Episodios:
- .
*/
function insere_tipo ($list)
{
		$_SESSION["tipos"] = 2;
        ?>
        <form method="POST" action="auxiliar_bd.php">
            <p>
        <?php
        foreach( $list as $key=>$termo )
        {
        	echo ("<br> $termo <br>");
            ?>

            Sujeito <input type="radio" value="sujeito" name="type<?php echo $key ?>" size="20" checked>
            Objeto <input type="radio" value="objeto" name="type<?php echo $key ?>" size="20">
            Verbo <input type="radio" value="verbo" name="type<?php echo $key ?>" size="20">
            Estado <input type="radio" value="estado" name="type<?php echo $key ?>" size="20">

            <?php
        }
        ?>
            <input type="submit" value="Enviar" name="B1" size="20">
            </p>
        </form>
        <?php
        $_SESSION["tipos"] = 1;
}


function insere_relacao ($rel, $conc, $imp, $list)
{
	$_SESSION["insert_relation"] = 1;
	$indice = strpos($imp, $rel);
	$tam = strlen($rel);
	$pred = trim(strip_tags(substr($imp,$indice + $tam)));
	$tam = strlen($pred);

	if($pred{$tam-1} == ".")
	{
		$pred{$tam-1} = "\0";
	}
	if( ($pred{0} == "a" || $pred{0} == "o") && $pred{1} == ' ')
	{
		$pred{0} = " ";
		$pred = trim($pred);
	}

	//$pred = explode($rel, $imp);
	$indice2 = -1;
	foreach ($list as $key=>$palavra)
	{
		if(trim($palavra->nome) !== "")
		{
			if( strstr($pred, $palavra->nome) )
			{
				if ( array_search($palavra->nome, $_SESSION["predicados_selecionados"]) === false )
				{
					$indice2 = $key;
					break;
				}
			}
		}
	}

	$indice3 = -1;
	foreach ($_SESSION["lista_de_sujeito_e_objeto"] as $key=>$palavra)
	{
		if( strstr($pred, $palavra->nome) && ( array_search($palavra->nome, $_SESSION["predicados_selecionados"]) === false ))
		{
			$indice3 = $key;
			break;
		}
	}

    ?>
 	<form method="POST" nome="rel_form" id="rel_form" action="algoritmo.php">
	<?php
			print "<H3>Conceito: $conc</H3>";
			print "<H4>Propriedade:	 $rel </H4><BR>";
			if (count($_SESSION["predicados_selecionados"])!=0)
			{
				echo "Predicados já cadastrados para essa propriedade:<p>";
				foreach($_SESSION["predicados_selecionados"] as $verbo)
				{
					echo "- " . $verbo . "<br>";
				}
			}
	?>

			<B>Impacto:</B><CODE> <?=$imp?></CODE><br>

<BR>
			<b>O predicado da relação já está cadastrado na lista abaixo?</b><br>

			<SELECT id='indice' name='indice' size=10 width="300">
			 <?php
			 foreach ($list as $key=>$termo)
			 {
				?>
				<OPTION value='<?=$key ?>' <?php if($indice2===$key) echo "selected" ?> > <?php echo $termo->nome ?></OPTION>
				<?php
			 }
			 ?>
			<OPTION value="-1"></OPTION>
			</SELECT><br>

			<input type="radio" onClick="seExiste('TRUE')" value="TRUE" id="existe" name="existe" size="20" <?php if($indice2!==-1) echo"checked" ?>> sim
			<input type="radio" onClick="seExiste('FALSE')" value="FALSE" id="existe" name="existe" size="20" <?php if($indice2===-1) echo"checked" ?> > não <BR>

			<DIV id=naoExiste>
<BR>
			<b>Se não existe, ele pertence à lista de elementos do nosso namespace(abaixo)?</b><br>
			<SELECT onChange='seleciona(this[this.selectedIndex].text)' size=10 width="300">
			 <?php
			 $selected=false;
			 foreach ($_SESSION["lista_de_sujeito_e_objeto"] as $key=>$termo)
			 {
				?>
				<OPTION value='<?=$termo->nome ?>' <?php if($indice3===$key){echo "selected";$selected=$termo->nome;} ?> ><?php echo $termo->nome ?></OPTION>
				<?php
			 }
			 ?>
			</SELECT><br>

			<table>
			<tr>
			<td>predicado:</td><td><input type="text" value="<?php print strip_tags($pred) ?>" id="nome" name="nome" size="20"></td>
			</tr>
			<tr>
			<td>namespace:</td><td><input type="text" value="proprio" id="namespace" name="namespace" size="20"></td>
			</tr>
			</table>

			</div>
			<input type="submit" value="Inserir Predicado" id="B1" name="B1" size="20">
			<INPUT type="button" value="Próximo Passo >>" name="B2" onClick="fim()">
			</p>
            <script language='javascript'>
				function seleciona(valor)
				{
					document.all.nome.value = valor;
					document.all.namespace.value = 'proprio';
				}

				function fim()
				{
					if(<?=count($_SESSION["predicados_selecionados"])?>!=0)
					{
						document.all.indice.disabled=false;
						document.all.indice.selectedIndex=<?=count($list)?>;
						document.all.existe[0].checked=true;
	            		var form = document.getElementById("rel_form");
						form.submit();
					}
					else
						alert('É necessário ao menos um predicado para cada verbo');
				}

				function seExiste(valor)
				{
					var indice = document.all.indice;
					var naoExiste = document.all.naoExiste;
					if(valor=='TRUE')
					{
						indice.disabled	= false;
						naoExiste.style.display = 'none';
					}
					else
					{
						indice.disabled	= true;
						naoExiste.style.display = 'block';
					}
				}

				<?php
				if($indice2!==-1)
					echo "seExiste('TRUE');";
				else
					echo "seExiste('FALSE');";

				if($selected !== false)
					print("\n document.all.nome.value = '$selected';");
				?>
            </script>
		</form>
        <?php
        $_SESSION['insert_relation'] = 1;
}


function disjuncao( $nome, $list )
{
	$_SESSION["disjoint"] = 1;

	if (count($_SESSION["axiomas_selecionados"])!=0)
	{
		echo "Termos disjunos já discriminados para esse conceito:<p>";
		foreach($_SESSION["axiomas_selecionados"] as $axioma)
		{
			echo "- " . $axioma . "<br>";
		}
		echo "</p>";
	}

	print "Existe algum termo disjunto do conceito <b>$nome</b> na lista abaixo ou no vocabulário mínimo?";

        ?>
            <form id='rel_form' name='rel_form'  method="POST" action="algoritmo.php">
            <p>
				<SELECT onChange='seleciona(this[this.selectedIndex].text)' name="indice" size=10 width="300">
				 <?php
				 foreach ($_SESSION["lista_de_sujeito_e_objeto"] as $key=>$termo)
				 {
				 	if ( strcmp( $termo->nome, $nome ) != 0 )
				 	{
					?>
					<OPTION value='<?=$termo->nome ?>'><?php echo $termo->nome ?></OPTION>
					<?php
					}
				 }
				 ?>
				</SELECT><br>

				<table>
				<tr>
				<td>conceito:</td><td><input type="text" value="<?php print strip_tags($pred) ?>" id="nome" name="nome" size="20"></td>
				</tr>
				<tr>
				<td>namespace:</td><td><input type="text" value="proprio" id="namespace" name="namespace" size="20"></td>
				</tr>
				</table>
			<input type="radio" onClick="seExiste('TRUE')" value="TRUE" name="existe" size="20"> sim
			<input type="radio" onClick="seExiste('FALSE')" value="FALSE" name="existe" size="20" checked> não<br>
			<input type="button" value="Inserir Axioma" id="B1" name="B1" onClick="insere()">
			<INPUT type="button" value="Próximo Passo >>" name="B2" onClick="fim()">
			</p>
            <script language='javascript'>
				function seleciona(valor)
				{
					document.all.nome.value = valor;
					document.all.namespace.value = 'proprio';
				}

				function insere()
				{
					if ( document.all.nome.value == '' )
					{
						alert('Se existe, favor defina o conceito.') ;
					} else
					{
				 		var form = document.getElementById("rel_form");
						form.submit();
					}
				}

				function fim()
				{
					document.all.existe[1].checked=true;
					var form = document.getElementById("rel_form");
					form.submit();
				}


				function seExiste(valor)
				{
					var nome   = document.all.nome;
					var indice = document.all.indice;
					var B1     = document.all.B1;
					if(valor=='FALSE')
					{
						nome.disabled   = true ;
						indice.disabled = true ;
						B1.disabled     = true ;
					}
					else
					{
						nome.disabled   = false ;
						indice.disabled = false ;
						B1.disabled     = false ;
					}
				}				

				seExiste('FALSE');
            </script>
            </form>
         <?php
}

if( isset($_SESSION["job"]) )
{
	if( $_SESSION["job"] == "exist" )
	{
				if( $_SESSION['funcao'] == 'sujeito_objeto' )
				{
	                ?>
	                <h3>Conceito: <?=$_SESSION["nome2"]->nome?></h3><br>
	                <?php
				}
				else if( $_SESSION['funcao'] == 'verbo' )
				{
					?>
	                <h3>Verbo: <?=$_SESSION["nome2"]->nome?></h3><br>
	                <?php
				}
				else if( isset($_SESSION["translate"]))
				{
					if($_SESSION["translate"] == 1)
					{
						?>
		                <h3>Conceito: <?=$_SESSION["nome2"]->nome?></h3><br>
		                <?php
					}
					else
					{
						?>
	                	<h3>Verbo: <?=$_SESSION["nome2"]->nome?></h3><br>
	                	<?php
					}
				}
                exist($_SESSION["nome1"], &$_SESSION["lista"]);
	}
	else if( $_SESSION["job"] == "insert" )
	{
		insert($_SESSION["nome1"], &$_SESSION["lista"]);
	}
	else if ( $_SESSION["job"] == "main_subject" )
	{
		importancia_central($_SESSION["nome1"], $_SESSION['nome2']->impacto);
	}
	else if ( $_SESSION["job"] == "reference" )
	{
		//faz_referencia($_SESSION["lista"][0], $_SESSION["lista"][1]);
		faz_referencia($_SESSION["lista"], $_SESSION["nome1"]);
	}
	else if ( $_SESSION["job"] == "type" )
	{
		insere_tipo($_SESSION["lista"]);
	}
	else if( isset($_SESSION["nome2"]) && $_SESSION["job"] == "insert_relation" )
	{
		insere_relacao($_SESSION["nome1"], $_SESSION["nome2"], $_SESSION["nome3"],$_SESSION["lista"]);
	}
	else if( $_SESSION["job"] == "disjoint" )
	{
		disjuncao($_SESSION["nome1"], $_SESSION["lista"]);
	}

        ?>
            <p>
            <form method="POST" action="auxiliar_bd.php" id="salvar_form">
                <input type="hidden" value="TRUE" name="save" size="20" >
                <input type="submit" value="SALVAR" <?php if($_SESSION["salvar"]=="FALSE")echo "disabled" ?> >
                  </form>
            </p>


        <?php

}
else
{
        ?>
        <form method="POST" action="algoritmo_inicio.php">
             <p>
              <font size="4">Carregar Ontologia?</font></p>
              <p>
              <input type="radio" value="TRUE" name="load" size="20"> sim
              <input type="radio" value="FALSE" name="load" size="20"> não
              <input type="submit" value="Enviar" name="B1" size="20">
              </p>
        </form>
        <?php
} 
?>

        <p><a href="inicio.php">Reiniciar Ontologia</a></p>
</td>
</tr>
</table>
</body>
</html>


