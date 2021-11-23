<?php
	class Settingsmodel {
		public function __construct() {
			// Se agrega esta validacion para controlar error de directorios
			if(is_file('../dbConnection.php')){
				require_once '../dbConnection.php';
			}else{
				require_once '../core/dbConnection.php';
			}
	    }

		public function get() {
			$pdo = new Conexion();
			$cmd = 'SELECT parameter, value FROM setting';

			$sql = $pdo->prepare($cmd);
			$sql->execute();

			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}

		public function updateData($usData, $setData){
	    	$pdo = new Conexion();

	    	$updatePass = '';
	    	if($usData['password'] != ''){
	    		$updatePass = ', password ="' . $usData['password'] .'"';
	    	}

	    	$cmd = '
	    		UPDATE user SET email =:email'. $updatePass .' WHERE owner =:owner
	    	';

	    	$parametros = array(
	    		':email' => $usData['email'],
	    		':owner' => $usData['owner']
	    	);

	    	$sql = $pdo->prepare($cmd);
			$sql->execute($parametros);

	    	$cmd = '
	    		DELETE FROM setting WHERE parameter in("shipingCost", "shipingFree", "tax", "paypalid");
	    	';

	    	$sql = $pdo->prepare($cmd);
			$sql->execute();

			$cmd = '
				INSERT INTO setting (parameter, value) 
				VALUES ("shipingCost", :shipingCost), ("shipingFree", :shipingFree), ("tax", :tax), ("paypalid", :paypalid);
	    	';

			$parametros = array(
				':shipingCost' => $setData['shipingCost'],
				':shipingFree' => $setData['shipingFree'],
				':tax' => $setData['tax'],
				':paypalid' => $setData['paypalid']
			);

	    	$sql = $pdo->prepare($cmd);
			$sql->execute($parametros);

			$cmd = '
	    		SELECT password FROM user WHERE owner =:owner
	    	';

	    	$parametros = array(
	    		':owner' => $usData['owner']
	    	);

	    	$sql = $pdo->prepare($cmd);
			$sql->execute($parametros);
			$sql->setFetchMode(PDO::FETCH_OBJ);

			return $sql->fetch();
	    }
	}