<?php

include_once("bd.inc");
include_once("bd_class.php");
include_once("seguranca.php");
include_once("bd.inc");
include_once ("../aplicacao/funcoes_genericas.php");


###################################################################
# Handles a request identified by its id.
# Receives the request id. (1.1)
# Do a select to get the request using the id received. (1.2)
# Get the field tipo_pedido. (1.3)
# If it's to remove: We call the function remove (), (ââ1.4)
# If it is to change: We (re) move the lexicon and insert the new.
# If it is to enter: call the function insert ();
###################################################################
if (!(function_exists("tratarPedidoLexico")))
{
    function tratarPedidoLexico($id_pedido)
    {
    	assert($id_pedido != null, "id_pedido must not be null");
    	
        $DB = new PGDB () ;
        $select_pedido_lexico = new QUERY ($DB) ;
        $delete = new QUERY ($DB);
        $select_sinonimo = new QUERY ($DB);
        $select_pedido_lexico->execute("SELECT * FROM pedidolex WHERE id_pedido = $id_pedido") ;
        if ($select_pedido_lexico->getntuples() == 0)
        {
            echo "<BR> [ERRO]Pedido invalido." ;
        }
        else
        {
            $record_pedido = $select_pedido_lexico->gofirst () ;
            $tipoPedido = $record_pedido['tipo_pedido'] ;
            if (!strcasecmp($tipoPedido,'remover'))
            {
                $id_lexico = $record_pedido['id_lexico'] ;
                $id_projeto = $record_pedido['id_projeto'] ;
                removeLexico($id_projeto,$id_lexico) ;
            }
            else
            {
                $id_projeto = $record_pedido['id_projeto'] ;
                $nome = $record_pedido['nome'] ;
                $nocao = $record_pedido['nocao'] ;
                $impacto = $record_pedido['impacto'] ;
                $classificacao = $record_pedido['tipo'];
                
                //sinonimos
                
                $sinonimos = array();
                $select_sinonimo->execute("SELECT nome FROM sinonimo WHERE id_pedidolex = $id_pedido");
                $sinonimo = $select_sinonimo->gofirst();
                if ($select_sinonimo->getntuples() != 0)
                {
               	    while($sinonimo != 'LAST_RECORD_REACHED')
               	    {
                        $sinonimos[] = $sinonimo["nome"];
                        $sinonimo = $select_sinonimo->gonext();
                    }
                }
                else
                {
                	//Nothing to do.
                }
                
                if (!strcasecmp($tipoPedido,'alterar'))
                {
                    $id_lexico = $record_pedido['id_lexico'] ;
                    alteraLexico($id_projeto, $id_lexico, $nome, $nocao, $impacto, $sinonimos, $classificacao);
                }
                else if(($idLexicoConflitante = adicionar_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao)) <= 0)
                {
                	assert($idLexicoConflitante != null, "idLexicoConflitante must not be null");
                	
                    $idLexicoConflitante = -1 * $idLexicoConflitante;
                    $selectLexConflitante->execute("SELECT nome FROM lexico WHERE id_lexico = " . $idLexicoConflitante);
                    $row = $selectLexConflitante->gofirst();
                    
                    return $row["nome"];
                }
                else
                {
                    //Nothing to do.
                }
            }
            
            return null;
        }
    }
}
else
{
    //Nothing to do.
}

?>
