#!/usr/bin/php
<?php
error_reporting(-1);

define('LOG_FILE',__FILE__.'.log');
define('n',"\n");

function logIt($str){
        $log = @date('Y-m-d H:i:s').' - '.$str.n;
        file_put_contents(LOG_FILE,$log,FILE_APPEND);
}

$external_ip = trim(shell_exec('dig +short myip.opendns.com @resolver1.opendns.com'));
logIt('External: '.$external_ip);

$domains = [
    [
        'host' => 'example.com',
        'ns'   => 'ns109.ovh.net',
        'ip'   => $external_ip,
        'user' => 'example-com-main',
        'pass' => '_identifier_pass_'
    ]
];


foreach($domains as $domain){
    checkDomain($domain);
}


function checkDomain($domain){
    $saved_ip = trim(shell_exec('dig +short '.$domain['host'].' @'.$domain['ns']));
    $k = '['.$domain['host'] .'] ';
    logIt($k.'Saved: '.$saved_ip);

    if (!$saved_ip || !$domain['ip']){
        return logIt($k.'KO ######');
    }
    if ($saved_ip != $domain['ip']){
        $result = saveDomain($domain['host'],$domain['ip'],$domain['user'],$domain['pass']);
        logIt($k.trim($result));
    }
}

function saveDomain($domain,$ip,$user,$pass){

    $basicAuth = base64_encode($user.':'.$pass);

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "http://www.ovh.com/nic/update?system=dyndns&hostname=".$domain."&myip=".$ip,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "authorization: Basic ".$basicAuth,
        "cache-control: no-cache"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return 'error: ' . $err;
    }

    if (strpos($response,'Authorization Required') !== false){
        return 'error: 401 Authorization Required';
    }else{
        return 'response: '.$response;
    }
}
