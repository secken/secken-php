# Secken API Library for PHP

A php library for using the Secken API.


## Prerequisites

 * PHP 5.3 or above
 * Secken Account and An application
 
 Download [here](https://www.secken.com/download) secken client, create account, and log in secken Dashboard.    
 A new application can be created in secken Dashboard, you can get appid、 appkey,、authid

## Overview

Secken provides a simple and secure authentication service. Secken APIs can be integrated by any application to enforce the security of user accounts. 

The PHP library is an easy-to-use tool, which allows the developers to access Secken more effectively.

For more detailed information, you can find [here](https://www.secken.com/api/).



## How To Use
### Initialize 

	include_once 'secken.class.php';

    $app_id     = 'app_id';
    $app_key    = 'app_key';
    $auth_id    = 'auth_id';


### Creating a instance

	$secken_api = new secken($app_id,$app_key,$auth_id);

### Request a QRCode for Binding

If the request is successful, will return the qrcode url,
and a single event_id correspond to the qrcode,the event_id will use in the getResult interface.

    $ret  = $secken_api->getBinding();

    # Step 2 - Check the returned result
    if ( $secken_api->getCode() != 200 ){
        var_dump($secken_api->getCode(), $secken_api->getMessage());

    } else {
        var_dump($ret);
    }


### Request a QRCode for Auth

If the request is successful, will return the qrcode url,
and a single event_id correspond to the qrcode,the event_id will use in the getResult interface.

    $ret  = $secken_api->getAuth();

    if ( $secken_api->getCode() != 200 ){
        var_dump($secken_api->getCode(), $secken_api->getMessage());

    } else {
        var_dump($ret);
    }

### Request a user online authentication

When calling this method, the server will push a verifying request to client’s mobile device, the client can select allowing or refusing this operation.

    $ret  = $secken_api->realtimeAuth($action_type,$uid);
    
    if ( $secken_api->getCode() != 200 ){
        var_dump($secken_api->getCode(), $secken_api->getMessage());

    } else {
        var_dump($ret);
    }

### Request a user offline authentication

When there is no Internet connection, the clients are allowed to do offline verification. The 6-digit code is indicated on secken app.

    $ret  = $secken_api->offlineAuth($uid,$dynamic_code);

    if ( $secken_api->getCode() != 200 ){
        var_dump($secken_api->getCode(), $secken_api->getMessage());

    } else {
        var_dump($ret);
    }
    
    
### Get event results

Once the methods like getBinding(), getAuth() and realtimeAuth() are called successfully, it triggers a special event, which is identified by a unique event_id. 


    $ret  = $secken_api->getResult($event_id);

    if ( $secken_api->getCode() != 200 ){
        var_dump($secken_api->getCode(), $secken_api->getMessage());

    } else {
        var_dump($ret);
    }
    
Regarding the event_id, this method returns a status code and informs those methods which value should be returned. A list of status code is described below:

* 200 ok
getBinding() and getAuth() return a value called uid. realtimeAuth() returns True, which represents the uid has been verified.

* 602 re-inquiry
The event is still in period of validity. This method requests event_id repeatedly.

* 603 invalid
The event is out of date. This method cancels requesting event_id.   

###Get authPage

When there is no Internet connection or secken app fails scanning the code, the app can call this API to do offline verification. After offline verification works successfully, the app calls callback function and returns to the previous website. In the mean time, the signature should be verified to avoid malicious deception.


    $ret  = $secken_api->getAuthPage($callback);

    if ( $secken_api->getCode() != 200 ){
        var_dump($secken_api->getCode(), $secken_api->getMessage());

    } else {
        var_dump($ret);
    }
    
##Error code
 
#####Success 

* 200 - ok

#####Client Error

* 400 - requested parameter format not satisfiable
* 401 - 6-digit code timeout
* 402 - app_id error
* 403 - requested signature error
* 404 - requested API not exist
* 405 - requested method error
* 406 - not in application whitelists
* 407 - Too many requests in 30s, please reload offline authentication page

#####Server Error

* 500 - service unavailable
* 501 - failed generating QR code
* 600 - 6-digit code verified error
* 601 - refuse authorization
* 602 - wait for user's response, please try again
* 603 - response timeout, refuse to try again
* 604 - user not exist

## Contact

web：[www.secken.com](https://www.secken.com)    

Email: [support@secken.com](mailto:support@secken.com)