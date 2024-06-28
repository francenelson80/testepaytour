<?php
session_start();



$DB_USER = "root";
$DB_PASS = "123456";
$DB_NAME = "teste";
$DB_HOST = "localhost";


//////////////////////////////////////////////////////////////////////////////////////////////////
//-------------FUNÇÕES UTILIZADA PARA INTERAGIR COM O BANCO DE DADOS MYSQL------------------------
class Metodos
{
	
	function Consulta($query)
	{	
		global $DB_USER, $DB_PASS, $DB_HOST, $DB_NAME;	
		if (!$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME))
		{ 
			$erro_encontrado = nl2br($query).'<br><br><b>'.mysqli_error($conn).'</b>';
			//Caso ocorra erro ao executar a Query, tenta enviar por e-mail, 
			//se o envio falhar, não faz nada!
			echo('Ocorreu um erro ao tentar conectar no banco de dados.');
		}
			 
	
		$conn->set_charset("utf8");
		
		$strquery = $query;
		$resultado = mysqli_query($conn, $strquery, MYSQLI_USE_RESULT);
		if(!$resultado)
		{
			$erro_encontrado = nl2br($query).'<br><br><b>'.mysqli_error($conn).'</b>';
			//Caso ocorra erro ao executar a Query, tenta enviar por e-mail, 
			//se o envio falhar, não faz nada!
			
			
			//Exibe para o usuário que ocorreu um erro na consulta
			echo('<b>Ocorreu um erro na consulta ao banco de dados</b>');
				
		}

		//$linhas = array();
		while($row = mysqli_fetch_array($resultado)) {	
			$linhas[] = $row;
			//echo($linhas[0][0]);
			//echo($linhas[0][1]);
		}
		mysqli_free_result($resultado);
		mysqli_close($conn);
		//return $linha;
		return $linhas;
	} 	
	
	

	function ExecuteSQL($query)
	{		
		global $DB_USER, $DB_PASS, $DB_HOST, $DB_NAME;		 
		if (!$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME))
		{ 
			$erro_encontrado = nl2br($query).'<br><br><b>'.mysqli_error($conn).'</b>';
			//Caso ocorra erro ao executar a Query, tenta enviar por e-mail, 
			//se o envio falhar, não faz nada!
			
			echo('Ocorreu um erro ao tentar conectar no banco de dados.');
		}


		$conn->set_charset("utf8");
		$strquery = $query;
		$resultado = mysqli_query($conn, $strquery);

		
		//Aprimorando a mensagem de erro
		if (!$resultado)
		{
			$titulo_erro = 'Ocorreu um erro ao executar a instrução no banco de dados';
			$mensagem_erro = mysqli_error($conn);
			
			//Verificando se o erro ocorre por violação de constraint (apagar ou modificar pai)
			if (strpos($mensagem_erro, 'not delete or update a parent row') > 0)
			{
				$titulo_erro = 'Ocorreu um erro ao tentar apagar o cadastro:';
				$mensagem_erro = 'Há registros associados a esse cadastro.';
			}

			//Verificando se o erro ocorre por campo data no formato inválido
			if (strpos($mensagem_erro, 'ncorrect date value:') > 0)
			{
				$titulo_erro = 'Ocorreu um erro ao informar uma data:';
				$mensagem_erro = 'Alguma data não foi informada ou o seu formato é inválido.';
			}

			$erro_encontrado = $mensagem_erro.'<br><br>'.nl2br($query).'<br><br><b>'.mysqli_error($conn).'</b>';
			//Caso ocorra erro ao executar a Query, tenta enviar por e-mail, 
			//se o envio falhar, não faz nada!
									
			echo('<b>'.$titulo_erro.'</b> <br><br>'.$mensagem_erro);
		}
			
		mysqli_close($conn);
 		return true;
	}


		
} //FINAL DA CLASSSE METODOS QUE INTERAGE COM O BANCO DE DADOS MYSQL

class Banco extends Metodos
{

}





?>
