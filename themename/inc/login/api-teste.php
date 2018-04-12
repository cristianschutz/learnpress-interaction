<?php 

	header('Content-type: text/javascript');
	if($_GET['usuario']=="admin" && $_GET['senha']=="1234" && $_GET['ambiente']=='p' && $_GET['hash_cron'] == '1234'){
		echo('{"acesso_permitido":true,"mensagem":"Usu\u00e1rio v\u00e1lido!","id":"00006373","login":"loginname","nome":"Nome Sobrenome","email":"logintest@teste.com","ativo_adesao":true,"ativo_mes_atual":false,"ativo_mes_anterior":true,"endereco":"AVENIDA HERC\u00cdLIO LUZ","numero":"1234","complemento":"Complemento aqui","bairro":"CENTRO","cidade":"Panambi","estado":"RS","cep":"98280000","pais":"Brasil","cursos":["quem-e-você-dentro-do-marketing-multinivel-8","quem-e-você-dentro-do-marketing-multinivel-7","quem-e-você-dentro-do-marketing-multinivel-2","quem-e-você-dentro-do-marketing-multinivel-4"]}');
	}else{
		echo('{"acesso_permitido":false,"mensagem":"Usu\u00e1rio ou senha inv\u00e1lidos!"}');
	}

?>