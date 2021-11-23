<?php
	session_start();
	
	require_once '../models/category.php';
	require_once '../utils/jwt.php';

	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: POST");

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$vars 			= ($_POST) ? $_POST : json_decode(file_get_contents("php://input"), TRUE);
		$categoryModel	= new Categorymodel();

		if($vars['_method'] == 'Get'){
			$response = array(
				'codeResponse' => 200,
				'data' => $categoryModel->get()
			);

			header('HTTP/1.1 200 Ok');
			header("Content-Type: application/json; charset=UTF-8");			
			exit(json_encode($response));
		} else if($vars['_method'] == 'POST'){
			$data = array(
				'inputName'		=> $vars['inputName'],
				'inputNameSp'	=> $vars['inputNameSp'],
				'chkVisible'	=> $vars['chkVisible'],
				'categoryId'	=> $vars['categoryId']
			);

			$categoryId = $categoryModel->register($data);
			$response 	= array(
				'codeResponse' => 0
			);

			if($categoryId){
				$folder = 'assets/img/category';
				if( !is_dir(dirname(__FILE__, 3) . "/{$folder}") )
					mkdir(dirname(__FILE__, 3) . "/{$folder}", 0777, true);

				if (!empty($_FILES['imageCat'])){
					$filename = $_FILES['imageCat']['name'];
					$tempname = $_FILES['imageCat']['tmp_name'];
					       
					if (move_uploaded_file($tempname, "../../{$folder}/{$filename}"))
						$categoryModel->updateThumbnail($categoryId, "{$folder}/{$filename}");
				}

				$response = array(
					'codeResponse' => 200
				);
			}

			header('HTTP/1.1 200 Ok');
			header("Content-Type: application/json; charset=UTF-8");			
			exit(json_encode($response));
		} else if($vars['_method'] == 'Delete'){
			$categoryModel->delete($vars['categoryId']);

			$response = array(
				'codeResponse' => 200
			);

			header('HTTP/1.1 200 Ok');
			header("Content-Type: application/json; charset=UTF-8");			
			exit(json_encode($response));
		} else if($vars['_method'] == 'unVisivility'){
			$categoryModel->unVisivility($vars['categoryId'], $vars['visible']);

			$response = array(
				'codeResponse' => 200
			);

			header('HTTP/1.1 200 Ok');
			header("Content-Type: application/json; charset=UTF-8");			
			exit(json_encode($response));
		}
	}

	header('HTTP/1.1 400 Bad Request');
	header("Content-Type: application/json; charset=UTF-8");

	$response = array(
		'codeResponse'	=> 400,
		'message' 		=> 'Bad Request'
	);
	exit( json_encode($response) );