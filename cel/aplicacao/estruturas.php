<?php

$_SESSION["estruturas"] = 1;

class conceito
{
	var $nome;
	var $descricao;
	var $relacoes;
	var $subconceitos;
	var $namespace;
	
	function conceito($nome, $descricao)
	{
		$this->nome = $nome;
		$this->descricao = $descricao;
		$this->relacoes = array();
		$this->subconceitos = array(); //not initialized
		$this->namespace = "";
	}
}

class relacao_entre_conceitos
{
	var $predicados;
	var $verbo;
	
	function relacao_entre_conceitos($predicado, $verbo)
	{
		$this->predicados[] = $predicado;
		$this->verbo = $verbo;
	}
}

class termo_do_lexico
{
	var $nome;
	var $nocao;
	var $impacto;
	
	function termo_do_lexico($name, $notion, $impact)
	{
		$this->nome = $name;
		$this->nocao = $notion;
		$this->impacto = $impact;
	}
}

?>