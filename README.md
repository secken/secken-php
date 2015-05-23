# Secken API Library for PHP
------
A php library for using the Secken API.


##Prerequisites
------
 * PHP 5.3 or above
 * Secken Account and An application
 
 Download [here](https://www.secken.com/download) secken client, create account, and log in secken Dashboard.    
 A new application can be created in secken Dashboard, you can get appid、 appkey,、authid

##Overview
------


Secken provides a simple and safe authentication service, other applications can be integrated by using API development libraries, to protect the security of the user account quickly.

The PHP library for developers to quickly integrate secken, which does not need direct interact with the platform API.

Developer documentation for using the Secken API can be found [here](https://www.secken.com/api/).



##How To Use
-----
###Initialize 

	include_once 'secken.class.php';

    $app_id     = '1234';
    $app_key    = 'app_key';
    $auth_id    = 'auth_id';


###Creating a instance

	$secken_api = new secken($app_id,$app_key,$auth_id);

###Request a QRCode for Binding
-------
If the request is successful, will return the qrcode url,
and a single event_id correspond to the qrcode,the event_id will use in the getResult interface.

    $ret  = $secken_api->getBinding();

    # Step 2 - Check the returned result
    if ( $secken_api->getCode() != 200 ){
        var_dump($secken_api->getCode(), $secken_api->getMessage());

    } else {
        var_dump($ret);
    }


###Request a QRCode for Auth
-------
If the request is successful, will return the qrcode url,
and a single event_id correspond to the qrcode,the event_id will use in the getResult interface.

    $ret  = $secken_api->getAuth();

    if ( $secken_api->getCode() != 200 ){
        var_dump($secken_api->getCode(), $secken_api->getMessage());

    } else {
        var_dump($ret);
    }

###Request a user online authentication

    $ret  = $secken_api->realtimeAuth($action_type,$uid);
    
    if ( $secken_api->getCode() != 200 ){
        var_dump($secken_api->getCode(), $secken_api->getMessage());

    } else {
        var_dump($ret);
    }

###Request a user offline authentication

    $ret  = $secken_api->offlineAuth($uid,$dynamic_code);

    if ( $secken_api->getCode() != 200 ){
        var_dump($secken_api->getCode(), $secken_api->getMessage());

    } else {
        var_dump($ret);
    }
    
    
###Get event results

    $ret  = $secken_api->getResult($event_id);

    if ( $secken_api->getCode() != 200 ){
        var_dump($secken_api->getCode(), $secken_api->getMessage());

    } else {
        var_dump($ret);
    }

##Contact
-----
web：[www.secken.com](https://www.secken.com)    

Email: [support@secken.com](mailto:support@secken.com)