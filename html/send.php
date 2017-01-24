<?php

//https://www.twilio.com/docs/api/rest/message

//includes an array $config with account details etc
$config_path = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';
include_once $config_path;

$token             = (getenv('TOKEN'))               ? getenv('TOKEN')               : $config['token'];
$defaultAccountSid = (getenv('DEFAULT_ACCOUNT_SID')) ? getenv('DEFAULT_ACCOUNT_SID') : $config['defaultAccountSid'];
$defaultAuthToken  = (getenv('DEFAULT_AUTH_TOKEN'))  ? getenv('DEFAULT_AUTH_TOKEN')  : $config['defaultAuthToken'];
$defaultFrom       = (getenv('DEFAULT_FROM'))        ? getenv('DEFAULT_FROM')        : $config['defaultFrom'];

//ensure hash is sane
if (strlen($token) < 1 || $token != $_REQUEST['token']) {
    $response = 'failure';
    goto end;
}

//per message
$from = $_REQUEST['from'];
$to = $_REQUEST['to'];
$body = $_REQUEST['text'];
$accountSid = $_REQUEST['accountSid'];
$authToken = $_REQUEST['authToken'];

if (empty($from)) {
    $from = $defaultFrom;
}

if (empty($accountSid)) {
    $accountSid = $defaultAccountSid;
}

if (empty($authToken)) {
    $authToken = $defaultAuthToken;
}

//not implemented
//$media_url = $_GET['udh'];//metadata?
//$status_callback = $_GET['dlr_url'];//dlr_url

//base url to POST to
$url = "https://api.twilio.com/2010-04-01/Accounts/${accountSid}/Messages.json";

//setup POST params
$params = array(
    'From' => $from,
    'To' => $to,
    'Body' => $body,
);

$c = curl_init($url);
curl_setopt($c, CURLOPT_POST, 1);
curl_setopt($c, CURLOPT_POSTFIELDS, $params);
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($c, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($c, CURLOPT_USERPWD, "${accountSid}:${authToken}");

$page = curl_exec($c);

if (curl_errno($c)) {
    $error = curl_error($c);
    $response = 'retry later';
} else {
    $data = json_decode($page, true);
    if ($data['status'] == 'queued') {
        $response = 'ok';
    } else {
        $response = 'failure';
    }
}
curl_close($c);

end:
header('Content-Type:text/plain');
echo $response;
