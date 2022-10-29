<?php 

namespace App\Model;

use App\DB\Database;
use App\Mail\Mailer;
use App\Response;
use PDO;

class User {

	public static function get($iduser)
	{

		$sql = "SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser";

		try {

			$db = new Database();

			$results = $db->select($sql, array(
				":iduser"=>$iduser
			));

			$data = $results[0];
			
			return Response::handleResponse("success", $data);

		} catch (PDOException $e) {
			
			return Response::handleResponse("error", "Falha ao obter usuário: " . $e->getMessage());
			
		}

	}

	public static function all() 
	{
		
		$sql = "SELECT * FROM tb_users a INNER JOIN tb_persons b ON a.idperson = b.idperson";		
		
		try {

			$db = new Database();

			$result = $db->select($sql);
			
			if (count($result) > 0) {

				return Response::handleResponse("success", $result);

			} else {

				return Response::handleResponse("success", "Nenhum usuário encontrado!");

			}

		} catch (PDOException $e) {

			return Response::handleResponse("error", "Falha ao obter usuários: " . $e->getMessage());
			
		}		

	}

	public static function add($user) 
	{
		
		$login = !empty($user['deslogin']) ? $user['deslogin'] : $user['desemail'];

		$response = User::checkUserExists($login);
			
		if ($response) {

			return Response::handleResponse("error", "Usuário já cadastrado!");

		}		

		$sql = "CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :nrcpf, :inadmin)";
		
		try {

			$db = new Database();

			$result = $db->select($sql, array(
				":desperson"=>$user['desperson'],
				":deslogin"=>$user['deslogin'],
				":despassword"=>User::getPasswordHash($user['despassword']),
				":desemail"=>$user['desemail'],
				":nrphone"=>$user['nrphone'],
				":nrcpf"=>$user['nrcpf'],
				":inadmin"=>$user['inadmin']
			));

			if ($result) {

				return Response::handleResponse("success", "Cadastro efetuado com sucesso!");

			}

		} catch (PDOException $e) {
			
			return Response::handleResponse("error", "Falha ao cadastrar usuário: " . $e->getMessage());
			
		}		

	}

	public static function update($id, $user) 
	{
		
		$sql = "CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :nrcpf, :inadmin)";
		
		try {

			$db = new Database();
			
			$result = $db->select($sql, array(
				":iduser"=>$id,
				":desperson"=>$user['desperson'],
				":deslogin"=>$user['deslogin'],
				":despassword"=>User::getPasswordHash($user['despassword']),
				":desemail"=>$user['desemail'],
				":nrphone"=>$user['nrphone'],
				":nrcpf"=>$user['nrcpf'],
				":inadmin"=>$user['inadmin']
			));

			if ($result) {

				return Response::handleResponse("success", "Usuário atualizado com sucesso!");
				
			}

		} catch (PDOException $e) {

			return Response::handleResponse("error", "Falha ao editar usuário: " . $e->getMessage());
			
		}		

	}

	public static function delete($id) 
	{
		
		$sql = "CALL sp_users_delete(:iduser)";		
		
		try {

			$db = new Database();
			
			$db->query($sql, array(
				":iduser"=>$id
			));
			
			if ($result) {

				return Response::handleResponse("success", "Usuário excluido com sucesso!");

			}

		} catch (PDOException $e) {

			return Response::handleResponse("error", "Falha ao excluir usuário: " . $e->getMessage());
			
		}		

	}

	public static function checkUserExists($login) 
	{
		
		$sql = "SELECT * FROM tb_users a INNER JOIN tb_persons b ON a.idperson = b.idperson WHERE a.deslogin = :LOGIN OR b.desemail = :LOGIN";		
		
		try {

			$db = new Database();
			
			$result = $db->select($sql, array(
				":LOGIN"=>$login
			));

			return is_array($result) && count($result) > 0;

		} catch (PDOException $e) {

			return Response::handleResponse("error", "Falha ao obter usuário: " . $e->getMessage());

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