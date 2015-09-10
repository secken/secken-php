<?php
define('SDK_DIR', dirname(dirname(dirname(__FILE__))));

include_once SDK_DIR.'/secken.class.php';

//填写洋葱网给您申请的app_id
$app_id     = '2ajfOJPKa0w0lJxyHR1csH6QPfrE3GsH';

//填写您在洋葱网申请的app_key
$app_key    = 'c3Z90bVBhaGifPsiYhzN';

//填写您在洋葱网申请的auth_id
$auth_id    = 'kSP6hqFLg9aDXBeZjqzl';

//实例化洋葱认证类
$secken_api = new secken($app_id,$app_key,$auth_id);


$ac = isset($_GET['ac']) ? $_GET['ac'] : 'none';

//发起验证请求
if($ac == 'qrcode_for_auth'){
    $auth_type = isset($_GET['auth_type']) ? $_GET['auth_type'] : 1;
    $resp = $secken_api -> getAuth($auth_type);
    echo json_encode($resp);
}

//获取事件结果
if($ac == 'event_result'){
    $event_id = isset($_GET['event_id']) ? $_GET['event_id'] : '';
    $resp = $secken_api -> getResult($event_id);

    if(is_array($resp)){
        $resp['description'] = $secken_api -> getMessage();
    }

    echo json_encode($resp);
}


?>
