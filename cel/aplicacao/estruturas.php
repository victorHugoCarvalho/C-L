<?php

$_SESSION["estruturas"] = 1;

class conceito
{
	var $nome;
	var $descricao;
	var $relacoes;
	var $subconceitos;
	var $namespace;
	
	function conceito($n, $d)
	{
		$this->nome = $n;
		$this->descricao = $d;
		$this->relacoes = array();
		$this->subconceitos = array(); //not initialized
		$this->namespace = "";
	}
}

class relacao_entre_conceitos
{
	var $predicados;
	var $verbo;
	
	function relacao_entre_conceitos($p, $v)
	{
		$this->predicados[] = $p;
		$this->verbo = $v;
	}
}

class termo_do_lexico
{
	var $nome;
	var $nocao;
	var $impacto;
	
	function termo_do_lexico($name, $notion, $i)
	{
		$this->nome = $name;
		$this->nocao = $notion;
		$this->impacto = $i;
	}
}

?>