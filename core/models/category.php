<?php
	class Categorymodel {
		public function __construct() {
	        require_once '../dbConnection.php';
	    }

		public function get() {
			$pdo = new Conexion();
			$cmd = 'SELECT id, UPPER(name) AS name, thumbnail, parent, UPPER(description) AS nameSp FROM category WHERE active = 1 order by name ASC, description ASC;';

			$sql = $pdo->prepare($cmd);
			$sql->execute();

			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}

		public function register($data){
	    	$pdo = new Conexion();

	    	if($data['categoryId'] == 0){
	    		$cmd = '
		    		INSERT INTO category (name, parent, description, active) VALUES (:name, :parent, :description, 1)
		    	';

		    	$parametros = array(
		    		':name' => $data['inputName'],
		    		':parent' => $data['chkVisible'],
		    		':description' => $data['inputNameSp']
		    	);

		    	$sql = $pdo->prepare($cmd);
				$sql->execute($parametros);

				return $pdo->lastInsertId();
	    	}else{
	    		$cmd = '
		    		UPDATE category SET name =:name, parent =:parent, description =:description WHERE id =:categoryId
		    	';

		    	$parametros = array(
		    		':name' => $data['inputName'],
		    		':parent' => $data['chkVisible'],
		    		':description' => $data['inputNameSp'],
		    		':categoryId' => $data['categoryId']
		    	);

		    	$sql = $pdo->prepare($cmd);
				$sql->execute($parametros);

				return $data['categoryId'];
	    	}
	    	
	    }

	    public function delete($categoryId){
	    	$pdo = new Conexion();
	    	$cmd = '
	    		UPDATE category SET active = 0 WHERE id =:categoryId
	    	';

	    	$parametros = array(
	    		':categoryId' => $categoryId
	    	);

	    	$sql = $pdo->prepare($cmd);
			$sql->execute($parametros);

			return TRUE;
	    }

	    public function updateThumbnail($categoryId, $thumbnail){
	    	$pdo = new Conexion();
			$cmd = 'UPDATE category SET thumbnail =:thumbnail WHERE id =:categoryId';

			$parametros = array(
				':thumbnail' => $thumbnail,
				':categoryId' => $categoryId			
			);

			$sql = $pdo->prepare($cmd);
			$sql->execute($parametros);

			return TRUE;
	    }

	    public function unVisivility($categoryId, $visible){
	    	$pdo = new Conexion();
	    	$cmd = '
	    		UPDATE category SET parent =:visible WHERE id =:categoryId
	    	';

	    	$parametros = array(
	    		':categoryId' => $categoryId,
	    		':visible' => $visible
	    	);

	    	$sql = $pdo->prepare($cmd);
			$sql->execute($parametros);

			return TRUE;
	    }
	}