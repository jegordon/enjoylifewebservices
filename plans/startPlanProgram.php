<?php

	function is_valid_callback($subject)
		{
		    $identifier_syntax
		      = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';

		    $reserved_words = array('break', 'do', 'instanceof', 'typeof', 'case',
		      'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue', 
		      'for', 'switch', 'while', 'debugger', 'function', 'this', 'with', 
		      'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum', 
		      'extends', 'super', 'const', 'export', 'import', 'implements', 'let', 
		      'private', 'public', 'yield', 'interface', 'package', 'protected', 
		      'static', 'null', 'true', 'false');

		    return preg_match($identifier_syntax, $subject) && !in_array(strtolower($subject), $reserved_words);
		}
		
	// El parametro token es necesario y checamos si el callback esta correcto
	if(isset($_GET['token']) && isset($_GET['usuario']) && (is_valid_callback($_GET['callback']) ) ) {
		global $DATABASE_HOSTNAME;
		global $DATABASE_NAME;
		global $DATABASE_PORT;
		global $DATABASE_USER;
		global $DATABASE_PASS;
		global $PASSPHRASE;
		require_once '../config/variables.php';		

		$token = $_GET['token'];
		$usuario = $_GET['usuario'];

		$currentDate = new MongoDate();

		//validamos el token recibido.
		if($token == md5($PASSPHRASE)){

			// Nos conectamos a la base
			$m = new Mongo('mongodb://'.$DATABASE_HOSTNAME.':'.$DATABASE_PORT);

			// Seleccionamos una base de datos
			$db = $m->$DATABASE_NAME;

			// Seleccionamos una coleccion
			$clientes = $db->clientes;

			
			$posts = array();

			$clientes -> update(  
				array("usuario" => $usuario),
				array('$set' => array(
					"fechaInicioPlan" => $currentDate
					)
				)
				);
			$m ->close();
			sleep(1);

			$success = array('response' => $posts);
			/* output in necessary format */
				header('content-type: application/json; charset=utf-8');
				echo $_GET['callback']."(";
				echo ")";

		}else{
			echo "Invalid Token";
		}

	}else{
		header('status: 400 Bad Request', true, 400);
		echo "Bad Request";
	}
?>