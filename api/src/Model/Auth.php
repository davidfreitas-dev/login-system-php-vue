<?php 

namespace App\Model;

use App\DB\Database;
use App\Response;
use PDO;

class Auth {
        
    public static function login($login, $password)
    {

        $sql = "SELECT * FROM tb_users a INNER JOIN tb_persons b ON a.idperson = b.idperson WHERE a.deslogin = :LOGIN OR b.desemail = :LOGIN";

		$db = new Database();

		$result = $db->select($sql, array(
			":LOGIN"=>$login
		)); 

		if (count($result) === 0) {

			return Response::handleResponse("error", "Usuário inexistente ou senha inválida.");

		}

		$data = $result[0];

		if (password_verify($password, $data['despassword'])) {

			return Auth::generateToken($data);

		} else {

			return Response::handleResponse("error", "Usuário inexistente ou senha inválida.");

		}

    }

    public static function generateToken($data)
    {

        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        $payload = [
            'name' => $data['desperson'],
            'email' => $data['desemail'],
        ];


        $header = json_encode($header);
        $payload = json_encode($payload);


        $header = self::base64UrlEncode($header);
        $payload = self::base64UrlEncode($payload);


        $sign = hash_hmac('sha256', $header . "." . $payload, $_ENV['JWT_SECRET_KEY'], true);
        $sign = self::base64UrlEncode($sign);


        $token = $header . '.' . $payload . '.' . $sign;

        $response = array(
            "user" => $data,
            "token" => $token
        );

        return Response::handleResponse("success", $response);

    }
    
    private static function base64UrlEncode($data)
    {

        $b64 = base64_encode($data);

        if ($b64 === false) {
            return false;
        }

        $url = strtr($b64, '+/', '-_');

        return rtrim($url, '=');
        
    }

}