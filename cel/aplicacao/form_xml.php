<?php

session_start();

include("funcoes_genericas.php");

chkUser("index.php");        // Checa se o usuario foi autenticado

?>

<html>

<body>

    <head>
        <title>Gerar XML</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    </head><form action="gerador_xml.php" method="post">

    <h2>Propriedades do Relatório a ser Gerado:</h2>
<?php

//Cenário - Gerar Relatórios XML 

//Objetivo:    Permitir ao administrador gerar relatórios em formato XML de um projeto,
//          identificados por data.     
//Contexto:    Gerente deseja gerar um relatório para um dos projetos da qual é administrador.
//          Pré-Condição: Login, projeto cadastrado.
//Atores:    Administrador     
//Recursos:    Sistema, dados do relatório, dados cadastrados do projeto, banco de dados.     
//Episódios:O administrador clica na opção de Gerar Relatório XML.
//          Restrição: Somente o Administrador do projeto pode ter essa função visível.
//          O sistema fornece para o administrador uma tela onde deverá fornecer os dados
//          do relatório para sua posterior identificação, como data e versão. 

   $today = getdate(); 
?>

    &nbsp;Data da Versão:
    <?= $today['mday'];?>/<?= $today['mon'];?>/<?= $today['year'];?>
    <p>&nbsp;<input type="hidden" name="data_dia" size="3" value="<?= $today['mday'];?>">
    <input  type="hidden" name="data_mes" size="3" value="<?= $today['mon'];?>">
    <input type="hidden" name="data_ano" size="6" value="<?= $today['year'];?>">

    &nbsp;</p>
    Versão do XML: &nbsp;<input type="text" name="versao" size="15">
    <p>Exibir

    Formatado: <input type="checkbox" name="flag" value="ON"><br><br>

    <input type="submit" value="Gerar"> </p>

</form>
    <br><i><a href="showSource.php?file=form_xml.php">Veja o código fonte!</a></i>
</body>

</html>
