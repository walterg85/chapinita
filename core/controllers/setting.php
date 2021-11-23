<?php
	session_start();
	
	require_once '../models/setting.php';
	require_once '../utils/jwt.php';

	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: POST");

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$vars 			= ($_POST) ? $_POST : json_decode(file_get_contents("php://input"), TRUE);
		$settingsModel 	= new Settingsmodel();

		if($vars['_method'] == 'Get'){
			$response = array(
				'codeResponse' 	=> 200,
				'data' 			=> $settingsModel->get()
			);

			header('HTTP/1.1 200 Ok');
			header("Content-Type: application/json; charset=UTF-8");			
			exit(json_encode($response));
		} else if($vars['_method'] == 'updateData'){
			$newPassword = ($vars['password'] == '') ? '' : encryptPass($vars['password']);

			$usData = array(
				'owner' 	=> $vars['owner'],
				'email' 	=> $vars['email'],
				'password' 	=> $newPassword
			);

			$setData = array(
				'shipingCost' 	=> $vars['shipingCost'],
				'shipingFree' 	=> $vars['shipingFree'],
				'tax' 			=> $vars['tax'],
				'paypalid'		=> $vars['paypalid']
			);

			$tmpResponse = $settingsModel->updateData($usData, $setData);

			if($tmpResponse){
				$response = array(
					'codeResponse' => 200,
					'message' => 'Updated information.'
				);

				$_SESSION['authData']->isDefault = (password_verify('12345', $tmpResponse->password)) ? 1 : 0;
				$_SESSION['authData']->email = $vars['email'];
			}else{
				$response = array(
					'codeResponse' 	=> 0,
					'message' 		=> 'Outdated information'
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
		'codeResponse'	=> 400,
		'message' 		=> 'Bad Request'
	);
	exit( json_encode($response) );