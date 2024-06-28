<?php   
	session_start();
    //Arquivo de configuração para acesso ao BD
	require("config.php");  

	$banco = new Banco();


    /* Destinatário e remetente do email - CONFIGURE ESTE BLOCO */
    $to = "francenelson@hotmail.com";
    $from = "francenelson@gmail.com"; // Deve ser um email válido do domínio
    $replyto = "francenelson@gmail.com";


	
	$_POST["escolaridade"];
	$nome = $_POST["nome"];
	$email = $_POST["email"];
	$fone = $_POST["fone"];
	$cargo = $_POST["cargo"];
	$escolaridade = $_POST["escolaridade"];
    $obs = $_POST["obs"];
    $arquivo = $_FILES["curriculum"];


    $ip = $_SERVER["REMOTE_ADDR"];

    switch ($escolaridade) {
        case 'FUN':
            $texto_escolaridade = 'Ensino Fundamental';
            break;
        case 'MED':
            $texto_escolaridade =  'Ensino Médio';
            break;
        case 'SUP':
            $texto_escolaridade =  'Ensino Superior';
            break;
        case 'MES':
            $texto_escolaridade =  'Mestrado';
            break;
        case 'DOU':
            $texto_escolaridade =  'Doutorado';
            break;
        
        default:
            return '';
            break;
    }
    //exit("Escolaridade: ".$texto_escolaridade);

   
    
		
    //Enviando email com os dados do formulário



    /* Cabeçalho da mensagem  */
    $boundary = "XYZ-" . date("dmYis") . "-ZYX";
    $headers = "MIME-Version: 1.0\n";
    $headers.= "From: $from\n";
    $headers.= "Reply-To: $replyto\n";
    $headers.= "Content-type: multipart/mixed; boundary=\"$boundary\"\r\n";  
    $headers.= "$boundary\n"; 

    $subject = "Cadastro de curriculum: ".$nome;
    $corpo_mensagem = $nome." cadastrou seu curriculum conosco!"
                ."<br>Nome: ".$nome
                ."<br>E-mail: ".$email
                ."<br>Telefone: ".$fone
                ."<br>Cargo desejado: ".$cargo
                ."<br>Escolaridade: ".$texto_escolaridade
                ."<br>Observações: ".$obs;

    /* Função que codifica o anexo para poder ser enviado na mensagem  */
    if(file_exists($arquivo["tmp_name"]) and !empty($arquivo)){
    
        $fp = fopen($_FILES["curriculum"]["tmp_name"],"rb"); // Abre o arquivo enviado.
        $anexo = fread($fp,filesize($_FILES["curriculum"]["tmp_name"])); // Le o arquivo aberto na linha anterior
        $anexo = base64_encode($anexo); // Codifica os dados com MIME para o e-mail 
        fclose($fp); // Fecha o arquivo aberto anteriormente
        $anexo = chunk_split($anexo); // Divide a variável do arquivo em pequenos pedaços para poder enviar
        $mensagem = "--$boundary\n"; // Nas linhas abaixo possuem os parâmetros de formatação e codificação, juntamente com a inclusão do arquivo anexado no corpo da mensagem
        $mensagem.= "Content-Transfer-Encoding: 8bits\n"; 
        $mensagem.= "Content-Type: text/html; charset=\"utf-8\"\n\n";
        $mensagem.= "$corpo_mensagem\n"; 
        $mensagem.= "--$boundary\n"; 
        $mensagem.= "Content-Type: ".$arquivo["type"]."\n";  
        $mensagem.= "Content-Disposition: attachment; filename=\"".$arquivo["name"]."\"\n";  
        $mensagem.= "Content-Transfer-Encoding: base64\n\n";  
        $mensagem.= "$anexo\n";  
        $mensagem.= "--$boundary--\r\n"; 
    }
    else // Caso não tenha anexo
    {
        $mensagem = "--$boundary\n"; 
        $mensagem.= "Content-Transfer-Encoding: 8bits\n"; 
        $mensagem.= "Content-Type: text/html; charset=\"utf-8\"\n\n";
        $mensagem.= "$corpo_mensagem\n";
    }
    
    /* Função que envia a mensagem  */
    if(mail($to, $subject, $mensagem, $headers))
    {
        echo "<br><br><center><b><font color='green'>Mensagem enviada com sucesso!";
        sleep(10);
    } 
    else
    {
        echo "<br><br><center><b><font color='red'>Ocorreu um erro ao enviar a mensagem!";
        sleep(10);
    }




    
    //Upload do arquivo do curriculum
    //O arquivo sera renomeado para evitar nomes iguais
    $novo_nome_arquivo = "";
    if (ltrim($_FILES["curriculum"]['name']) != '')
    {			
        $novo_nome_arquivo = "cur_".date("Ymd-His")."_".$_FILES["curriculum"]['name'];	
        $destino = "curriculum/".$novo_nome_arquivo;	
        
        if (!(is_uploaded_file($_FILES["curriculum"]["tmp_name"])))
        {
            echo("Curriculum não carregado: ".$_FILES["curriculum"]["tmp_name"]);	
            sleep(10);
            exit();
        
        }
        else
        {
            if (!move_uploaded_file($_FILES["curriculum"]["tmp_name"], $destino))
            {			
                echo("Falha no upload do curriculum: ".$_FILES["curriculum"]["tmp_name"]);	
                sleep(10);
                exit();
            }
        };				

    };	



    //Salvando no BD
    $SQL = " 
    insert into tb_candidato
        (nome, email, telefone, cargo_desejado, escolaridade, 
        obs, nome_arquivo, dthora_envio, ip)
    values
        ('".$nome
        ."', '".$email
        ."', '".$fone
        ."', '".$cargo
        ."', '".$escolaridade
        ."', '".$obs
        ."', '".$novo_nome_arquivo
        ."', current_timestamp()"
        .", '".$ip
        ."'); ";		
    //exit($SQL);			

    $salvar = $banco->ExecuteSQL($SQL);



	
	header("Location: cadastro.html?saved=S");
	die();
?>