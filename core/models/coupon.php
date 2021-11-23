<?php
	class Couponmodel {
		public function __construct() {
	        require_once '../dbConnection.php';
	    }

		public function get() {
			$pdo = new Conexion();
			$cmd = 'SELECT id, codigo, valor, tipo FROM coupon WHERE status = 1;';

			$sql = $pdo->prepare($cmd);
			$sql->execute();

			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}

		public function register($data){
	    	$pdo = new Conexion();
	    	$cmd = '
	    		INSERT INTO coupon (codigo, valor, tipo, status) VALUES (:codigo, :valor, :tipo, 1)
	    	';

	    	$parametros = array(
	    		':codigo' => $data['code'],
	    		':valor' => $data['value'],
	    		':tipo' => $data['tipo']
	    	);

	    	$sql = $pdo->prepare($cmd);
			$sql->execute($parametros);

			return TRUE;
	    }

	    public function delete($couponId){
	    	$pdo = new Conexion();
	    	$cmd = '
	    		UPDATE coupon SET status = 0 WHERE id =:couponId
	    	';

	    	$parametros = array(
	    		':couponId' => $couponId
	    	);

	    	$sql = $pdo->prepare($cmd);
			$sql->execute($parametros);

			return TRUE;
	    }
	}