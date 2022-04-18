<?php

declare(strict_types=1);

namespace App\Util;

class CurlUtil
{
	public static function post()
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'localhost:8888/api/login/oauth2.0/token?client_id=60f9bd80d01913d3c74e&client_secret=6ec3749d9bc70dbacaa58ed378243bb01c655ed3&grant_type=authorization_code&code=1ae22decc49aa76b97e7&redirect_uri=localhost:3100/callback',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return $response;
	}
}
