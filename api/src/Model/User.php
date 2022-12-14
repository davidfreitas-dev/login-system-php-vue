<?php 

namespace App\Model;

use App\DB\Database;
use App\Mail\Mailer;
use App\Response;
use PDO;

class User {

	public static function get($iduser)
	{

		$sql = "SELECT * FROM tb_users WHERE iduser = :iduser";

		try {

			$db = new Database();

			$results = $db->select($sql, array(
				":iduser"=>$iduser
			));

			$data = $results[0];
			
			return Response::handleResponse(200, "success", $data);

		} catch (PDOException $e) {
			
			return Response::handleResponse(500, "error", "Falha ao obter usuário: " . $e->getMessage());
			
		}

	}

	public static function all() 
	{
		
		$sql = "SELECT * FROM tb_users";		
		
		try {

			$db = new Database();

			$result = $db->select($sql);
			
			if (count($result) > 0) {

				return Response::handleResponse(200, "success", $result);

			} else {

				return Response::handleResponse(404, "success", "Nenhum usuário encontrado!");

			}

		} catch (PDOException $e) {

			return Response::handleResponse(500, "error", "Falha ao obter usuários: " . $e->getMessage());
			
		}		

	}

	public static function add($user) 
	{

		$userExists = User::checkUserExists($user['desemail']);
			
		if ($userExists) {

			return Response::handleResponse(200, "error", "Usuário já cadastrado!");

		}		

		$sql = "INSERT INTO tb_users (desperson, desemail, despassword, inadmin) VALUES (:desperson, :desemail, :despassword, :inadmin)";
		
		try {

			$db = new Database();

			$result = $db->query($sql, array(
				":desperson"=>$user['desperson'],
				":desemail"=>$user['desemail'],
				":despassword"=>User::getPasswordHash($user['despassword']),
				":inadmin"=>$user['inadmin']
			));

      $userExists = User::checkUserExists($user['desemail']);

			if ($result && $userExists) {

				return Response::handleResponse(201, "success", "Cadastro efetuado com sucesso!");

			} else {

        return Response::handleResponse(500, "error", "Não foi possível efetuar o cadastro.");

      }

		} catch (PDOException $e) {
			
			return Response::handleResponse(400, "error", "Falha ao cadastrar usuário: " . $e->getMessage());
			
		}		

	}

	public static function update($id, $user) 
	{
		
		$sql = "UPDATE tb_users SET desperson = :desperson, desemail = :desemail, despassword = :despassword, inadmin = :inadmin WHERE iduser = :iduser";
		
		try {

			$db = new Database();
			
			$result = $db->query($sql, array(
				":iduser"=>$id,
				":desperson"=>$user['desperson'],
				":desemail"=>$user['desemail'],
				":despassword"=>User::getPasswordHash($user['despassword']),
				":inadmin"=>$user['inadmin']
			));

			if ($result) {

				return Response::handleResponse(200, "success", "Usuário atualizado com sucesso!");
				
			} else {

        return Response::handleResponse(500, "error", "Não foi possível atualizar o usuário.");

      }

		} catch (PDOException $e) {

			return Response::handleResponse("error", "Falha ao editar usuário: " . $e->getMessage());
			
		}		

	}

	public static function delete($id) 
	{
		
		$sql = "DELETE FROM tb_users WHERE iduser = :iduser";		
		
		try {

			$db = new Database();
			
			$result = $db->query($sql, array(
				":iduser"=>$id
			));
			
			if ($result) {

				return Response::handleResponse("success", "Usuário excluido com sucesso!");

			} else {

        return Response::handleResponse(500, "error", "Não foi possível excluir o usuário.");

      }

		} catch (PDOException $e) {

			return Response::handleResponse("error", "Falha ao excluir usuário: " . $e->getMessage());
			
		}		

	}

	public static function checkUserExists($email) 
	{
		
		$sql = "SELECT * FROM tb_users WHERE desemail = :email";		
		
		try {

			$db = new Database();
			
			$result = $db->select($sql, array(
				":email"=>$email
			));

			return is_array($result) && count($result) > 0;

		} catch (PDOException $e) {

			return Response::handleResponse(400, "error", "Falha ao obter usuário: " . $e->getMessage());

		}		

	}

	public static function getPasswordHash($password)
	{

		return password_hash($password, PASSWORD_BCRYPT, [
			'cost' => 12
		]);

	}

	public static function getForgot($email)
	{

		$sql = "SELECT * FROM tb_persons a INNER JOIN tb_users b USING(idperson) WHERE a.desemail = :email";

		try {
			
			$db = new Database();

			$results = $db->select($sql, array(
				":email"=>$email
			));

			if (count($results) === 0) {

				return Response::handleResponse("error", "E-mail não encontrado.");

			} else {

				$data = $results[0];

				$query = $db->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
					":iduser"=>$data['iduser'],
					":desip"=>$_SERVER['REMOTE_ADDR']
				)); 

				if (count($query) === 0)	{

					return Response::handleResponse("error", "Não foi possível recuperar a senha.");

				} else {

					$dataRecovery = $query[0];

					$code = openssl_encrypt($dataRecovery['idrecovery'], 'AES-128-CBC', pack("a16", $_ENV['SECRET']), 0, pack("a16", $_ENV['SECRET_IV']));

					$code = base64_encode($code);

					$link = "http://127.0.0.1:3000/forgot/reset?code=$code";

					$mailer = new Mailer($data['desemail'], $data['desperson'], "Redefinir senha de usuário do sistema", array(
						"name"=>$data['desperson'],
						"link"=>$link
					));				

					$mailer->send();

					return Response::handleResponse("success", "E-mail de recuperação enviado com sucesso!");

				}

			}

		} catch (PDOException $e) {
			
			return Response::handleResponse("error", "Falha ao recuperar senha: " . $e->getMessage());

		}		

	}

	public static function validForgotDecrypt($code)
	{

		$code = base64_decode($code);

		$idrecovery = openssl_decrypt($code, 'AES-128-CBC', pack("a16", $_ENV['SECRET']), 0, pack("a16", $_ENV['SECRET_IV']));

		$sql = "SELECT * FROM tb_userspasswordsrecoveries a
				INNER JOIN tb_users b USING(iduser)
				INNER JOIN tb_persons c USING(idperson)
				WHERE a.idrecovery = :idrecovery
				AND a.dtrecovery IS NULL
				AND DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW()";
		
		try {
			
			$db = new Database();

			$results = $db->select($sql, array(
				":idrecovery"=>$idrecovery
			));

			if (count($results) === 0) {

				return Response::handleResponse("error", "Não foi possível recuperar a senha.");

			} else {

				return $results[0];

			}

		} catch (PDOException $e) {
			
			return Response::handleResponse("error", "Falha ao validar token: " . $e->getMessage());

		}

	}

	public static function setForgotUsed($idrecovery)
	{

		$sql = "UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery";

		try {

			$db = new Database();

			$db->query($sql, array(
				":idrecovery"=>$idrecovery
			));

		} catch (PDOException $e) {

			return Response::handleResponse("error", "Falha ao gravar senha antiga: " . $e->getMessage());

		}

	}

	public static function setPassword($password, $iduser)
	{

		$sql = "UPDATE tb_users SET despassword = :password WHERE iduser = :iduser";

		try {

			$db = new Database();

			$db->query($sql, array(
				":password"=>$password,
				":iduser"=>$iduser
			));

			return Response::handleResponse("success", "Senha alterada com sucesso");

		} catch (PDOException $e) {

			return Response::handleResponse("error", "Falha ao gravar nova senha: " . $e->getMessage());

		}

	}

}

 ?>