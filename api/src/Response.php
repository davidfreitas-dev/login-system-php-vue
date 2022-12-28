<?php 

namespace App;

class Response {

	public static function handleResponse($code, $status, $response)
	{

		return json_encode(
			array(
        "code" => $code,
				"status" => $status,
				"data" => $response
			)
		);

	}
    
}
