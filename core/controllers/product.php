<?php
	session_start();
	
	require_once '../models/product.php';
	require_once '../utils/jwt.php';

	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: POST");

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$vars 			= ($_POST) ? $_POST : json_decode(file_get_contents("php://input"), TRUE);
		$productModel 	= new Productmodel();

		if($vars['_method'] == 'POST'){
			$prodData = array(
				'inputName' 			=> $vars['inputName'],
				'inputNameSp' 			=> $vars['inputNameSp'],
				'inputDescription'	=> $vars['inputDescription'],
				'inputDescriptionSp'	=> $vars['inputDescriptionSp'],
				'inputPrice'			=> floatval($vars['inputPrice']),
				'inputSalePrice'		=> floatval($vars['inputSalePrice']),
				'dimensions'			=> $vars['pConfig']
			);

			if($vars['productId'] == 0){
				$tmpResponse = $productModel->register($prodData);
			} else {
				$productId 					= $vars['productId'];
				$prodData['productId'] 	= $productId;				
				$tmpResponse 				= $productModel->updates($prodData);

				$deletesImages = json_decode($vars['deletesImages'], TRUE);
				foreach ($deletesImages as $key => $value) {
					$oldImg = dirname(__FILE__, 3) . "/assets/img/product/{$productId}/{$value}";
					@unlink( $oldImg );
				}
			}

			if($tmpResponse[0]){
				$productId 	= $tmpResponse[1];
				$folder 		= "assets/img/product/{$productId}";

				if (!empty($_FILES['imagesproduct'])){
					mkdir(dirname(__FILE__, 3) . "/{$folder}", 0777, true);

					$imagesprod = $_FILES['imagesproduct'];
					foreach($imagesprod['name'] as $key => $imagesproduct) {
							$_FILES['imagesproduct[]']['name']		= $imagesprod['name'][$key];
			            $_FILES['imagesproduct[]']['type']		= $imagesprod['type'][$key];
			            $_FILES['imagesproduct[]']['tmp_name']	= $imagesprod['tmp_name'][$key];
			            $_FILES['imagesproduct[]']['error']		= $imagesprod['error'][$key];
			            $_FILES['imagesproduct[]']['size']		= $imagesprod['size'][$key];

							$filename = $imagesprod['name'][$key];
							$tempname = $imagesprod['tmp_name'][$key];

							move_uploaded_file($tempname, "../../{$folder}/{$filename}");
					}
				}

				if( is_dir( dirname(__FILE__, 3) . "/{$folder}" ) ) {
					$images = getProdutsPhotos(dirname(__FILE__, 3) . "/{$folder}", $productId);

					$thumbnail = NULL;
					if( count($images) > 0 )
						$thumbnail = $images[0];

					$productModel->updateThumbnails($productId, $thumbnail, json_encode($images, JSON_FORCE_OBJECT));
				}

				$productModel->insertCategory($productId, $vars['inputCategory']);

				$response = array(
					'codeResponse' => 200
				);
			}else{
				$response = array(
					'codeResponse' => 0
				);
			}

			header('HTTP/1.1 200 Ok');
			header("Content-Type: application/json; charset=UTF-8");			
			exit(json_encode($response));
		} else if($vars['_method'] == 'GET'){
			$productList 	= $productModel->getProduct($vars['limite']);

			if($productList){
				foreach ($productList as $key => $value) {
					$productList[$key]['categoria'] = $productModel->getCategories($value['id']);
				}
			}

			$response = array(
				'codeResponse'	=> 200,
				'data' 			=> $productList,
				'message' 		=> 'Ok'
			);

			header('HTTP/1.1 200 Ok');
			header("Content-Type: application/json; charset=UTF-8");			
			exit(json_encode($response));
		} else if($vars['_method'] == 'Delete'){
			$productModel->deleteProduct($vars['productId']);

			header('HTTP/1.1 200 Ok');		
			exit();
		} else if($vars['_method'] == 'getProductId'){
			$productData 	= $productModel->getProductId($vars['productId']);

			if($productData){
				foreach ($productData as $key => $value) {
					$productData[$key]['categoria'] = $productModel->getCategories($value['id']);
				}
			}

			$response = array(
				'codeResponse'	=> 200,
				'data' 			=> $productData,
				'message' 		=> 'Ok'
			);

			header('HTTP/1.1 200 Ok');
			header("Content-Type: application/json; charset=UTF-8");			
			exit(json_encode($response));
		} else if($vars['_method'] == 'getProductCat'){
			$productList 	= $productModel->getProductCat($vars['categoryId']);

			if($productList){
				foreach ($productList as $key => $value) {
					$productList[$key]['categoria'] = $productModel->getCategories($value['id']);
				}
			}

			$response = array(
				'codeResponse' => 200,
				'data' 			=> $productList,
				'message' 		=> 'Ok'
			);

			header('HTTP/1.1 200 Ok');
			header("Content-Type: application/json; charset=UTF-8");			
			exit(json_encode($response));
		} else if($vars['_method'] == 'search'){
			$productList 	= $productModel->search($vars['strQuery']);

			if($productList){
				foreach ($productList as $key => $value) {
					$productList[$key]['categoria'] = $productModel->getCategories($value['id']);
				}
			}

			$response = array(
				'codeResponse' => 200,
				'data' 			=> $productList,
				'message' 		=> 'Ok'
			);

			header('HTTP/1.1 200 Ok');
			header("Content-Type: application/json; charset=UTF-8");			
			exit(json_encode($response));
		}
	}

	function getProdutsPhotos($dir, $producId) {
		$result = array();
		$cdir   = scandir($dir);

		foreach ($cdir as $key => $value){
			if (!in_array($value,array(".",".."))){
				if (is_dir($dir . DIRECTORY_SEPARATOR . $value)){
					if (!is_dir_empty( $dir . DIRECTORY_SEPARATOR . $value ))
						$result[$value] = getProdutsPhotos($dir . DIRECTORY_SEPARATOR . $value, $producId);
				} else {
					$result[] = "assets/img/product/{$producId}/{$value}";
				}
			}
		}

		return $result;
   }

    function is_dir_empty($dir) {
      if (!is_readable($dir)) return NULL;
      return (count(scandir($dir)) == 2);
    }

	header('HTTP/1.1 400 Bad Request');
	header("Content-Type: application/json; charset=UTF-8");

	$response = array(
		'codeResponse' => 400,
		'message' => 'Bad Request'
	);
	exit( json_encode($response) );