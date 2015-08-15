<?php

    include_once 'secken.class.php';

    $app_id     = 'app_id';
    $app_key    = 'app_key';
    $auth_id    = 'auth_id';

    // Create an API object using your credentials
    $secken_api = new secken($app_id,$app_key,$auth_id);

    # Step 1 - Get an qrcode for binding
    $ret  = $secken_api->getAuth(1, "https://callback.com/path");

    //$ret = $secken_api->realtimeAuth('uid', 1, 1, 'https://callback.com/path', '123.123.123.123', '名字');

    //$ret = $secken_api->offline_auth('2121','sd');

    # Step 2 - Check the returned result
    if ( $secken_api->getCode() != 200 ){
        var_dump($secken_api->getCode(), $secken_api->getMessage());

    } else {
        var_dump($ret);
    }

?>
