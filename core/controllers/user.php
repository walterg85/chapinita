<?php
	session_start();
	
	require_once '../models/user.php';
	require_once '../utils/jwt.php';

	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: POST");

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$vars = ($_POST) ? $_POST : json_decode(file_get_contents("php://input"), TRUE);
		$userModel = new Usersmodel();

		if($vars['_method'] == 'VALIDATE'){
			$tmpResponse = $userModel->login($vars['uname']);

			$response = array(
				'codeResponse' => 0,
				'message' => 'Username or password incorrect'
			);

			if($tmpResponse){
				if (password_verify($vars['password'], $tmpResponse->password)){
					unset($tmpResponse->password);

					$headers = array('alg' => 'HS256', 'typ' => 'JWT');
					$payload = array('username' => $vars['uname'], 'exp' => (time() + 86400));
					$tmpResponse->token = generate_jwt($headers, $payload);

					$response = array(
						'codeResponse' 	=> 200,
						'data' 			=> $tmpResponse,
						'message' 		=> 'Welcome'
					);

					$_SESSION['isLoged'] 				= TRUE;
					$_SESSION['authData'] 				= $tmpResponse;
					$_SESSION['authData']->isDefault 	= 0;

					if($vars['password'] == '12345')
						$_SESSION['authData']->isDefault = 1;
				}
			}

			header('HTTP/1.1 200 Ok');
			header("Content-Type: application/json; charset=UTF-8");			
			exit(json_encode($response));
		} else if($vars['_method'] == 'initDefault'){
			$usData = array(
				'owner' 	=> 'admin',
				'email' 	=> '',
				'password' 	=> encryptPass('12345'),
				'type' 		=> 1
			);

			$setData = array(
				'shipingCost' 	=> '4.99',
				'shipingFree' 	=> '75.00',
				'tax' 			=> '8.0',
				'paypalid'		=> 'ATTvB3Pjt7K6c0Pm7km72twwH3GI-3FnaqZvgwWbqfRU-RmndDwSuRXN21dFmc0-hpDxQC4P3MP_wC2H'
			);

			$tmpResponse = $userModel->initDefault($usData, $setData);

			if($tmpResponse){
				$response = array(
					'codeResponse' 	=> 200,
					'message' 		=> 'Application started.'
				);
			}else{
				$response = array(
					'codeResponse' 	=> 0,
					'message' 		=> 'Application not started'
				);
			}

			header('HTTP/1.1 200 Ok');
			header("Content-Type: application/json; charset=UTF-8");			
			exit(json_encode($response));
		}
	}

	function encryptPass($strPassword) {
		$options = [
		    'cost' => 12
		];

		return password_hash($strPassword, PASSWORD_BCRYPT, $options);
	}

	header('HTTP/1.1 400 Bad Request');
	header("Content-Type: application/json; charset=UTF-8");

	$response = array(
		'codeResponse' => 400,
		'message' => 'Bad Request'
	);
	exit( json_encode($response) );