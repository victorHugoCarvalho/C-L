<html>
<head>
<title></title>
</head>

<body>
<?php

include_once("bd.inc") ;
include_once("CELConfig/CELConfig.inc");

	$link = bd_connect() or die("Erro na conexão à BD : " . mysql_error() . __LINE__);
	if ( $link && mysql_select_db(CELConfig_ReadVar("BD_database") ))
	{
		echo "SUCESSO NA CONEXÃO À BD <br>";
	}
    else
    {
    	echo "ERRO NA CONEXÃO À BD <br>";
    }


/* query para a adição do campo  tipo na tabela de lexicos*/
/*
$query  = "alter table lexico add tipo varchar(15) NULL after nome;";
$result = mysql_query($query) or die("A consulta à BD falhou : " . mysql_error() . __LINE__);
*/
/* query pra tirar o campo impacto da tabela lexico         */
/*
$query = "alter table lexico drop impacto;";
$result = mysql_query($query) or die("A consulta à BD falhou : " . mysql_error() . __LINE__);
*/
/* query pra criar a tabela de impactos                       */
/*
$query = "create table impacto (id_impacto int(11) not null AUTO_INCREMENT,
                                        id_lexico int(11) not null ,
                                        impacto varchar(250) not null,
                                        unique key(id_impacto),
                                        primary key(id_lexico, impacto)
                                        );";
$result = mysql_query($query) or die("A consulta à BD falhou : " . mysql_error() . __LINE__);


$query = "insert into impacto (id_impacto, id_lexico, impacto) VALUES (0,0, 'TESTE');";
$result = mysql_query($query) or die("A consulta à BD falhou : " . mysql_error() . __LINE__);


$query = "delete from impacto;";
$result = mysql_query($query) or die("A consulta à BD falhou : " . mysql_error() . __LINE__);
*/

// query para criar tabela de conceitos. __JERONIMO__
$query = "create table conceito (id_conceito int(11) not null AUTO_INCREMENT,
                                        nome varchar(250) not null ,
                                        descricao varchar(250) not null,
										pai int(11),
                                        unique key(nome),
                                        primary key(id_conceito)
                                        );";
$result = mysql_query($query) or die("A consulta à BD falhou : " . mysql_error() . __LINE__);

$query = "create table relacao_conceito (id_conceito int(11) not null,
                                        id_relacao int(11) not null,
                                        predicado varchar(250) not null
                                        );";
$result = mysql_query($query) or die("A consulta à BD falhou : " . mysql_error() . __LINE__);

$query = "create table relacao (id_relacao int(11) not null AUTO_INCREMENT,
                                        nome varchar(250) not null ,
                                        unique key(nome),
                                        primary key(id_relacao)
                                        );";
$result = mysql_query($query) or die("A consulta à BD falhou : " . mysql_error() . __LINE__);

$query = "create table axioma (id_axioma int(11) not null AUTO_INCREMENT,
                                        axioma varchar(250) not null ,
                                        unique key(axioma),
                                        primary key(id_axioma)
                                        );";
$result = mysql_query($query) or die("A consulta à BD falhou : " . mysql_error() . __LINE__);

$query = "create table algoritmo (id_variavel int(11) not null AUTO_INCREMENT,
                                        nome varchar(250) not null ,
										valor varchar(250) not null ,
                                        unique key(nome),
                                        primary key(id_variavel)
                                        );";
$result = mysql_query($query) or die("A consulta à BD falhou : " . mysql_error() . __LINE__);

mysql_close($link);

?>
</body>
</html>
