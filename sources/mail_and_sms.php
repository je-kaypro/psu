<?php
    ini_set("SMTP","ssl://smtp.gmail.com");
    function send_email($email,$msg,$subject){
        $headers = "From: surveyors@gmail.com";
        if(mail($email, $subject, $msg, $headers)){
           return true;
        }
    }
    
    function SendSMS ($host, $port, $username, $password, $phoneNoRecip, $msgText) {

        /* Parameters:
        $host - IP address or host name of the NowSMS server
        $port - "Port number for the web interface" of the NowSMS Server
        $username - "SMS Users" account on the NowSMS server
        $password - Password defined for the "SMS Users" account on the NowSMS Server
        $phoneNoRecip - One or more phone numbers (comma delimited) to receive the text
        message
        $msgText - Text of the message
        */
	$fp = fsockopen($host, $port, $errno, $errstr);
	if (!$fp) {
		echo "errno: $errno \n";
		echo "errstr: $errstr\n";
		return $result;
	}
	fwrite($fp, "GET /?Phone=" . rawurlencode($phoneNoRecip) . "&Text=" .
	rawurlencode($msgText) . " HTTP/1.0\n");
	if ($username != "") {
            $auth = $username . ":" . $password;
            $auth = base64_encode($auth);
            fwrite($fp, "Authorization: Basic " . $auth . "\n");
	}
	fwrite($fp, "\n");
	$res = "";
	while(!feof($fp)) {
            $res .= fread($fp,1);
	}
	fclose($fp);
	return $res;
    }

?>