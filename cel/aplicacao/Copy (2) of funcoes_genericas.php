<?php
include_once("bd.inc");
include_once("bd_class.php");

/*
if (!(class_exists("PGDB"))) {
	include("bd_class.php");
}
*/

/* chkUser(): checa se o usuário acessando foi autenticado (presença da variável de sessão
$id_usuario_corrente). Caso ele já tenha sido autenticado, continua-se com a execução do
script. Caso contrário, abre-se uma janela de logon. */
if (!(function_exists("chkUser"))) {
	function chkUser($url)
	{
		if (!(session_is_registered("id_usuario_corrente"))) {
?>
<script language="javascript1.3">

open('login.php?url=<?=$url?>', 'login', 'dependent,height=430,width=490,resizable,scrollbars,titlebar');

</script>
<?php
exit();
		}
	}
}
###################################################################
# Insere um cenario no banco de dados.
# Recebe o id_projeto, titulo, objetivo, contexto, atores, recursos, excecao e episodios. (1.1)
# Insere os valores do lexico na tabela CENARIO. (1.2)
# Devolve o id_cenario. (1.4)
#
###################################################################
if (!(function_exists("inclui_cenario"))) {
	function inclui_cenario($id_projeto, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios)
	{
		//global $r;      // Conexao com a base de dados
		$r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		
		$q = "INSERT INTO cenario (id_projeto,data, titulo, objetivo, contexto, atores, recursos, excecao, episodios)
              VALUES ($id_projeto,'now', '" . strtolower($titulo) . "', '$objetivo', '$contexto', '$atores', '$recursos', '$excecao', '$episodios')";
		mysql_query($q) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		$q = "SELECT max(id_cenario) FROM cenario";
		$qrr = mysql_query($q) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		$result = mysql_fetch_row($qrr);
		return $result[0];
	}
}
###################################################################
# Insere um lexico no banco de dados.
# Recebe o id_projeto, nome, noção, impacto e os sinonimos. (1.1)
# Insere os valores do lexico na tabela LEXICO. (1.2)
# Insere todos os sinonimos na tabela SINONIMO. (1.3)
# Devolve o id_lexico. (1.4)
#
###################################################################
if (!(function_exists("inclui_lexico"))) {
	function inclui_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao)
	{
		$r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		$data = date("Y-m-d");
		$q = "INSERT INTO lexico (id_projeto, data, nome, nocao, impacto, tipo)
              VALUES ($id_projeto, '$data', '" . strtolower($nome) . "', '$nocao', '$impacto', '$classificacao')";
		mysql_query($q) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		
		//sinonimo
		$newLexId = mysql_insert_id($r);
		
		
		if( ! is_array($sinonimos) )
		$sinonimos = array();
		
		foreach($sinonimos as $novoSin){
			
			$q = "INSERT INTO sinonimo (id_lexico, nome, id_projeto)
            	VALUES ($newLexId, '" . strtolower($novoSin) . "', $id_projeto)";
			
			mysql_query($q, $r) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		}
		
		$q = "SELECT max(id_lexico) FROM lexico";
		$qrr = mysql_query($q) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		$result = mysql_fetch_row($qrr);
		return $result[0];
	}
}
###################################################################
# Insere um projeto no banco de dados.
# Recebe o nome e descricao. (1.1)
# Verifica se este usuario ja possui um projeto com esse nome. (1.2)
# Caso nao possua, insere os valores na tabela PROJETO. (1.3)
# Devolve o id_cprojeto. (1.4)
#
###################################################################
if (!(function_exists("inclui_projeto"))) {
	function inclui_projeto($nome, $descricao)
	{
		$r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		//verifica se usuario ja existe
		$qv = "SELECT * FROM projeto WHERE nome = '$nome'";
		$qvr = mysql_query($qv) or die("Erro ao enviar a query de select<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		
		//$result = mysql_fetch_row($qvr);
		$resultArray = mysql_fetch_array($qvr);
		
		
		if ( $resultArray != false )
		{
			//verifica se o nome existente corresponde a um projeto que este usuario participa
			$id_projeto_repetido = $resultArray['id_projeto'];
			
			$id_usuario_corrente = $_SESSION['id_usuario_corrente'];
			
			$qvu = "SELECT * FROM participa WHERE id_projeto = '$id_projeto_repetido' AND id_usuario = '$id_usuario_corrente' ";
			
			$qvuv = mysql_query($qvu) or die("Erro ao enviar a query de SELECT no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
			
			$resultArray = mysql_fetch_row($qvuv);
			
			if ($resultArray[0] != null )
			{
				return -1;
			}
			
		}
		
		$q = "SELECT MAX(id_projeto) FROM projeto";
		$qrr = mysql_query($q) or die("Erro ao enviar a query de MAX ID<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		$result = mysql_fetch_row($qrr);
		
		if ( $result[0] == false )
		{
			$result[0] = 1;
		}
		else
		{
			$result[0]++;
		}
		$data = date("Y-m-d");
		
		$qr = "INSERT INTO projeto (id_projeto, nome, data_criacao, descricao)
	              VALUES ($result[0],'$nome','$data' , '$descricao')";
		
		mysql_query($qr) or die("Erro ao enviar a query INSERT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		
		return $result[0];
	}
}

if (!(function_exists("replace_skip_tags"))) {
	function replace_skip_tags($search, $subject, $t_lnk, $id_lnk) {
		$title = ($t_lnk == "c") ? "Cenario" : "Lexico";
		$subject_tmp = preg_replace("/>(.*)(" . $search . ")(.*)</Ui", ">$1$2abcdef$3<", $subject);
		if ($t_lnk == "l") {
			$subject_tmp2 = preg_replace("/(\s|\b)(" . $search . ")(\s|\b)/i", '$1<a title="' . $title . '" href="main.php?t=' . $t_lnk . '&id=' . $id_lnk . '">$2</a>$3', $subject_tmp);
		} else {
			$subject_tmp2 = preg_replace("/(\s|\b)(" . $search . ")(\s|\b)/i", '$1<a title="' . $title . '" href="main.php?t=' . $t_lnk . '&id=' . $id_lnk . '"><span style="font-variant: small-caps">$2</span></a>$3', $subject_tmp);
		}
		$subject_tmp3 = preg_replace("/>(.*)(" . $search . ")abcdef(.*)</Ui", ">$1$2$3<", $subject_tmp2);
		
		?>
<?php
			        /*
				$arquivo = fopen("teste_BUG_expressao_regular.txt","a") ;
				
				fprintf($arquivo , "subject_tmp3: ") ;
				is_array($subject_tmp3)? fprintf($arquivo, print_r($subject_tmp3)) : fprint($arquivo, $subject_tmp3) ;
				fprintf($arquivo, "\n");

				fprintf($arquivo , "subject_tmp2: ") ;
				is_array($subject_tmp2)? fprintf($arquivo, print_r($subject_tmp2)) : fprint($arquivo, $subject_tmp2) ;
				fprintf($arquivo, "\n");

				fprintf($arquivo , "subject_tmp: ") ;
				is_array($subject_tmp)? fprintf($arquivo, print_r($subject_tmp)) : fprint($arquivo, $subject_tmp) ;
				fprintf($arquivo, "\n");				

				fprintf($arquivo , "search: ") ;
				is_array($search)? fprintf($arquivo, print_r($search)) : fprint($arquivo, $search) ;
				fprintf($arquivo, "\n");

				fprintf($arquivo , "subject: ") ;
				is_array($subject)? fprintf($arquivo, print_r($subject)) : fprint($arquivo, $subject) ;
				fprintf($arquivo, "\n\n\n");
			
				fclose($arquivo) ;
				*/
				?>
<?
		
		return $subject_tmp3;
	}
}

if (!(function_exists("recarrega"))) {
	function recarrega($url) {
?>
<script language="javascript1.3">

location.replace('<?=$url?>');

</script>
<?php
	}
}

if (!(function_exists("breakpoint"))) {
	function breakpoint($num) {
?>
<script language="javascript1.3">

alert('<?=$num?>');

</script>
<?php
	}
}

if (!(function_exists("simple_query"))) {
/*	function simple_query($field, $table, $where) {
		$r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		$q = "SELECT $field FROM $table WHERE $where";
		$qrr = mysql_query($q) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		$result = mysql_fetch_row($qrr);
		return $result[0];      
  */
    function simple_query($field, $table, $where) {
        $r = bd_connect() or die("Erro ao conectar ao SGBD");
        $q = "SELECT $field FROM $table WHERE $where";
        $qrr = mysql_query($q) or die("Erro ao enviar a query");
        $result = mysql_fetch_row($qrr);
        return $result[0];		
	}
}



// Para a correta inclusao de um cenario, uma serie de procedimentos
// precisam ser tomados (relativos ao requisito 'navegacao circular'):
//
// 1. Incluir o novo cenario na base de dados;
// 2. Para todos os cenarios daquele projeto:
//      2.1. Procurar em contexto, episodios
//           por ocorrencias do titulo do cenario incluido;
//      2.2. Para os campos em que forem encontradas ocorrencias:
//          2.2.1. Transformar a ocorrencia (titulo do cenario) em link;
//      2.3. Se algum campo sofreu alteracao:
//          2.3.1. Incluir entrada na tabela 'centocen';
//      2.4. Procurar em contexto, episodios do cenario incluido
//           por ocorrencias de titulos de outros cenarios do mesmo projeto;
//      2.5. Se achar alguma ocorrencia:
//          2.5.1. Transformar ocorrencia em link;
//          2.5.2. Incluir entrada na tabela 'centocen';
// 3. Para todos os nomes de termos do lexico daquele projeto:
//      3.1. Procurar ocorrencias desses nomes no titulo, objetivo, contexto,
//           recursos, atores, episodios do cenario incluido;
//      3.2. Para os campos em que forem encontradas ocorrencias:
//          3.2.1. Transformar as ocorrencias (nomes de termos) em link;
//      3.3. Se algum campo sofreu alteracao:
//          3.3.1. Incluir entrada na tabela 'centolex';

if (!(function_exists("adicionar_cenario"))) {
	function adicionar_cenario($id_projeto, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios)
	{
		// Conecta ao SGBD
		$r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		// Inclui o cenario na base de dados (sem transformar os campos
		// em links e sem criar os relacionamentos)
		$id_incluido = inclui_cenario($id_projeto, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios);
		
		$q = "SELECT id_cenario, titulo, contexto, episodios
              FROM cenario
              WHERE id_projeto = $id_projeto
              AND id_cenario != $id_incluido
              ORDER BY CHAR_LENGTH(titulo) DESC";
		$qrr = mysql_query($q) or die("Erro ao enviar a query de SELECT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		while ($result = mysql_fetch_array($qrr)) {    // (2) Para todos os cenarios
		
		$result_m = replace_skip_tags($titulo, $result, "c", $id_incluido);
		
		if ($result['contexto'] != $result_m['contexto'] ||
		$result['episodios'] != $result_m['episodios']) {   // (2.3)
		
		$q = "UPDATE cenario SET
                      contexto = '" . $result_m['contexto'] . "',
                      episodios = '" . $result_m['episodios'] . "'
                      WHERE id_cenario = " . $result['id_cenario'];
		mysql_query($q) or die("Erro ao enviar a query de UPDATE<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (2.2.1 tbm)
		$q = "INSERT INTO centocen (id_cenario_from, id_cenario_to)
                      VALUES (" . $result['id_cenario'] . ", $id_incluido)";
		mysql_query($q) or die("Erro ao enviar a query de INSERT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (2.3.1)
		}
		
		// Para podermos executar (2.4), devemos retirar os links (possivelmente presentes)
		// dos titulos dos outros cenarios do mesmo projeto. Esta regexp remove tags HTML.
		$result['titulo'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['titulo']);
		
		$contexto_m = replace_skip_tags($result['titulo'], $contexto, "c", $result['id_cenario']);
		$episodios_m = replace_skip_tags($result['titulo'], $episodios, "c", $result['id_cenario']);
		
		if ($contexto != $contexto_m ||
		$episodios != $episodios_m) {   // (2.5)
		$q = "UPDATE cenario SET
                      contexto = '$contexto_m',
                      episodios = '$episodios_m'
                      WHERE id_cenario = $id_incluido";
		mysql_query($q) or die("Erro ao enviar a query de UPDATE 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (2.5.1)
		
		// $qCen = "SELECT * FROM centocen WHERE id_cenario_from = $id_incluido AND id_cenario_to = " . $result['id_cenario'];
		//  $qrCen = mysql_query($qCen) or die("Erro ao enviar a query de select no centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		//  $resultArrayCen = mysql_fetch_array($qrCen);
		
		//  if ($resultArrayCen == false)
		//  {
		$q = "INSERT INTO centocen (id_cenario_from, id_cenario_to) VALUES ($id_incluido, " . $result['id_cenario'] . ")";
		//$q = "INSERT INTO centocen (id_cenario_to, id_cenario_from) VALUES ($id_incluido, " . $result['id_cenario'] . ")";
		mysql_query($q) or die("Erro ao enviar a query de insert no centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (2.5.2)
		//  }
		// Atualiza definicao de $objetivo, $contexto, $atores, $recursos, $episodios
		$contexto = $contexto_m;
		$episodios = $episodios_m;
		}   // if
		}   // while
		
		$q = "SELECT id_lexico, nome FROM lexico WHERE id_projeto = $id_projeto";
		$qrr = mysql_query($q) or die("Erro ao enviar a query de SELECT 3<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		while ($result = mysql_fetch_array($qrr)) {    // (3)
		//$titulo_m = replace_skip_tags($result['nome'], $titulo, "l", $result['id_lexico']);
		$objetivo_m = replace_skip_tags($result['nome'], $objetivo, "l", $result['id_lexico']);
		$contexto_m = replace_skip_tags($result['nome'], $contexto, "l", $result['id_lexico']);
		$atores_m = replace_skip_tags($result['nome'], $atores, "l", $result['id_lexico']);
		$recursos_m = replace_skip_tags($result['nome'], $recursos, "l", $result['id_lexico']);
		$excecao_m = replace_skip_tags($result['nome'], $excecao, "l", $result['id_lexico']);
		$episodios_m = replace_skip_tags($result['nome'], $episodios, "l", $result['id_lexico']);
		if (//$titulo != $titulo_m      ||
		$objetivo  != $objetivo_m ||
		$contexto  != $contexto_m ||
		$atores    != $atores_m   ||
		$recursos  != $recursos_m ||
		$excecao   != $excecao_m  ||
		$episodios != $episodios_m) {   // (3.3)
		$q = "UPDATE cenario SET
                      objetivo  = '$objetivo_m',
                      contexto  = '$contexto_m',
                      atores    = '$atores_m',
                      recursos  = '$recursos_m',
                      excecao   = '$excecao_m',
                      episodios = '$episodios_m'
                      WHERE id_cenario = $id_incluido";
		mysql_query($q) or die("Erro ao enviar a query de UPDATE3<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (3.2.1)
		
		$qCen = "SELECT * FROM centolex WHERE id_cenario = $id_incluido AND id_lexico = " . $result['id_lexico'];
		$qrCen = mysql_query($qCen) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		$resultArrayCen = mysql_fetch_array($qrCen);
		
		if ($resultArrayCen == false)
		{
			$q = "INSERT INTO centolex (id_cenario, id_lexico) VALUES ($id_incluido, " . $result['id_lexico'] . ")";
			mysql_query($q) or die("Erro ao enviar a query de INSERT 3<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (3.3.1)
		}
		// Atualiza definicao de $titulo, $objetivo, $contexto, $atores, $recursos, $episodios
		//$titulo    = $titulo_m;
		$objetivo  = $objetivo_m;
		$contexto  = $contexto_m;
		$atores    = $atores_m;
		$recursos  = $recursos_m;
		$excecao   = $excecao_m;
		$episodios = $episodios_m;
		}   // if
		}   // while
		
		//Sinonimos
		
		
		$qSinonimos = "SELECT nome, id_lexico FROM sinonimo WHERE id_projeto = $id_projeto AND id_pedidolex = 0";
		
		$qrrSinonimos = mysql_query($qSinonimos) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		
		$nomesSinonimos = array();
		
		$id_lexicoSinonimo = array();
		
		while($rowSinonimo = mysql_fetch_array($qrrSinonimos)){
			
			$nomesSinonimos[]     = $rowSinonimo["nome"];
			$id_lexicoSinonimo[]  = $rowSinonimo["id_lexico"];
			
		}
		$qlc = "SELECT id_cenario, titulo, contexto, episodios, objetivo, atores, recursos, excecao
              FROM cenario
              WHERE id_projeto = $id_projeto
              AND id_cenario = $id_incluido";
		$count = count($nomesSinonimos);
		for ($i = 0; $i < $count; $i++)
		{
			
			$qrr = mysql_query($qlc) or die("Erro ao enviar a query de busca<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
			while ($result = mysql_fetch_array($qrr)) {    // (3)
			// $titulo_m = replace_skip_tags($nomesSinonimos[$i], $titulo, "l", $id_lexicoSinonimo[$i]);
			$objetivo_m = replace_skip_tags($nomesSinonimos[$i], $objetivo, "l", $id_lexicoSinonimo[$i]);
			$contexto_m = replace_skip_tags($nomesSinonimos[$i], $contexto, "l", $id_lexicoSinonimo[$i]);
			$atores_m = replace_skip_tags($nomesSinonimos[$i], $atores, "l", $id_lexicoSinonimo[$i]);
			$recursos_m = replace_skip_tags($nomesSinonimos[$i], $recursos, "l", $id_lexicoSinonimo[$i]);
			$excecao_m = replace_skip_tags($nomesSinonimos[$i], $excecao, "l", $id_lexicoSinonimo[$i]);
			$episodios_m = replace_skip_tags($nomesSinonimos[$i], $episodios, "l", $id_lexicoSinonimo[$i]);
			if (//$titulo != $titulo_m      ||
			$objetivo  != $objetivo_m ||
			$contexto  != $contexto_m ||
			$atores    != $atores_m   ||
			$recursos  != $recursos_m ||
			$excecao   != $excecao_m  ||
			$episodios != $episodios_m) {   // (3.3)
			$q = "UPDATE cenario SET
                      objetivo  = '$objetivo_m',
                      contexto  = '$contexto_m',
                      atores    = '$atores_m',
                      recursos  = '$recursos_m',
                      excecao   = '$excecao_m',
                      episodios = '$episodios_m'
                      WHERE id_cenario = $id_incluido";
			mysql_query($q) or die("Erro ao enviar a query de update 4<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (3.2.1)
			
			$qCen = "SELECT * FROM centolex WHERE id_cenario = $id_incluido AND id_lexico = $id_lexicoSinonimo[$i] ";
			$qrCen = mysql_query($qCen) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
			$resultArrayCen = mysql_fetch_array($qrCen);
			
			if ($resultArrayCen == false)
			{
				$q = "INSERT INTO centolex (id_cenario, id_lexico) VALUES ($id_incluido, $id_lexicoSinonimo[$i])";
				mysql_query($q) or die("Erro ao enviar a query de insert no centolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (3.3.1)
			}
			// Atualiza definicao de $titulo, $objetivo, $contexto, $atores, $recursos, $episodios
			//$titulo    = $titulo_m;
			$objetivo  = $objetivo_m;
			$contexto  = $contexto_m;
			$atores    = $atores_m;
			$recursos  = $recursos_m;
			$excecao   = $excecao_m;
			$episodios = $episodios_m;
			}   // if
			}   // while
			
		} //for
		
	}
}

// Para a correta inclusao de um termo no lexico, uma serie de procedimentos
// precisam ser tomados (relativos ao requisito 'navegacao circular'):
//
// 1. Incluir o novo termo na base de dados;
// 2. Para todos os cenarios daquele projeto:
//      2.1. Procurar em titulo, objetivo, contexto, recursos, atores, episodios
//           por ocorrencias do termo incluido ou de seus sinonimos;
//      2.2. Para os campos em que forem encontradas ocorrencias:
//              2.2.1. Transformar a ocorrencia (nome do lexico) em link;
//      2.3. Se algum campo sofreu alteracao:
//              2.3.1. Incluir entrada na tabela 'centolex';
// 3. Para todos termos do lexico daquele projeto (menos o recem-inserido):
//      3.1. Procurar em nocao, impacto por ocorrencias do termo inserido ou de seus sinonimos;
//      3.2. Para os campos em que forem encontradas ocorrencias:
//              3.2.1. Transformar a ocorrencia (nome do lexico ou sinonimo) em link;
//      3.3. Se algum campo sofreu alteracao:
//              3.3.1. Incluir entrada na tabela 'lextolex';
//      3.4. Procurar em nocao, impacto do termo inserido por
//           ocorrencias de termos do lexico do mesmo projeto;
//      3.5. Se achar alguma ocorrencia:
//          3.5.1. Transformar ocorrencia em link;
//          3.5.2. Incluir entrada na table 'lextolex';

if (!(function_exists("adicionar_lexico"))) {
	function adicionar_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao){
		
		
		$r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		
		$id_incluido = inclui_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao); // (1)
		// $nome, $nocao e $impacto campos do formulario
		
		$qr = "SELECT id_cenario, titulo, objetivo, contexto, atores, recursos, excecao, episodios
          FROM cenario
          WHERE id_projeto = $id_projeto";
		
		$qrr = mysql_query($qr) or die("Erro ao enviar a query de SELECT 1<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		
		while ($result = mysql_fetch_array($qrr)) {    // (2) Para todos os cenarios
		$result_m = replace_skip_tags($nome, $result, "l", $id_incluido);
		
		if (//$result['titulo'] != $result_m['titulo']        ||
		$result['objetivo'] != $result_m['objetivo']    ||
		$result['contexto'] != $result_m['contexto']    ||
		$result['atores'] != $result_m['atores']        ||
		$result['recursos'] != $result_m['recursos']    ||
		$result['excecao']  != $result_m['excecao']    ||
		$result['episodios'] != $result_m['episodios']) {   // (2.3)
		
		$q = "UPDATE cenario SET
                  objetivo = '" . $result_m['objetivo'] . "',
                  contexto = '" . $result_m['contexto'] . "',
                  atores = '" . $result_m['atores'] . "',
                  recursos = '" . $result_m['recursos'] . "',
                  excecao = '" . $result_m['excecao'] . "',
                  episodios = '" . $result_m['episodios'] . "'  
                  WHERE id_cenario = " . $result['id_cenario'];
		
		mysql_query($q) or die("Erro ao enviar a query de UPDATE 1<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (2.2.1 tbm)
		$q = "INSERT INTO centolex (id_cenario, id_lexico)
                  VALUES (" . $result['id_cenario'] . ", $id_incluido)";
		
		mysql_query($q) or die("Erro ao enviar a query de INSERT 1<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (2.3.1)
		}
		}
		
		//sinonimos do novo lexico
		$count = count($sinonimos);
		for ($i = 0; $i < $count; $i++)
		{
			
			$qrr = mysql_query($qr) or die("Erro ao enviar a query de SELECT 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
			while ($result2 = mysql_fetch_array($qrr))
			{
				
				$result_m2 = replace_skip_tags($sinonimos[$i], $result2, "l", $id_incluido);
				if (//$result2['titulo'] != $result_m2['titulo']        ||
				$result2['objetivo'] != $result_m2['objetivo']    ||
				$result2['contexto'] != $result_m2['contexto']    ||
				$result2['atores'] != $result_m2['atores']        ||
				$result2['recursos'] != $result_m2['recursos']    ||
				$result2['excecao']  != $result_m2['excecao']    ||
				$result2['episodios'] != $result_m2['episodios']) {   // (2.3)
				
				$q = "UPDATE cenario SET
                  objetivo = '" . $result_m2['objetivo'] . "',                              
                  contexto = '" . $result_m2['contexto'] . "',                              
                  atores = '" . $result_m2['atores'] . "',                                  
                  recursos = '" . $result_m2['recursos'] . "',                              
                  excecao = '" . $result_m2['excecao'] . "',                                
                  episodios = '" . $result_m2['episodios'] . "'                             
                  WHERE id_cenario = " . $result2['id_cenario'];                            
				
				mysql_query($q) or die("Erro ao enviar a query de UPDATE 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
				$qLex = "SELECT * FROM centolex WHERE id_cenario = " . $result2['id_cenario'] . " AND id_lexico = $id_incluido ";
				$qrLex = mysql_query($qLex) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
				$resultArraylex = mysql_fetch_array($qrLex);
				
				if ( $resultArraylex == false )
				{
					
					$q = "INSERT INTO centolex (id_cenario, id_lexico)
                  VALUES (" . $result2['id_cenario'] . ", $id_incluido)";                   
					
					mysql_query($q) or die("Erro ao enviar a query de INSERT 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
				} //if
				}//if
				
			}//while
			
		} //for
		
		
		
		
		$qlo = "SELECT id_lexico, nome, nocao, impacto, tipo
          FROM lexico
          WHERE id_projeto = $id_projeto
          AND id_lexico != $id_incluido";      
		//pega todos os outros lexicos
		$qrr = mysql_query($qlo) or die("Erro ao enviar a query de SELECT no LEXICO<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		
		while ($result = mysql_fetch_array($qrr)) 
		{    // (3)
		
			$result_m = replace_skip_tags($nome, $result, "l", $id_incluido);
			
			if ($result['nocao'] != $result_m['nocao'] || $result['impacto'] != $result_m['impacto']) 
			{   // (3.3)
				$q = "UPDATE lexico SET
		                  nocao = '" . $result_m['nocao'] . "',
		                  impacto = '" . $result_m['impacto'] . "'
		                  WHERE id_lexico = '" . $result['id_lexico'] . "'";
				// echo($nome)."   ";
				//  echo($result_m['nocao'])."   ";
				
				mysql_query($q) or die("Erro ao enviar a query de update no LEXICO 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
				$qLex = "SELECT * FROM lextolex WHERE id_lexico_from = " . $result['id_lexico'] . " AND id_lexico_to = $id_incluido";
				$qrLex = mysql_query($qLex) or die("Erro ao enviar a query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
				$resultArraylex = mysql_fetch_array($qrLex);
				
				if ( $resultArraylex == false )
				{
					
					
					$q = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
		                  VALUES (" . $result['id_lexico'] . ", $id_incluido)";
					
					mysql_query($q) or die("Erro ao enviar a query de INSERT no lextolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
				}
			}
			
			$nocao_m = replace_skip_tags($result['nome'], $nocao, "l", $result['id_lexico']);
			$impacto_m = replace_skip_tags($result['nome'], $impacto, "l", $result['id_lexico']);
			
			if ($nocao_m != $nocao || $impacto_m != $impacto) 
			{     // (3.5)
				$q = "UPDATE lexico SET nocao = '$nocao_m', impacto = '$impacto_m' WHERE id_lexico = $id_incluido";
				mysql_query($q) or die("Erro ao executar query de update no lexico 4<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);// (3.5.1)
				
				$qLex = "SELECT * FROM lextolex WHERE id_lexico_from = $id_incluido AND id_lexico_to = " . $result['id_lexico'];
				$qrLex = mysql_query($qLex) or die("Erro ao enviar a query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
				$resultArraylex = mysql_fetch_array($qrLex);
				
				if ( $resultArraylex == false )
				{
					
					
					$q = "INSERT INTO lextolex (id_lexico_from, id_lexico_to) VALUES ($id_incluido, " . $result['id_lexico'] . ")";
					
					mysql_query($q) or die("Erro ao executar query de insert no lextolex 3<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);      // (3.5.2)
				}
				// Atualiza a definicao de $nocao e $impacto
				$nocao = $nocao_m;
				$impacto = $impacto_m;
			}   // if
		}   // while
		
		
		//lexico para lexico
		
		$ql = "SELECT id_lexico, nome, nocao, impacto
          FROM lexico
          WHERE id_projeto = $id_projeto
          AND id_lexico != $id_incluido";                                                                     
		
		//sinonimos incluidos nos outros lexicos
		
		$qrr = mysql_query($ql) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		
		$count = count($sinonimos);
		for ($i = 0; $i < $count; $i++)
		{
			while ($resultl = mysql_fetch_array($qrr)) {
				$result_ml = replace_skip_tags($sinonimos[$i], $resultl, "l", $id_incluido);
				
				if ($resultl['nocao'] != $result_ml['nocao'] ||
				$resultl['impacto'] != $result_ml['impacto']) {
					$q = "UPDATE lexico SET
	                  nocao = '" . $result_ml['nocao'] . "',                            
	                  impacto = '" . $result_ml['impacto'] . "'                         
	                  WHERE id_lexico = " . $resultl['id_lexico'];                      
					
					mysql_query($q) or die("Erro ao enviar a query de update no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
					
					$qLex = "SELECT * FROM lextolex WHERE id_lexico_from = " . $resultl['id_lexico'] . " AND id_lexico_to = $id_incluido";
					$qrLex = mysql_query($qLex) or die("Erro ao enviar a query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
					$resultArraylex = mysql_fetch_array($qrLex);
					
					if ( $resultArraylex == false )
					{
						
						$q = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
	                     VALUES (" . $resultl['id_lexico'] . ", $id_incluido)";            
						
						mysql_query($q) or die("Erro ao enviar a query de insert no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
					}//if
				}    //if
			}//while
		}//for
		
		//sinonimos ja existentes
		
		$qSinonimos = "SELECT nome, id_lexico FROM sinonimo WHERE id_projeto = $id_projeto AND id_lexico != $id_incluido AND id_pedidolex = 0";
		
		$qrrSinonimos = mysql_query($qSinonimos) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		
		$nomesSinonimos = array();
		
		$id_lexicoSinonimo = array();
		
		while($rowSinonimo = mysql_fetch_array($qrrSinonimos)){
			
			$nomesSinonimos[]     = $rowSinonimo["nome"];
			$id_lexicoSinonimo[]  = $rowSinonimo["id_lexico"];
			
		}
		
		
		
		
		//////PROBLEMAS/////
		/*
		$qlIncluido = "SELECT id_lexico, nome, nocao, impacto
          FROM lexico
          WHERE id_projeto = $id_projeto
          AND id_lexico = $id_incluido";     
		
		
		$count = count($nomesSinonimos);
		for ($i = 0; $i < $count; $i++)
		{
			// echo ($nomesSinonimos[$i])."    ";
			$qrr = mysql_query($qlIncluido) or die("Erro ao enviar a query de select no Lexico 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
			while ($resultlne = mysql_fetch_array($qrr)) {
				$result_mlne = replace_skip_tags($nomesSinonimos[$i], $resultlne, "l", $id_lexicoSinonimo[$i]);
				if ($resultlne['nocao'] != $result_mlne['nocao'] ||
				$resultlne['impacto'] != $result_mlne['impacto']) {
					$qup = "UPDATE lexico SET
	                  nocao = '" . $result_mlne['nocao'] . "',                            
	                  impacto = '" . $result_mlne['impacto'] . "'                         
	                  WHERE id_lexico = " . $id_incluido;             
					?><script>alert('<?=$resultlne['nocao']?>');</script><?php 						
					mysql_query($qup) or die("Erro ao enviar a query de update 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
					//echo ($nomesSinonimos[$i])."    ";
					//echo ($result_mlne['nocao'])."    ";
					
					$qLex = "SELECT * FROM lextolex WHERE id_lexico_to = $id_lexicoSinonimo[$i] AND id_lexico_from = $id_incluido ";
					$qrLex = mysql_query($qLex) or die("Erro ao enviar a query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
					$resultArraylex = mysql_fetch_array($qrLex);
					
					if ( $resultArraylex == false )
					{
						$q = "INSERT INTO lextolex (id_lexico_to, id_lexico_from)
	                  VALUES ( $id_lexicoSinonimo[$i], $id_incluido) ";                                                                            
						mysql_query($q) or die("Erro ao enviar a query de insert 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
					}
					
				}//if
			}//while
		}//for
		*/
	}
}


###################################################################
# Essa funcao recebe um id de cenario e remove todos os seus
# links e relacionamentos existentes.
###################################################################
if (!(function_exists("removeCenario"))) {
	function removeCenario($id_projeto,$id_cenario){
		$DB = new PGDB () ;
		$sql = new QUERY ($DB) ;
		$sql2 = new QUERY ($DB) ;
		$sql3 = new QUERY ($DB) ;
		$sql4 = new QUERY ($DB) ;
		$sql5 = new QUERY ($DB) ;
		$sql6 = new QUERY ($DB) ;
		$sql7 = new QUERY ($DB) ;
		# Este select procura o cenario a ser removido
		# dentro do projeto
		//print("<br>SELECT * FROM cenario WHERE id_projeto = $id_projeto and id_cenario = $id_cenario");
		$sql2->execute ("SELECT * FROM cenario WHERE id_projeto = $id_projeto and id_cenario = $id_cenario") ;
		if ($sql2->getntuples() == 0){
			//echo "<BR> Cenario nao existe para esse projeto." ;
		}else{
			$record = $sql2->gofirst ();
			$tituloCenario = $record['titulo'] ;
			# tituloCenario = Nome do cenario com id = $id_cenario
		}
		# [ATENCAO] Essa query pode ser melhorada com um join
		//print("<br>SELECT * FROM cenario WHERE id_projeto = $id_projeto");
		/*  $sql->execute ("SELECT * FROM cenario WHERE id_projeto = $id_projeto AND id_cenario != $tituloCenario");
		if ($sql->getntuples() == 0){
		echo "<BR> Projeto não possui cenarios." ;
		}else{*/
		$qr = "SELECT * FROM cenario WHERE id_projeto = $id_projeto AND id_cenario != $id_cenario";
		//echo($qr)."          ";
		$qrr = mysql_query($qr) or die("Erro ao enviar a query de SELECT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		while ($result = mysql_fetch_array($qrr))
		{
			# Percorre todos os cenarios tirando as tag do cenario
			# a ser removido
			//$record = $sql->gofirst ();
			//while($record !='LAST_RECORD_REACHED'){
			$idCenarioRef = $result['id_cenario'] ;
			$tituloAnterior = $result['titulo'] ;
			$objetivoAnterior = $result['objetivo'] ;
			$contextoAnterior = $result['contexto'] ;
			$atoresAnterior = $result['atores'] ;
			$recursosAnterior = $result['recursos'] ;
			$episodiosAnterior = $result['episodios'] ;
			$excecaoAnterior = $result['excecao'] ;
			#echo        "/<a title=\"Cenário\" href=\"main.php?t='c'&id=$id_cenario>($tituloCenario)<\/a>/mi"  ;
			#$episodiosAnterior = "<a title=\"Cenário\" href=\"main.php?t=c&id=38\">robin</a>" ;
			/*"'<a title=\"Cenário\" href=\"main.php?t=c&id=38\">robin<\/a>'si" ; */
			$tiratag = "'<[\/\!]*?[^<>]*?>'si" ;
			//$tiratagreplace = "";
			//$tituloCenario = preg_replace($tiratag,$tiratagreplace,$tituloCenario);
			$regexp = "/<a[^>]*?>($tituloCenario)<\/a>/mi" ;//rever
			$replace = "$1";
			//echo($episodiosAnterior)."   ";
			//$tituloAtual = $tituloAnterior ;
			//*$tituloAtual  = preg_replace($regexp,$replace,$tituloAnterior);*/
			$objetivoAtual  = preg_replace($regexp,$replace,$objetivoAnterior);
			$contextoAtual  = preg_replace($regexp,$replace,$contextoAnterior);
			$atoresAtual    = preg_replace($regexp,$replace,$atoresAnterior);
			$recursosAtual  = preg_replace($regexp,$replace,$recursosAnterior);
			$episodiosAtual = preg_replace($regexp,$replace,$episodiosAnterior);
			$excecaoAtual   = preg_replace($regexp,$replace,$excecaoAnterior);
			/*echo "ant:".$episodiosAtual ;
			echo "<br>" ;
			echo "dep:".$episodiosAnterior ;*/
			// echo($tituloCenario)."   ";
			// echo($episodiosAtual)."  ";
			//print ("<br>update cenario set objetivo = '$objetivoAtual',contexto = '$contextoAtual',atores = '$atoresAtual',recursos = '$recursosAtual',episodios = '$episodiosAtual' where id_cenario = $idCenarioRef ");
			$sql7->execute ("update cenario set objetivo = '$objetivoAtual',contexto = '$contextoAtual',atores = '$atoresAtual',recursos = '$recursosAtual', episodios = '$episodiosAtual', excecao = '$excecaoAtual' where id_cenario = $idCenarioRef ");
			
			//$record = $sql->gonext() ;
			// }
		}
		# Remove o relacionamento entre o cenario a ser removido
		# e outros cenarios que o referenciam
		$sql3->execute ("DELETE FROM centocen WHERE id_cenario_from = $id_cenario") ;
		$sql4->execute ("DELETE FROM centocen WHERE id_cenario_to = $id_cenario") ;
		# Remove o relacionamento entre o cenario a ser removido
		# e o seu lexico
		$sql5->execute ("DELETE FROM centolex WHERE id_cenario = $id_cenario") ;
		# Remove o cenario escolhido
		$sql6->execute ("DELETE FROM cenario WHERE id_cenario = $id_cenario") ;
		
	}
	
}

###################################################################
# Essa funcao recebe um id de lexico e remove todos os seus
# links e relacionamentos existentes em todas as tabelas do banco.
###################################################################
if (!(function_exists("removeLexico"))) {
	function removeLexico($id_projeto,$id_lexico){
		$DB = new PGDB () ;
		$sql = new QUERY ($DB) ;
		$update = new QUERY ($DB) ;
		$delete = new QUERY ($DB) ;
		
		# Este select procura o lexico a ser removido
		# dentro do projeto
		$sql->execute ("SELECT * FROM lexico WHERE id_projeto = $id_projeto and id_lexico = $id_lexico ") ;
		if ($sql->getntuples() == 0){
			//echo "<BR> Lexico nao existe para esse projeto." ;
		}else{
			$record = $sql->gofirst ();
			$nomeLexico = $record['nome'] ;
			# nomeLexico = Nome do lexico com id = $id_lexico
		}
		# [ATENCAO] Essa query pode ser melhorada com um join
		$sql->execute ("SELECT * FROM lexico WHERE id_projeto = $id_projeto ");
		if ($sql->getntuples() == 0){
			//echo "<BR> Projeto não possui lexicos ainda." ;
		}else{
			# Percorre todos os lexicos tirando as tag do lexico
			# a ser removido
			$record = $sql->gofirst ();
			while($record !='LAST_RECORD_REACHED'){
				$idLexicoRef = $record['id_lexico'] ;
				$nocaoAnterior = $record['nocao'] ;
				$impactoAnterior = $record['impacto'] ;
				$regexp = "/<a[^>]*?>($nomeLexico)<\/a>/mi" ;
				$replace = "$1";
				$nocaoAtual = preg_replace($regexp,$replace,$nocaoAnterior);
				$impactoAtual = preg_replace($regexp,$replace,$impactoAnterior);
				//print ("<br>update lexico set nocao = '$nocaoAtual',impacto = '$impactoAtual' where id_lexico = $idLexicoRef ");
				$update->execute ("update lexico set nocao = '$nocaoAtual',impacto = '$impactoAtual' where id_lexico = $idLexicoRef ");
				$record = $sql->gonext() ;
			}
		}
		
		// retira os links do lexico dos cenarios
		# [ATENCAO] Essa query pode ser melhorada com um join
		$sql->execute ("SELECT * FROM cenario WHERE id_projeto = $id_projeto ");
		if ($sql->getntuples() == 0){
			//echo "<BR> Projeto não possui cenarios." ;
		}else{
			# Percorre todos os cenarios tirando as tag do lexico
			# a ser removido
			$record = $sql->gofirst ();
			while($record !='LAST_RECORD_REACHED'){
				$idCenarioRef = $record['id_cenario'] ;
				$objetivoAnterior = $record['objetivo'] ;
				$contextoAnterior = $record['contexto'] ;
				$atoresAnterior = $record['atores'] ;
				$recursosAnterior = $record['recursos'] ;
				$episodiosAnterior = $record['episodios'] ;
				$excecaoAnterior = $record['excecao'] ;
				$regexp = "/<a[^>]*?>($nomeLexico)<\/a>/mi" ;
				$replace = "$1";
				$objetivoAtual = preg_replace($regexp,$replace,$objetivoAnterior);
				$contextoAtual = preg_replace($regexp,$replace,$contextoAnterior);
				$atoresAtual = preg_replace($regexp,$replace,$atoresAnterior);
				$recursosAtual = preg_replace($regexp,$replace,$recursosAnterior);
				$episodiosAtual = preg_replace($regexp,$replace,$episodiosAnterior);
				$excecaoAtual = preg_replace($regexp,$replace,$excecaoAnterior);
				$update->execute ("update cenario set objetivo = '$objetivoAtual',contexto = '$contextoAtual', atores = '$atoresAtual', recursos = '$recursosAtual', episodios = '$episodiosAtual', excecao = '$excecaoAtual' where id_cenario = $idCenarioRef ");
				$record = $sql->gonext() ;
			}//while
		}//if
		
		
		
		
		
		
		
		//pega os sinonimos deste lexico
		$qSinonimos = "SELECT * FROM sinonimo WHERE id_projeto = $id_projeto AND id_lexico = $id_lexico";
		
		$qrrSinonimos = mysql_query($qSinonimos) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		
		$nomesSinonimos = array();
		
		while($rowSinonimo = mysql_fetch_array($qrrSinonimos)){
			
			$nomesSinonimos[]     = $rowSinonimo["nome"];
			
		}
		
		//remove sinonimos deste lexico nos outros lexicos do projeto
		$count = count($nomesSinonimos);
		for ($i = 0; $i < $count; $i++)
		{
			$sql->execute ("SELECT * FROM lexico WHERE id_projeto = $id_projeto ");
			if ($sql->getntuples() == 0){
				//echo "<BR> Projeto não possui lexicos -ainda." ;
			}else{
				# Percorre todos os lexicos tirando as tag do sinonimo
				# a ser removido
				//echo($sinonimo)."   ";
				$record = $sql->gofirst ();
				$sinonimoProcura = $nomesSinonimos[$i];
				while($record !='LAST_RECORD_REACHED'){
					$idLexicoRef = $record['id_lexico'] ;
					$nocaoAnterior = $record['nocao'] ;
					$impactoAnterior = $record['impacto'] ;
					$regexp = "/<a[^>]*?>($sinonimoProcura)<\/a>/mi" ;
					$replace = "$1";
					$nocaoAtual = preg_replace($regexp,$replace,$nocaoAnterior);
					$impactoAtual = preg_replace($regexp,$replace,$impactoAnterior);
					//print ("<br>update lexico set nocao = '$nocaoAtual',impacto = '$impactoAtual' where id_lexico = $idLexicoRef ");
					$update->execute ("update lexico set nocao = '$nocaoAtual',impacto = '$impactoAtual' where id_lexico = $idLexicoRef ");
					$record = $sql->gonext() ;
				}
			}
		}
		
		
		// retira os links dos sinonimos dos cenarios
		$count = count($nomesSinonimos);
		for ($i = 0; $i < $count; $i++)
		{
			
			# [ATENCAO] Essa query pode ser melhorada com um join
			$sql->execute ("SELECT * FROM cenario WHERE id_projeto = $id_projeto ");
			if ($sql->getntuples() == 0){
				//echo "<BR> Projeto não possui lexicos -- ainda." ;
			}else{
				# Percorre todos os cenarios tirando as tag do lexico
				# a ser removido
				$record = $sql->gofirst ();
				while($record !='LAST_RECORD_REACHED'){
					$idCenarioRef = $record['id_cenario'] ;
					$objetivoAnterior = $record['objetivo'] ;
					$contextoAnterior = $record['contexto'] ;
					$atoresAnterior = $record['atores'] ;
					$recursosAnterior = $record['recursos'] ;
					$episodiosAnterior = $record['episodios'] ;
					$excecaoAnterior = $record['excecao'] ;
					$sinonimoProcura = $nomesSinonimos[$i];
					$regexp = "/<a[^>]*?>($sinonimoProcura)<\/a>/mi" ;
					$replace = "$1";
					$objetivoAtual = preg_replace($regexp,$replace,$objetivoAnterior);
					$contextoAtual = preg_replace($regexp,$replace,$contextoAnterior);
					$atoresAtual = preg_replace($regexp,$replace,$atoresAnterior);
					$recursosAtual = preg_replace($regexp,$replace,$recursosAnterior);
					$episodiosAtual = preg_replace($regexp,$replace,$episodiosAnterior);
					$excecaoAtual = preg_replace($regexp,$replace,$excecaoAnterior);
					$update->execute ("update cenario set objetivo = '$objetivoAtual',contexto = '$contextoAtual', atores = '$atoresAtual', recursos = '$recursosAtual', episodios = '$episodiosAtual', excecao = '$excecaoAtual' where id_cenario = $idCenarioRef ");
					$record = $sql->gonext() ;
				}//while
			}//if
		}//for
		
		
		
		
		/*   # Procura pelo possivel cenario que ele define
		# remove sua tag e relacionamento
		//print ("<br>cenario<br>SELECT * FROM cenario WHERE id_projeto = $id_projeto");
		//$sql->execute ("SELECT * FROM cenario WHERE titulo like '%<a title=\"Léxico\" href=\"main.php?t=l&id=$id_lexico\">$nomeLexico</a>%'");
		$sql->execute ("SELECT * FROM cenario WHERE id_projeto = $id_projeto");
		
		if($sql->getntuples() != 0){
		$record = $sql->gofirst ();
		while($record !='LAST_RECORD_REACHED'){
		//$record = $sql->gofirst ();
		$idCenarioRef = $record['id_cenario'] ;
		$tituloAnterior = $record['titulo'] ;
		$objetivoAnterior = $record['objetivo'] ;
		$atoresAnterior = $record['atores'] ;
		$contextoAnterior = $record['contexto'] ;
		$recursosAnterior = $record['recursos'] ;
		$excecaoAnterior = $record['excecao'] ;
		$episodiosAnterior = $record['episodios'] ;
		$tiratag = "'<[\/\!]*?[^<>]*?>'si" ;
		$tiratagreplace = "";
		$tituloAtual = preg_replace($tiratag,$tiratagreplace,$tituloAnterior);
		$objetivoAtual = preg_replace($tiratag,$tiratagreplace,$objetivoAnterior);
		$contextoAtual = preg_replace($tiratag,$tiratagreplace,$contextoAnterior);
		$atoresAtual = preg_replace($tiratag,$tiratagreplace,$atoresAnterior);
		$recursosAtual = preg_replace($tiratag,$tiratagreplace,$recursosAnterior);
		$excecaoAtual = preg_replace($tiratag,$tiratagreplace,$excecaoAnterior);
		$episodiosAtual = preg_replace($tiratag,$tiratagreplace,$episodiosAnterior);
		//print("<br>i update cenario set titulo = '$tituloAtual',objetivo = '$objetivoAtual',atores = '$atoresAtual',recursos = '$recursosAtual', episodios = '$episodiosAtual' where id_cenario = $idCenarioRef ");
		$update->execute ("update cenario set titulo = '$tituloAtual',objetivo = '$objetivoAtual',contexto = '$contextoAtual',excecao = '$excecaoAtual', atores = '$atoresAtual',recursos = '$recursosAtual', episodios = '$episodiosAtual' where id_cenario = $idCenarioRef ") ;
		$delete->execute ("DELETE FROM centolex WHERE id_cenario = $idCenarioRef") ;
		$record = $sql->gonext() ;
		}
		}*/
		
		# Remove o relacionamento entre o lexico a ser removido
		# e outros lexicos que o referenciam
		$delete->execute ("DELETE FROM lextolex WHERE id_lexico_from = $id_lexico") ;
		$delete->execute ("DELETE FROM lextolex WHERE id_lexico_to = $id_lexico") ;
		$delete->execute ("DELETE FROM centolex WHERE id_lexico = $id_lexico") ;
		
		# Remove o lexico escolhido
		$delete->execute ("DELETE FROM sinonimo WHERE id_lexico = $id_lexico") ;
		$delete->execute ("DELETE FROM lexico WHERE id_lexico = $id_lexico") ;
	}
}

###################################################################
# Essa funcao recebe um id de conceito e remove todos os seus
# links e relacionamentos existentes.
###################################################################
if (!(function_exists("removeConceito"))) {
	function removeConceito($id_projeto, $id_conceito){
		$DB = new PGDB () ;
		$sql = new QUERY ($DB) ;
		$sql2 = new QUERY ($DB) ;
		$sql3 = new QUERY ($DB) ;
		$sql4 = new QUERY ($DB) ;
		$sql5 = new QUERY ($DB) ;
		$sql6 = new QUERY ($DB) ;
		$sql7 = new QUERY ($DB) ;
		# Este select procura o cenario a ser removido
		# dentro do projeto
		
		$sql2->execute ("SELECT * FROM conceito WHERE id_projeto = $id_projeto and id_conceito = $id_conceito") ;
		if ($sql2->getntuples() == 0){
			//echo "<BR> Cenario nao existe para esse projeto." ;
		}else{
			$record = $sql2->gofirst ();
			$nomeConceito = $record['nome'] ;
			# tituloCenario = Nome do cenario com id = $id_cenario
		}
		# [ATENCAO] Essa query pode ser melhorada com um join
		//print("<br>SELECT * FROM cenario WHERE id_projeto = $id_projeto");
		/*  $sql->execute ("SELECT * FROM cenario WHERE id_projeto = $id_projeto AND id_cenario != $tituloCenario");
		if ($sql->getntuples() == 0){
		echo "<BR> Projeto não possui cenarios." ;
		}else{*/
		$qr = "SELECT * FROM conceito WHERE id_projeto = $id_projeto AND id_conceito != $id_conceito";
		//echo($qr)."          ";
		$qrr = mysql_query($qr) or die("Erro ao enviar a query de SELECT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		while ($result = mysql_fetch_array($qrr))
		{
			# Percorre todos os cenarios tirando as tag do conceito
			# a ser removido
			//$record = $sql->gofirst ();
			//while($record !='LAST_RECORD_REACHED'){
			$idConceitoRef = $result['id_conceito'] ;
			$nomeAnterior = $result['nome'] ;
			$descricaoAnterior = $result['descricao'] ;
			$namespaceAnterior = $result['namespace'] ;
			#echo        "/<a title=\"Cenário\" href=\"main.php?t='c'&id=$id_cenario>($tituloCenario)<\/a>/mi"  ;
			#$episodiosAnterior = "<a title=\"Cenário\" href=\"main.php?t=c&id=38\">robin</a>" ;
			/*"'<a title=\"Cenário\" href=\"main.php?t=c&id=38\">robin<\/a>'si" ; */
			$tiratag = "'<[\/\!]*?[^<>]*?>'si" ;
			//$tiratagreplace = "";
			//$tituloCenario = preg_replace($tiratag,$tiratagreplace,$tituloCenario);
			$regexp = "/<a[^>]*?>($nomeConceito)<\/a>/mi" ;//rever
			$replace = "$1";
			//echo($episodiosAnterior)."   ";
			//$tituloAtual = $tituloAnterior ;
			//*$tituloAtual = preg_replace($regexp,$replace,$tituloAnterior);*/
			$descricaoAtual = preg_replace($regexp,$replace,$descricaoAnterior);
			$namespaceAtual = preg_replace($regexp,$replace,$namespaceAnterior);
			/*echo "ant:".$episodiosAtual ;
			echo "<br>" ;
			echo "dep:".$episodiosAnterior ;*/
			// echo($tituloCenario)."   ";
			// echo($episodiosAtual)."  ";
			//print ("<br>update cenario set objetivo = '$objetivoAtual',contexto = '$contextoAtual',atores = '$atoresAtual',recursos = '$recursosAtual',episodios = '$episodiosAtual' where id_cenario = $idCenarioRef ");
			$sql7->execute ("update conceito set descricao = '$descricaoAtual', namespace = '$namespaceAtual' where id_conceito = $idConceitoRef ");
			
			//$record = $sql->gonext() ;
			// }
		}
		
		# Remove o conceito escolhido
		$sql6->execute ("DELETE FROM conceito WHERE id_conceito = $id_conceito") ;
		$sql6->execute ("DELETE FROM relacao_conceito WHERE id_conceito = $id_conceito") ;
		
	}
	
}
###################################################################
# Essa funcao recebe um id de relacao e remove todos os seus
# links e relacionamentos existentes.
###################################################################
if (!(function_exists("removeRelacao"))) {
	function removeRelacao($id_projeto, $id_relacao){
		$DB = new PGDB () ;

		$sql6 = new QUERY ($DB) ;
		
		# Remove o conceito escolhido
		$sql6->execute ("DELETE FROM relacao WHERE id_relacao = $id_relacao") ;
		$sql6->execute ("DELETE FROM relacao_conceito WHERE id_relacao = $id_relacao") ;
		
	}
	
}

###################################################################
# Funcao faz um select na tabela lexico.
# Para inserir um novo lexico, deve ser verificado se ele ja existe,
# ou se existe um sinonimo com o mesmo nome.
# Recebe o id do projeto e o nome do lexico (1.0)
# Faz um SELECT na tabela lexico procurando por um nome semelhante
# no projeto (1.1)
# Faz um SELECT na tabela sinonimo procurando por um nome semelhante
# no projeto (1.2)
# retorna true caso nao exista ou false caso exista (1.3)
###################################################################
function checarLexicoExistente($projeto, $nome)
{
	$naoexiste = false;
	
	$r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
	$q = "SELECT * FROM lexico WHERE id_projeto = $projeto AND nome = '$nome' ";
	$qr = mysql_query($q) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
	$resultArray = mysql_fetch_array($qr);
	if ( $resultArray == false )
	{
		$naoexiste = true;
	}
	
	$q = "SELECT * FROM sinonimo WHERE id_projeto = $projeto AND nome = '$nome' ";
	$qr = mysql_query($q) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
	$resultArray = mysql_fetch_array($qr);
	
	if ( $resultArray != false )
	{
		$naoexiste = false;
	}
	
	return $naoexiste;
	
	
}


###################################################################
# Recebe o id do projeto e a lista de sinonimos (1.0)
# Funcao faz um select na tabela sinonimo.
# Para verificar se ja existe um sinonimo igual no BD.
# Faz um SELECT na tabela lexico para verificar se ja existe
# um lexico com o mesmo nome do sinonimo.(1.1)
# retorna true caso nao exista ou false caso exista (1.2)
###################################################################
function checarSinonimo($projeto, $listSinonimo)
{
	$naoexiste = true;
	
	$r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
	
	foreach($listSinonimo as $sinonimo){
		
		$q = "SELECT * FROM sinonimo WHERE id_projeto = $projeto AND nome = '$sinonimo' ";
		$qr = mysql_query($q) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		$resultArray = mysql_fetch_array($qr);
		if ( $resultArray != false )
		{
			$naoexiste = false;
			return $naoexiste;
		}
		
		$q = "SELECT * FROM lexico WHERE id_projeto = $projeto AND nome = '$sinonimo' ";
		$qr = mysql_query($q) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		$resultArray = mysql_fetch_array($qr);
		if ( $resultArray != false )
		{
			$naoexiste = false;
			return $naoexiste;
		}
	}
	
	return $naoexiste;
	
	
}



###################################################################
# Funcao faz um select na tabela cenario.
# Para inserir um novo cenario, deve ser verificado se ele ja existe.
# Recebe o id do projeto e o titulo do cenario (1.0)
# Faz um SELECT na tabela cenario procurando por um nome semelhante
# no projeto (1.2)
# retorna true caso nao exista ou false caso exista (1.3)
###################################################################
function checarCenarioExistente($projeto, $titulo)
{
	$naoexiste = false;
	
	$r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
	$q = "SELECT * FROM cenario WHERE id_projeto = $projeto AND titulo = '$titulo' ";
	$qr = mysql_query($q) or die("Erro ao enviar a query de select no cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
	$resultArray = mysql_fetch_array($qr);
	if ( $resultArray == false )
	{
		$naoexiste = true;
	}
	
	return $naoexiste;
	
	
}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para inserir um novo cenario ela deve receber os campos do novo
# cenario.
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este cenario caso o criador não seja o gerente.
# Arquivos que utilizam essa funcao:
# add_cenario.php
###################################################################
if (!(function_exists("inserirPedidoAdicionarCenario"))) {
	function inserirPedidoAdicionarCenario($id_projeto, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios, $id_usuario)
	{
		$DB = new PGDB();
		$insere  = new QUERY($DB);
		$select  = new QUERY($DB);
		$select2 = new QUERY($DB);
		
		$q = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $id_projeto";
		$qr = mysql_query($q) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		$resultArray = mysql_fetch_array($qr);
		
		
		if ( $resultArray == false ) //nao e gerente
		{
			$insere->execute("INSERT INTO pedidocen (id_projeto, titulo, objetivo, contexto, atores, recursos, excecao, episodios, id_usuario, tipo_pedido, aprovado) VALUES ($id_projeto, '$titulo', '$objetivo', '$contexto', '$atores', '$recursos', '$excecao', '$episodios', $id_usuario, 'inserir', 0)");
			$select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
			$select2->execute("SELECT * FROM participa WHERE gerente = 1 AND id_projeto = $id_projeto");
			$record = $select->gofirst();
			$nome = $record['nome'];
			$email = $record['email'];
			$record2 = $select2->gofirst();
			while($record2 != 'LAST_RECORD_REACHED') {
				$id = $record2['id_usuario'];
				$select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
				$record = $select->gofirst();
				$mailGerente = $record['email'];
				mail("$mailGerente", "Pedido de Inclusão Cenário", "O usuario do sistema $nome\nPede para inserir o cenario $titulo \nObrigado!","From: $nome\r\n"."Reply-To: $email\r\n");
				$record2 = $select2->gonext();
			}
		}
		else{ //Eh gerente
		adicionar_cenario($id_projeto, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios) ;
		}
	}
}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para alterar um cenario ela deve receber os campos do cenario
# jah modificados.(1.1)
# Ao final ela manda um e-mail para o gerentes do projeto
# referente a este cenario caso o criador não seja o gerente.(2.1)
# Arquivos que utilizam essa funcao:
# alt_cenario.php
###################################################################
if (!(function_exists("inserirPedidoAlterarCenario"))) {
	function inserirPedidoAlterarCenario($id_projeto, $id_cenario, $titulo, $objetivo, $contexto, $atores, $recursos,$excecao, $episodios, $justificativa, $id_usuario) {
		$DB = new PGDB();
		$insere = new QUERY($DB);
		$select = new QUERY($DB);
		$select2 = new QUERY($DB);
		
		$q = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $id_projeto";
		$qr = mysql_query($q) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		$resultArray = mysql_fetch_array($qr);
		
		
		if ( $resultArray == false ) //nao e gerente
		{
			
			$insere->execute("INSERT INTO pedidocen (id_projeto, id_cenario, titulo, objetivo, contexto, atores, recursos, excecao, episodios, id_usuario, tipo_pedido, aprovado, justificativa) VALUES ($id_projeto, $id_cenario, '$titulo', '$objetivo', '$contexto', '$atores', '$recursos', '$excecao', '$episodios', $id_usuario, 'alterar', 0, '$justificativa')");
			$select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
			$select2->execute("SELECT * FROM participa WHERE gerente = 1 AND id_projeto = $id_projeto");
			$record = $select->gofirst();
			$nome = $record['nome'];
			$email = $record['email'];
			$record2 = $select2->gofirst();
			while($record2 != 'LAST_RECORD_REACHED') {
				$id = $record2['id_usuario'];
				$select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
				$record = $select->gofirst();
				$mailGerente = $record['email'];
				mail("$mailGerente", "Pedido de Alteração Cenário", "O usuario do sistema $nome\nPede para alterar o cenario $titulo \nObrigado!","From: $nome\r\n"."Reply-To: $email\r\n");
				$record2 = $select2->gonext();
			}
		}
		else{ //Eh gerente
		
		removeCenario($id_projeto,$id_cenario) ;
		adicionar_cenario($id_projeto, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios) ;
		
		}
	}
}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para remover um cenario ela deve receber
# o id do cenario e id projeto.(1.1)
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este lexico.(2.1)
# Arquivos que utilizam essa funcao:
# rmv_cenario.php
###################################################################
if (!(function_exists("inserirPedidoRemoverCenario"))) {
	function inserirPedidoRemoverCenario($id_projeto, $id_cenario, $id_usuario) {
		$DB = new PGDB();
		$insere = new QUERY($DB);
		$select = new QUERY($DB);
		$select2 = new QUERY($DB);
		$select->execute("SELECT * FROM cenario WHERE id_cenario = $id_cenario");
		$cenario = $select->gofirst();
		$titulo = $cenario['titulo'];
		$insere->execute("INSERT INTO pedidocen (id_projeto, id_cenario, titulo, id_usuario, tipo_pedido, aprovado) VALUES ($id_projeto, $id_cenario, '$titulo', $id_usuario, 'remover', 0)");
		$select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
		$select2->execute("SELECT * FROM participa WHERE gerente = 1 AND id_projeto = $id_projeto");
		$record = $select->gofirst();
		$nome = $record['nome'];
		$email = $record['email'];
		$record2 = $select2->gofirst();
		while($record2 != 'LAST_RECORD_REACHED') {
			$id = $record2['id_usuario'];
			$select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
			$record = $select->gofirst();
			$mailGerente = $record['email'];
			mail("$mailGerente", "Pedido de Remover Cenário", "O usuario do sistema $nome\nPede para remover o cenario $id_cenario \nObrigado!", "From: $nome\r\n" . "Reply-To: $email\r\n");
			$record2 = $select2->gonext();
		}
	}
}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para inserir um novo lexico ela deve receber os campos do novo
# lexicos.
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este lexico caso o criador não seja o gerente.
# Arquivos que utilizam essa funcao:
# add_lexico.php
###################################################################
if (!(function_exists("inserirPedidoAdicionarLexico"))) {
	function inserirPedidoAdicionarLexico($id_projeto,$nome,$nocao,$impacto,$id_usuario,$sinonimos, $classificacao){
		
		$DB = new PGDB() ;
		$insere = new QUERY($DB) ;
		$select = new QUERY($DB) ;
		$select2 = new QUERY($DB) ;
		
		$q = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $id_projeto";
		$qr = mysql_query($q) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		$resultArray = mysql_fetch_array($qr);
		
		
		if ( $resultArray == false ) //nao e gerente
		{
			
			$insere->execute("INSERT INTO pedidolex (id_projeto,nome,nocao,impacto,tipo,id_usuario,tipo_pedido,aprovado) VALUES ($id_projeto,'$nome','$nocao','$impacto','$classificacao',$id_usuario,'inserir',0)") ;
			
			$newId = $insere->getLastId();
			
			$select->execute("SELECT * FROM usuario WHERE id_usuario = '$id_usuario'") ;
			
			$select2->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $id_projeto") ;
			
			
			//insere sinonimos
			
			foreach($sinonimos as $sin){
				
				$insere->execute("INSERT INTO sinonimo (id_pedidolex, nome, id_projeto) VALUES ($newId, '$sin', $id_projeto)");
			}
			//fim da insercao dos sinonimos
			
			if ($select->getntuples() == 0 &&$select2->getntuples() == 0){
				echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail." ;
			}else{
				
				$record = $select->gofirst ();
				$nome2 = $record['nome'] ;
				$email = $record['email'] ;
				$record2 = $select2->gofirst ();
				while($record2 != 'LAST_RECORD_REACHED'){
					$id = $record2['id_usuario'] ;
					$select->execute("SELECT * FROM usuario WHERE id_usuario = $id") ;
					$record = $select->gofirst ();
					$mailGerente = $record['email'] ;
					mail("$mailGerente", "Pedido de Inclusão de Léxico", "O usuario do sistema $nome2\nPede para inserir o lexico $nome \nObrigado!","From: $nome2\r\n"."Reply-To: $email\r\n");
					$record2 = $select2->gonext();
					
					
				}
			}
			
		}else{ //Eh gerente
		adicionar_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao) ;
		
		}
	}
}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para alterar um lexico ela deve receber os campos do lexicos
# jah modificados.(1.1)
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este lexico caso o criador não seja o gerente.(2.1)
# Arquivos que utilizam essa funcao:
# alt_lexico.php
###################################################################
if (!(function_exists("inserirPedidoAlterarLexico"))) {
	function inserirPedidoAlterarLexico($id_projeto,$id_lexico,$nome,$nocao,$impacto,$justificativa,$id_usuario, $sinonimos, $classificacao){
		
		$DB = new PGDB () ;
		$insere = new QUERY ($DB) ;
		$select = new QUERY ($DB) ;
		$select2 = new QUERY ($DB) ;
		
		$q = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $id_projeto";
		$qr = mysql_query($q) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		$resultArray = mysql_fetch_array($qr);
		
		
		if ( $resultArray == false ) //nao e gerente
		{
			
			
			//print("INSERT INTO pedidolex (id_projeto,id_lexico,nome,nocao,impacto,id_usuario,tipo_pedido,aprovado) VALUES ($id_projeto,$id_lexico,'$nome','$nocao','$impacto',$id_usuario,'alterar',0)");
			$insere->execute("INSERT INTO pedidolex (id_projeto,id_lexico,nome,nocao,impacto,id_usuario,tipo_pedido,aprovado,justificativa, tipo) VALUES ($id_projeto,$id_lexico,'$nome','$nocao','$impacto',$id_usuario,'alterar',0,'$justificativa', '$classificacao')") ;
			
			$newPedidoId = $insere->getLastId();
			
			//sinonimos
			foreach($sinonimos as $sin){
				
				$insere->execute("INSERT INTO sinonimo (id_pedidolex,nome,id_projeto) VALUES ($newPedidoId,'$sin', $id_projeto)") ;
				
			}
			
			
			$select->execute("SELECT * FROM usuario WHERE id_usuario = '$id_usuario'") ;
			$select2->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $id_projeto") ;
			
			if ($select->getntuples() == 0 && $select2->getntuples() == 0){
				echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail." ;
			}else{
				$record = $select->gofirst ();
				$nome2 = $record['nome'] ;
				$email = $record['email'] ;
				$record2 = $select2->gofirst ();
				while($record2 != 'LAST_RECORD_REACHED'){
					$id = $record2['id_usuario'] ;
					$select->execute("SELECT * FROM usuario WHERE id_usuario = $id") ;
					$record = $select->gofirst ();
					$mailGerente = $record['email'] ;
					mail("$mailGerente", "Pedido de Alterar Léxico", "O usuario do sistema $nome2\nPede para alterar o lexico $nome \nObrigado!","From: $nome2\r\n"."Reply-To: $email\r\n");
					$record2 = $select2->gonext();
				}
			}
		}
		else{ //Eh gerente
		
		removeLexico($id_projeto,$id_lexico);
		adicionar_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao) ;
		}
		
	}
}
###################################################################
# Funcao faz um insert na tabela de pedido.
# Para remover um lexico ela deve receber
# o id do lexico e id projeto.(1.1)
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este lexico.(2.1)
# Arquivos que utilizam essa funcao:
# rmv_lexico.php
###################################################################
if (!(function_exists("inserirPedidoRemoverLexico"))) {
	function inserirPedidoRemoverLexico($id_projeto,$id_lexico,$id_usuario){
		$DB = new PGDB () ;
		$insere = new QUERY ($DB) ;
		$select = new QUERY ($DB) ;
		$select2 = new QUERY ($DB) ;
		$select->execute("SELECT * FROM lexico WHERE id_lexico = $id_lexico") ;
		$lexico = $select->gofirst ();
		$nome = $lexico['nome'] ;
		
		//print("INSERT INTO pedidolex (id_projeto,id_lexico,nome,id_usuario,tipo_pedido,aprovado) VALUES ($id_projeto,$id_lexico,'$nome',$id_usuario,'remover',0)");
		$insere->execute("INSERT INTO pedidolex (id_projeto,id_lexico,nome,id_usuario,tipo_pedido,aprovado) VALUES ($id_projeto,$id_lexico,'$nome',$id_usuario,'remover',0)") ;
		$select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario") ;
		$select2->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $id_projeto") ;
		
		if ($select->getntuples() == 0&&$select2->getntuples() == 0){
			echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail." ;
		}else{
			$record = $select->gofirst ();
			$nome = $record['nome'] ;
			$email = $record['email'] ;
			$record2 = $select2->gofirst ();
			while($record2 != 'LAST_RECORD_REACHED'){
				$id = $record2['id_usuario'] ;
				$select->execute("SELECT * FROM usuario WHERE id_usuario = $id") ;
				$record = $select->gofirst ();
				$mailGerente = $record['email'] ;
				mail("$mailGerente", "Pedido de Remover Léxico", "O usuario do sistema $nome2\nPede para remover o lexico $id_lexico \nObrigado!","From: $nome\r\n"."Reply-To: $email\r\n");
				$record2 = $select2->gonext();
			}
		}
	}
}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para alterar um conceito ela deve receber os campos do conceito
# jah modificados.(1.1)
# Ao final ela manda um e-mail para o gerentes do projeto
# referente a este cenario caso o criador não seja o gerente.(2.1)
# Arquivos que utilizam essa funcao:
# alt_cenario.php
###################################################################
if (!(function_exists("inserirPedidoAlterarCenario"))) {
	function inserirPedidoAlterarConceito($id_projeto, $id_conceito, $nome, $descricao, $namespace, $justificativa, $id_usuario) {
		$DB = new PGDB();
		$insere = new QUERY($DB);
		$select = new QUERY($DB);
		$select2 = new QUERY($DB);
		
		$q = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $id_projeto";
		$qr = mysql_query($q) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		$resultArray = mysql_fetch_array($qr);
		
		
		if ( $resultArray == false ) //nao e gerente
		{
			
			$insere->execute("INSERT INTO pedidocon (id_projeto, id_conceito, nome, descricao, namespace, id_usuario, tipo_pedido, aprovado, justificativa) VALUES ($id_projeto, $id_conceito, '$nome', '$descricao', '$namespace', $id_usuario, 'alterar', 0, '$justificativa')");
			$select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
			$select2->execute("SELECT * FROM participa WHERE gerente = 1 AND id_projeto = $id_projeto");
			$record = $select->gofirst();
			$nomeUsuario = $record['nome'];
			$email = $record['email'];
			$record2 = $select2->gofirst();
			while($record2 != 'LAST_RECORD_REACHED') {
				$id = $record2['id_usuario'];
				$select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
				$record = $select->gofirst();
				$mailGerente = $record['email'];
				mail("$mailGerente", "Pedido de Alteração Conceito", "O usuario do sistema $nomeUsuario\nPede para alterar o conceito $nome \nObrigado!","From: $nomeUsuario\r\n"."Reply-To: $email\r\n");
				$record2 = $select2->gonext();
			}
		}
		else{ //Eh gerente
		
		removeConceito($id_projeto,$id_conceito) ;
		adicionar_conceito($id_projeto, $nome, $descricao, $namespace) ;
		
		}
	}
}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para remover um conceito ela deve receber
# o id do conceito e id projeto.(1.1)
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este conceito.(2.1)
# Arquivos que utilizam essa funcao:
# rmv_conceito.php
###################################################################
if (!(function_exists("inserirPedidoRemoverConceito"))) {
	function inserirPedidoRemoverConceito($id_projeto,$id_conceito,$id_usuario){
		$DB = new PGDB () ;
		$insere = new QUERY ($DB) ;
		$select = new QUERY ($DB) ;
		$select2 = new QUERY ($DB) ;
		$select->execute("SELECT * FROM conceito WHERE id_conceito = $id_conceito") ;
		$conceito = $select->gofirst ();
		$nome = $conceito['nome'] ;
		
		$insere->execute("INSERT INTO pedidocon (id_projeto,id_conceito,nome,id_usuario,tipo_pedido,aprovado) VALUES ($id_projeto,$id_conceito,'$nome',$id_usuario,'remover',0)") ;
		$select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario") ;
		$select2->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $id_projeto") ;
		
		if ($select->getntuples() == 0&&$select2->getntuples() == 0){
			echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail." ;
		}else{
			$record = $select->gofirst ();
			$nome = $record['nome'] ;
			$email = $record['email'] ;
			$record2 = $select2->gofirst ();
			while($record2 != 'LAST_RECORD_REACHED'){
				$id = $record2['id_usuario'] ;
				$select->execute("SELECT * FROM usuario WHERE id_usuario = $id") ;
				$record = $select->gofirst ();
				$mailGerente = $record['email'] ;
				mail("$mailGerente", "Pedido de Remover Conceito", "O usuario do sistema $nome2\nPede para remover o conceito $id_conceito \nObrigado!","From: $nome\r\n"."Reply-To: $email\r\n");
				$record2 = $select2->gonext();
			}
		}
	}
}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para remover uma relacao ela deve receber
# o id da relacao e id projeto.(1.1)
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este relacao.(2.1)
# Arquivos que utilizam essa funcao:
# rmv_relacao.php
###################################################################
if (!(function_exists("inserirPedidoRemoverRelacao"))) {
	function inserirPedidoRemoverRelacao($id_projeto,$id_relacao,$id_usuario){
		$DB = new PGDB () ;
		$insere = new QUERY ($DB) ;
		$select = new QUERY ($DB) ;
		$select2 = new QUERY ($DB) ;
		$select->execute("SELECT * FROM relacao WHERE id_relacao = $id_relacao") ;
		$relacao = $select->gofirst ();
		$nome = $relacao['nome'] ;
		
		$insere->execute("INSERT INTO pedidorel (id_projeto,id_relacao,nome,id_usuario,tipo_pedido,aprovado) VALUES ($id_projeto,$id_relacao,'$nome',$id_usuario,'remover',0)") ;
		$select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario") ;
		$select2->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $id_projeto") ;
		
		if ($select->getntuples() == 0&&$select2->getntuples() == 0){
			echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail." ;
		}else{
			$record = $select->gofirst ();
			$nome = $record['nome'] ;
			$email = $record['email'] ;
			$record2 = $select2->gofirst ();
			while($record2 != 'LAST_RECORD_REACHED'){
				$id = $record2['id_usuario'] ;
				$select->execute("SELECT * FROM usuario WHERE id_usuario = $id") ;
				$record = $select->gofirst ();
				$mailGerente = $record['email'] ;
				mail("$mailGerente", "Pedido de Remover Conceito", "O usuario do sistema $nome2\nPede para remover o conceito $id_relacao \nObrigado!","From: $nome\r\n"."Reply-To: $email\r\n");
				$record2 = $select2->gonext();
			}
		}
	}
}

###################################################################
# Processa um pedido identificado pelo seu id.
# Recebe o id do pedido.(1.1)
# Faz um select para pegar o pedido usando o id recebido.(1.2)
# Pega o campo tipo_pedido.(1.3)
# Se for para remover: Chamamos a funcao remove();(1.4)
# Se for para alterar: Devemos (re)mover o cenario e inserir o novo.
# Se for para inserir: chamamos a funcao insert();
###################################################################
if (!(function_exists("tratarPedidoCenario"))) {
	function tratarPedidoCenario($id_pedido){
		$DB = new PGDB () ;
		$select = new QUERY ($DB) ;
		$delete = new QUERY ($DB) ;
		//print("<BR>SELECT * FROM pedidocen WHERE id_pedido = $id_pedido");
		$select->execute("SELECT * FROM pedidocen WHERE id_pedido = $id_pedido") ;
		if ($select->getntuples() == 0){
			echo "<BR> [ERRO]Pedido invalido." ;
		}else{
			$record = $select->gofirst () ;
			$tipoPedido = $record['tipo_pedido'] ;
			if(!strcasecmp($tipoPedido,'remover')){
				$id_cenario = $record['id_cenario'] ;
				$id_projeto = $record['id_projeto'] ;
				removeCenario($id_projeto,$id_cenario) ;
				//$delete->execute ("DELETE FROM pedidocen WHERE id_cenario = $id_cenario") ;
			}else{
				
				$id_projeto = $record['id_projeto'] ;
				$titulo     = $record['titulo'] ;
				$objetivo   = $record['objetivo'] ;
				$contexto   = $record['contexto'] ;
				$atores     = $record['atores'] ;
				$recursos   = $record['recursos'] ;
				$excecao    = $record['excecao'] ;
				$episodios  = $record['episodios'] ;
				if(!strcasecmp($tipoPedido,'alterar')){
					$id_cenario = $record['id_cenario'] ;
					removeCenario($id_projeto,$id_cenario) ;
					//$delete->execute ("DELETE FROM pedidocen WHERE id_cenario = $id_cenario") ;
				}
				adicionar_cenario($id_projeto, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios) ;
			}
			//$delete->execute ("DELETE FROM pedidocen WHERE id_pedido = $id_pedido") ;
		}
	}
}
###################################################################
# Processa um pedido identificado pelo seu id.
# Recebe o id do pedido.(1.1)
# Faz um select para pegar o pedido usando o id recebido.(1.2)
# Pega o campo tipo_pedido.(1.3)
# Se for para remover: Chamamos a funcao remove();(1.4)
# Se for para alterar: Devemos (re)mover o lexico e inserir o novo.
# Se for para inserir: chamamos a funcao insert();
###################################################################
if (!(function_exists("tratarPedidoLexico"))) {
	function tratarPedidoLexico($id_pedido){
		$DB = new PGDB () ;
		$select = new QUERY ($DB) ;
		$delete = new QUERY ($DB);
		$selectSin = new QUERY ($DB);
		$select->execute("SELECT * FROM pedidolex WHERE id_pedido = $id_pedido") ;
		if ($select->getntuples() == 0){
			echo "<BR> [ERRO]Pedido invalido." ;
		}else{
			$record = $select->gofirst () ;
			$tipoPedido = $record['tipo_pedido'] ;
			if(!strcasecmp($tipoPedido,'remover')){
				$id_lexico = $record['id_lexico'] ;
				$id_projeto = $record['id_projeto'] ;
				//echo ("removeLexico\n");
				removeLexico($id_projeto,$id_lexico) ;
				//$delete->execute ("DELETE FROM pedidolex WHERE id_lexico = $id_lexico") ;
			}else{
				
				$id_projeto = $record['id_projeto'] ;
				$nome = $record['nome'] ;
				$nocao = $record['nocao'] ;
				$impacto = $record['impacto'] ;
				$classificacao = $record['tipo'];
				
				//sinonimos
				
				$sinonimos = array();
				
				$selectSin->execute("SELECT nome FROM sinonimo WHERE id_pedidolex = $id_pedido");
				
				$sinonimo = $selectSin->gofirst();
				
				while($sinonimo != 'LAST_RECORD_REACHED'){
					
					$sinonimos[] = $sinonimo["nome"];
					
					$sinonimo = $selectSin->gonext();
				}
				
				if(!strcasecmp($tipoPedido,'alterar')){
					$id_lexico = $record['id_lexico'] ;
					removeLexico($id_projeto,$id_lexico) ;
					//$delete->execute ("DELETE FROM pedidolex WHERE id_lexico = $id_lexico") ;
				}
				// adicionar_lexico($id_projeto, $nome, $nocao, $impacto) ;
				
				
				if(($idLexicoConflitante = adicionar_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao)) <= 0)
				{
					$idLexicoConflitante = -1 * $idLexicoConflitante;
					
					$selectLexConflitante->execute("SELECT nome FROM lexico WHERE id_lexico = " . $idLexicoConflitante);
					
					$row = $selectLexConflitante->gofirst();
					
					return $row["nome"];
				}
				
				
				
			}
			return null;
			//$delete->execute ("DELETE FROM pedidolex WHERE id_pedido = $id_pedido") ;
		}
	}
}
###################################################################
# Processa um pedido identificado pelo seu id.
# Recebe o id do pedido.(1.1)
# Faz um select para pegar o pedido usando o id recebido.(1.2)
# Pega o campo tipo_pedido.(1.3)
# Se for para remover: Chamamos a funcao remove();(1.4)
# Se for para alterar: Devemos (re)mover o cenario e inserir o novo.
# Se for para inserir: chamamos a funcao insert();
###################################################################
if (!(function_exists("tratarPedidoConceito"))) {
	function tratarPedidoConceito($id_pedido){
		$DB = new PGDB () ;
		$select = new QUERY ($DB) ;
		$delete = new QUERY ($DB) ;
		$select->execute("SELECT * FROM pedidocon WHERE id_pedido = $id_pedido") ;
		if ($select->getntuples() == 0){
			echo "<BR> [ERRO]Pedido invalido." ;
		}else{
			$record = $select->gofirst () ;
			$tipoPedido = $record['tipo_pedido'] ;
			if(!strcasecmp($tipoPedido,'remover')){
				$id_conceito = $record['id_conceito'] ;
				$id_projeto = $record['id_projeto'] ;
				removeConceito($id_projeto,$id_conceito) ;
			}else{
				
				$id_projeto = $record['id_projeto'] ;
				$nome     	= $record['nome'] ;
				$descricao  = $record['descricao'] ;
				$namespace   = $record['namespace'] ;
				
				if(!strcasecmp($tipoPedido,'alterar')){
					$id_cenario = $record['id_conceito'] ;
					removeConceito($id_projeto,$id_conceito) ;
				}
				adicionar_conceito($id_projeto, $nome, $descricao, $namespace) ;
			}
		}
	}
}

###################################################################
# Processa um pedido identificado pelo seu id.
# Recebe o id do pedido.(1.1)
# Faz um select para pegar o pedido usando o id recebido.(1.2)
# Pega o campo tipo_pedido.(1.3)
# Se for para remover: Chamamos a funcao remove();(1.4)
# Se for para alterar: Devemos (re)mover o cenario e inserir o novo.
# Se for para inserir: chamamos a funcao insert();
###################################################################
if (!(function_exists("tratarPedidoRelacao"))) {
	function tratarPedidoRelacao($id_pedido){
		$DB = new PGDB () ;
		$select = new QUERY ($DB) ;
		$delete = new QUERY ($DB) ;
		$select->execute("SELECT * FROM pedidorel WHERE id_pedido = $id_pedido") ;
		if ($select->getntuples() == 0){
			echo "<BR> [ERRO]Pedido invalido." ;
		}else{
			$record = $select->gofirst () ;
			$tipoPedido = $record['tipo_pedido'] ;
			if(!strcasecmp($tipoPedido,'remover')){
				$id_relacao = $record['id_relacao'] ;
				$id_projeto = $record['id_projeto'] ;
				removeRelacao($id_projeto,$id_relacao) ;
			}else{
				
				$id_projeto = $record['id_projeto'] ;
				$nome     	= $record['nome'] ;
								
				if(!strcasecmp($tipoPedido,'alterar')){
					$id_relacao = $record['id_relacao'] ;
					removeRelacao($id_projeto,$id_relacao) ;
				}
				adicionar_relacao($id_projeto, $nome) ;
			}
		}
	}
}
#############################################
#Deprecated by the author:
#Essa funcao deveria receber um id_projeto
#de forma a verificar se o gerente pertence
#a esse projeto.Ela so verifica atualmente
#se a pessoa e um gerente.
#############################################
if (!(function_exists("verificaGerente"))) {
	function verificaGerente($id_usuario){
		$DB = new PGDB () ;
		$select = new QUERY ($DB) ;
		$select->execute("SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario") ;
		if ($select->getntuples() == 0){
			return 0 ;
		}else{
			return 1 ;
		}
	}
}

#############################################
# Formata Data
# Recebe YYY-DD-MM
# Retorna DD-MM-YYYY
#############################################
if (!(function_exists("formataData"))) {
	function formataData($data){
		
		$novaData = substr( $data, 8, 9 ) .
		substr( $data, 4, 4 ) .
		substr( $data, 0, 4 );
		return $novaData ;
	}
}





// Retorna TRUE ssse $id_usuario eh admin de $id_projeto
if (!(function_exists("is_admin"))) {
	function is_admin($id_usuario, $id_projeto)
	{
		$r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		$q = "SELECT *
              FROM participa
              WHERE id_usuario = $id_usuario
              AND id_projeto = $id_projeto
              AND gerente = 1";
		$qrr = mysql_query($q) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		return (1 == mysql_num_rows($qrr));
	}
}

// Retorna TRUE ssse $id_usuario tem permissao sobre $id_projeto
if (!(function_exists("check_proj_perm"))) {
	function check_proj_perm($id_usuario, $id_projeto)
	{
		$r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		$q = "SELECT *
              FROM participa
              WHERE id_usuario = $id_usuario
              AND id_projeto = $id_projeto";
		$qrr = mysql_query($q) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		return (1 == mysql_num_rows($qrr));
	}
}
###################################################################
# Verifica se um determinado usuario e gerente de um determinado
# projeto
# Recebe o id do projeto. (1.1)
# Faz um select para pegar o resultArray da tabela Participa.(1.2)
# Se o resultArray for nao nulo: devolvemos TRUE(1);(1.3)
# Se o resultArray for nulo: devolvemos False(0);(1.4)
###################################################################
function verificaGerente($id_usuario, $id_projeto)
{
	$ret = 0;
	$q = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $id_projeto";
	$qr = mysql_query($q) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
	$resultArray = mysql_fetch_array($qr);
	
	if ( $resultArray != false ){
		
		$ret = 1;
	}
	return $ret;
	
}

###################################################################
# Remove um determinado projeto da base de dados
# Recebe o id do projeto. (1.1)
# Apaga os valores da tabela pedidocen que possuam o id do projeto enviado (1.2)
# Apaga os valores da tabela pedidolex que possuam o id do projeto enviado (1.3)
# Faz um SELECT para saber quais léxico pertencem ao projeto de id_projeto (1.4)
# Apaga os valores da tabela lextolex que possuam possuam lexico do projeto (1.5)
# Apaga os valores da tabela centolex que possuam possuam lexico do projeto (1.6)
# Apaga os valores da tabela sinonimo que possuam possuam o id do projeto (1.7)
# Apaga os valores da tabela lexico que possuam o id do projeto enviado (1.8)
# Faz um SELECT para saber quais cenario pertencem ao projeto de id_projeto (1.9)
# Apaga os valores da tabela centocen que possuam possuam cenarios do projeto (2.0)
# Apaga os valores da tabela centolex que possuam possuam cenarios do projeto (2.1)
# Apaga os valores da tabela cenario que possuam o id do projeto enviado (2.2)
# Apaga os valores da tabela participa que possuam o id do projeto enviado (2.3)
# Apaga os valores da tabela publicacao que possuam o id do projeto enviado (2.4)
# Apaga os valores da tabela projeto que possuam o id do projeto enviado (2.5)
#
###################################################################
function removeProjeto($id_projeto)
{
	$r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
	
	//Remove os pedidos de cenario
	$qv = "Delete FROM pedidocen WHERE id_projeto = '$id_projeto' ";
	$deletaPedidoCenario = mysql_query($qv) or die("Erro ao apagar pedidos de cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
	
	//Remove os pedidos de lexico
	$qv = "Delete FROM pedidolex WHERE id_projeto = '$id_projeto' ";
	$deletaPedidoLexico = mysql_query($qv) or die("Erro ao apagar pedidos do lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
	
	//Remove os lexicos //verificar lextolex!!!
	$qv = "SELECT * FROM lexico WHERE id_projeto = '$id_projeto' ";
	$qvr = mysql_query($qv) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
	
	while ($result = mysql_fetch_array($qvr))
	{
		$id_lexico = $result['id_lexico']; //seleciona um lexico
		
		$qv = "Delete FROM lextolex WHERE id_lexico_from = '$id_lexico' OR id_lexico_to = '$id_lexico' ";
		$deletaLextoLe = mysql_query($qv) or die("Erro ao apagar pedidos do lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		
		$qv = "Delete FROM centolex WHERE id_lexico = '$id_lexico'";
		$deletacentolex = mysql_query($qv) or die("Erro ao apagar pedidos do centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		
		//$qv = "Delete FROM sinonimo WHERE id_lexico = '$id_lexico'";
		//$deletacentolex = mysql_query($qv) or die("Erro ao apagar sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		
		$qv = "Delete FROM sinonimo WHERE id_projeto = '$id_projeto'";
		$deletacentolex = mysql_query($qv) or die("Erro ao apagar sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		
	}
	
	$qv = "Delete FROM lexico WHERE id_projeto = '$id_projeto' ";
	$deletaLexico = mysql_query($qv) or die("Erro ao apagar pedidos do lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
	
	//remove os cenarios
	$qv = "SELECT * FROM cenario WHERE id_projeto = '$id_projeto' ";
	$qvr = mysql_query($qv) or die("Erro ao enviar a query de select no cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
	$resultArrayCenario = mysql_fetch_array($qvr);
	
	while ($result = mysql_fetch_array($qvr))
	{
		$id_lexico = $result['id_cenario']; //seleciona um lexico
		
		$qv = "Delete FROM centocen WHERE id_cenario_from = '$id_cenario' OR id_cenario_to = '$id_cenario' ";
		$deletaCentoCen = mysql_query($qv) or die("Erro ao apagar pedidos do centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		
		$qv = "Delete FROM centolex WHERE id_cenario = '$id_cenario'";
		$deletaLextoLe = mysql_query($qv) or die("Erro ao apagar pedidos do centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		
		
	}
	
	$qv = "Delete FROM cenario WHERE id_projeto = '$id_projeto' ";
	$deletaLexico = mysql_query($qv) or die("Erro ao apagar pedidos do cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
	
	//remover participantes
	$qv = "Delete FROM participa WHERE id_projeto = '$id_projeto' ";
	$deletaParticipantes = mysql_query($qv) or die("Erro ao apagar no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
	
	//remover publicacao
	$qv = "Delete FROM publicacao WHERE id_projeto = '$id_projeto' ";
	$deletaPublicacao = mysql_query($qv) or die("Erro ao apagar no publicacao<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
	
	//remover projeto
	$qv = "Delete FROM projeto WHERE id_projeto = '$id_projeto' ";
	$deletaProjeto= mysql_query($qv) or die("Erro ao apagar no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
	
}
?>
