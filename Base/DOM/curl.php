<?php
function httpGet($url)
{
    $ch = curl_init();  

    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//  curl_setopt($ch,CURLOPT_HEADER, false); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

	$header = [
		"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36",
	];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    $output=curl_exec($ch);

	if (curl_errno($ch)) {
        return curl_error($ch);
	} else {
		$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if (200 !== $httpStatusCode) {
            return $output;
		}
	}

    curl_close($ch);
    return $output;
}