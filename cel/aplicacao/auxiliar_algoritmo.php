<?php

function existe_relacao($rel, $list)
{
	foreach($list as $key=>$relacao)
	{
		if( @$relacao->verbo == $rel )
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


function existe_conceito($conc, $list)
{
	foreach($list as $key=>$conc1)
	{
		if( $conc1->nome == $conc )
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