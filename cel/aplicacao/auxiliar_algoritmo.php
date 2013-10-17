<?php

function existe_relacao($relacao, $lista)
{
	foreach($lista as $key=>$relacao)
	{
		if ($relacao->verbo == $relacao )
		{
			return $key;
		}
                else
                {
                    //Nothing to do
                }
	}
	return -1;
}


function existe_conceito($conceito, $lista)
{
	foreach ($lista as $key=>$conceito)
	{
		if ($conceito->nome == $conceito)
		{
			return $key;
		}
                else
                {
                    //Nothing to do
                }
	}
	return -1;
}

?>