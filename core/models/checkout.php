<?php
	class Checkoutmodel {
		public function __construct() {
	        require_once '../dbConnection.php';
	    }

		public function createOrder($data){
			$pdo = new Conexion();
			$cmd = '
				INSERT INTO tienda.order
						(customer_id, amount, ship_price, shipping_address, order_date, payment_data, coupon, status)
				VALUES
					(:customer_id, :amount, :ship_price, :shipping_address, now(), :payment_data, :coupon, 1)
			';

			$parametros = array(
				':customer_id' 		=> $data['customer_id'],
				':amount' 			=> $data['amount'],
				':ship_price' 		=> $data['ship_price'],
				':shipping_address'	=> $data['shipping_address'],
				':payment_data'		=> $data['payment_data'],
				':coupon'			=> $data['coupon']
			);

			$order_id = 0;

			try{
				$sql = $pdo->prepare($cmd);
				$sql->execute($parametros);
				$order_id = $pdo->lastInsertId();
			} catch (PDOException $e) {
		        return $e->getMessage();
		    }

		    $cmd = '
				INSERT INTO order_log
						(order_id, status, comments, update_date)
				VALUES
					(:order_id, :status, :comments, now())
			';

			$parametros = array(
				':order_id' 		=> $order_id,
				':status' 			=> 1,
				':comments' 		=> 'a new purchase order is registered'
			);

			$sql = $pdo->prepare($cmd);
			$sql->execute($parametros);

			return $order_id;
	    }

	    public function createOrderDetail($data, $orderId){
			$pdo = new Conexion();

			foreach ($data as $key => $value) {
				$cmd = '
					INSERT INTO order_detail
							(order_id, product_id, price, quantity, amount, selected_options)
					VALUES
						(:order_id, :product_id, :price, :quantity, :amount, :selected_options)
				';

				$parametros = array(
					':order_id' 	=> $orderId,
					':product_id'	=> $value['product_id'],
					':price' 		=> $value['price'],
					':quantity'		=> $value['quantity'],
					':amount'		=> $value['amount'],
					':selected_options' => $value['config']
				);

				$sql = $pdo->prepare($cmd);
				$sql->execute($parametros);
			}

			return TRUE;
	    }

	    public function getOrder($currentOrderId){
	    	$pdo = new Conexion();

	    	$cmd = '
	    		SELECT id, customer_id, amount, ship_price, shipping_address, order_date, payment_data, status FROM tienda.order WHERE id =:order_id
	    	';

	    	$parametros = array(
	    		':order_id' => $currentOrderId
	    	);

	    	$sql = $pdo->prepare($cmd);
			$sql->execute($parametros);
			$sql->setFetchMode(PDO::FETCH_OBJ);

			$data['order'] = $sql->fetch();

			$cmd = '
	    		SELECT 
					p.name, ot.price, ot.quantity, ot.amount, p.thumbnail, ot.selected_options 
				FROM tienda.order_detail AS ot 
				INNER JOIN product AS p ON ot.product_id = p.id
				WHERE ot.order_id =:order_id
	    	';

	    	$sql = $pdo->prepare($cmd);
			$sql->execute($parametros);
			$data['detail'] = $sql->fetchAll(PDO::FETCH_ASSOC);

			return $data;
	    }

	    public function GetCoupon($code) {
			$pdo = new Conexion();
			$cmd = 'SELECT valor, tipo FROM coupon WHERE codigo =:code AND status = 1';

			$parametros = array(
				':code' => $code
			);

			$sql = $pdo->prepare($cmd);
			$sql->execute($parametros);
			$sql->setFetchMode(PDO::FETCH_OBJ);

			return $sql->fetch();
		}

		public function GetOrders(){
	    	$pdo = new Conexion();

	    	$cmd = '
	    		SELECT id, customer_id, amount, ship_price, shipping_address, order_date, payment_data, status, coupon, ship_date, shipper_tracking FROM tienda.order
	    	';

	    	$sql = $pdo->prepare($cmd);
			$sql->execute();
			$data['order'] = $sql->fetchAll(PDO::FETCH_ASSOC);

			return $data;
	    }

	    public function getDetailOrder($orderId){
	    	$pdo = new Conexion();

	    	$cmd = '
	    		SELECT 
					p.name, ot.price, ot.quantity, ot.amount, p.thumbnail, ot.selected_options 
				FROM tienda.order_detail AS ot 
				INNER JOIN product AS p ON ot.product_id = p.id
				WHERE ot.order_id =:order_id
	    	';

	    	$parametros = array(
	    		':order_id' => $orderId
	    	);

	    	$sql = $pdo->prepare($cmd);
			$sql->execute($parametros);
			return $sql->fetchAll(PDO::FETCH_ASSOC);
	    }

	    public function cancelOrder($orderId){
	    	$pdo = new Conexion();

	    	$cmd = '
	    		UPDATE tienda.order SET status = 0 WHERE id =:orderId
	    	';

	    	$parametros = array(
	    		':orderId' => $orderId
	    	);

	    	$sql = $pdo->prepare($cmd);
			$sql->execute($parametros);
			return TRUE;
	    }

	    public function setTracking($orderId, $tracking){
	    	$pdo = new Conexion();

	    	$cmd = '
	    		UPDATE tienda.order SET status = 2, ship_date = now(), shipper_tracking =:tracking   WHERE id =:orderId
	    	';

	    	$parametros = array(
	    		':orderId' => $orderId,
	    		':tracking' => $tracking
	    	);

	    	$sql = $pdo->prepare($cmd);
			$sql->execute($parametros);
			return TRUE;
	    }

	    public function finalizeOrder($orderId){
	    	$pdo = new Conexion();

	    	$cmd = '
	    		UPDATE tienda.order SET status = 3 WHERE id =:orderId
	    	';

	    	$parametros = array(
	    		':orderId' => $orderId
	    	);

	    	$sql = $pdo->prepare($cmd);
			$sql->execute($parametros);
			return TRUE;
	    }
	}