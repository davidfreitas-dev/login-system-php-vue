<?php 

namespace App;

class Response {

	public static function handleResponse($status, $response)
	{

		return json_encode(
			array(
				"status" => $status,
				"data" => $response
			)
		);

	}
    
}
