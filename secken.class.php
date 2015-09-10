<?php
/**
 * PHP SDK for yangcong.com
 * 洋葱授权类 v2.0
 *
 * 洋葱开放API文档
 * https://www.yangcong.com/api
 *
 **/

class secken {

    //应用id
    private $app_id = '';

    //应用Key
    private $app_key = '';

    //web授权code
    private $auth_id = '';

    //api请求地址
    const BASE_URL              = 'https://api.yangcong.com/v2/';

    //获取可绑定洋葱客户的二维码
    const QRCODE_FOR_BINDING    = 'qrcode_for_binding';

    //获取通过洋葱进行验证的二维码
    const QRCODE_FOR_AUTH       = 'qrcode_for_auth';

    //根据 event_id 查询详细事件信息
    const EVENT_RESULT          = 'event_result';

    //洋葱在线授权验证
    const REALTIME_AUTH         = 'realtime_authorization';

    //洋葱离线授权验证
    const OFFLINE_AUTH          = 'offline_authorization';

    //获取洋葱授权网页
    const AUTH_PAGE             = 'https://auth.yangcong.com/v2/auth_page';

    /**
     * 错误码
     * @var array
     */
    private $errorCode = array(
        200 => '请求成功',
        400 => '请求参数格式错误',
        401 => 'app 状态错误',
        402 => 'app_id错误',
        403 => '请求签名错误',
        404 => '请求API不存在',
        405 => '请求方法错误',
        406 => '不在应用白名单里',
        407 => '30s离线验证太多次，请重新打开离线验证页面',
        500 => '洋葱系统服务错误',
        501 => '生成二维码图片失败',
        600 => '动态验证码错误',
        601 => '用户拒绝授权',
        602 => '等待用户响应超时，可重试',
        603 => '等待用户响应超时，不可重试',
        604 => '用户或event_id不存在',
        605 => '用户未开启该验证类型'
    );

    /**
     * 初始化
     */
    public function __construct($app_id, $app_key, $auth_id) {
        $this->app_id   = $app_id;
        $this->app_key  = $app_key;
        $this->auth_id  = $auth_id;
    }

    /**
     * 获取绑定二维码
     * @param
     * auth_type    Int    验证类型(可选)（1: 点击确认按钮,默认 2: 使用手势密码 3: 人脸验证 4: 声音验证）
     * callback     String 回调地址（可选）
     * @return array
     * status       Int     状态码
     * description  String  状态码对应描述信息
     * qrcode_url   String  二维码地址
     * qrcode_data  String  二维码图片的字符串内容
     * event_id     String  事件ID,可调用event_result API来获取扫描结果,如果设置了callback，则无法获取扫描结果
     * signature    String  签名，可保证数据完整性
     */
    public function getBinding($auth_type = null, $callback = '') {
        $data  = array();
        $data  = array(
            'app_id'    => $this->app_id
        );

        if( $auth_type ) $data['auth_type'] = intval($auth_type);
        if( $callback ) $data['callback'] = urlencode($callback);

        $data['signature'] = $this->getSignature($data);

        $url    = $this->gen_get_url(self::QRCODE_FOR_BINDING, $data);
        $ret    = $this->request($url);

        return $this->prettyRet($ret);
    }

    /**
     * 获取登录二维码
     * @param
     * auth_type Int    验证类型(可选)（1: 点击确认按钮,默认 2: 使用手势密码 3: 人脸验证 4: 声音验证）
     * callback  String 回调地址(可选)
     * @return array
     * status       Int     状态码
     * description  String  状态码对应描述信息
     * qrcode_url   String  二维码地址
     * qrcode_data  String  二维码图片的字符串内容
     * event_id     String  事件ID,可调用event_result API来获取扫描结果,如果设置了callback，则无法获取扫描结果
     * signature    String  签名，可保证数据完整性
     */
    public function getAuth($auth_type = null, $callback = '') {
        $data   = array();
        $data   = array(
            'app_id'    => $this->app_id
        );

        if( $auth_type ) $data['auth_type'] = intval($auth_type);
        if( $callback ) $data['callback'] = urlencode($callback);

        $data['signature'] = $this->getSignature($data);

        $url    = $this->gen_get_url(self::QRCODE_FOR_AUTH, $data);
        $ret    = $this->request($url);

        return $this->prettyRet($ret);
    }

    /**
     * 查询UUID事件结果
     * @param
     * event_id     String   事件id
     * signature    String   签名，用于确保客户端提交数据的完整性
     * @return array
     * status       Int     状态码
     * description  String  状态码对应描述信息
     * event_id     String  事件ID,可调用event_result API来获取扫描结果,如果设置了callback，则无法获取扫描结果
     * uid          String  用户在洋葱上对应ID
     * signature    String  签名，可保证数据完整性
     */
    public function getResult($event_id) {
        $data   = array();
        $data   = array(
            'app_id'    => $this->app_id,
            'event_id'  => $event_id
        );

        $data['signature'] = $this->getSignature($data);

        $url    = $this->gen_get_url(self::EVENT_RESULT, $data);
        $ret    = $this->request($url);

        return $this->prettyRet($ret);
    }

    /**
     * 实时验证
     * @param
     * action_type   Int     操作类型(1:登录验证，2:请求验证，3:交易验证，4:其它验证)
     * auth_type     Int     验证类型（1: 点击确认按钮 2: 使用手势密码 3: 人脸验证 4: 声音验证）
     * callback      String  回调地址，当用户同意或拒绝验证的后续处理（可选）
     * uid           String  用户ID
     * user_ip       String  用户Ip地址(可选)
     * username      String  第三方用户名，需要URL编码（可选）
     * signature     String  签名，用于确保客户端提交数据的完整性
     * @return array
     * status        Int     状态码
     * description   String  状态码对应描述信息
     * event_id      String  事件ID,可调用event_result API来获取扫描结果,如果设置了callback，则无法获取扫描结果
     * signature     String  签名，可保证数据完整性
     */
    public function realtimeAuth($uid, $action_type = 1, $auth_type=1, $callback='', $user_ip = '', $username = '') {
        $data   = array();
        $data   = array(
            'action_type'   => intval($action_type),
            'app_id'        => $this->app_id,
            'auth_type'     => intval($auth_type),
            'uid'           => $uid
        );

        if ( $callback ) $data['callback'] = urlencode($callback);
        if ( $user_ip )  $data['user_ip']  = $user_ip;
        if ( $username ) $data['username'] = urlencode($username);

        $data['signature'] = $this->getSignature($data);

        $url    = self::BASE_URL . self::REALTIME_AUTH;

        $ret    = $this->request($url, 'POST', $data);

        return $this->prettyRet($ret);
    }

    /**
     * 动态码验证
     * @param
     * uid            String  用户ID
     * dynamic_code   Int     6位动态码
     * signature      String  签名，用于确保客户端提交数据的完整性
     * @return array
     * status         Int     状态码
     * description    String  状态码对应描述信息
     * signature      String  签名，可保证数据完整性
     */
    public function offlineAuth($uid, $dynamic_code) {
        $data   = array();
        $data   = array(
            'appid'         => $this->app_id,
            'uid'           => $uid,
            'dynamic_code'  => intval($dynamic_code)
        );

        $data['signature'] = $this->getSignature($data);

        $url    = self::BASE_URL . self::OFFLINE_AUTH;

        $ret    = $this->request($url, 'POST', $data);

        return $this->prettyRet($ret);
    }

    /**
     * 洋葱网页授权
     * @param
     * auth_id    String  授权ID
     * timestamp  Int     发起请求时的时间戳
     * callback   String  回调地址
     * signature  String  签名，用于确保客户端提交数据的完整性
     * @return
     * uid        String  洋葱用户ID
     * timestamp  Int     服务器返回数据时的时间戳
     * signature  String  签名，可保证数据完整性
     */
    public function getAuthPage($callback) {

        $data   = array();
        $data   = array(
            'auth_id'       => $this->auth_id,
            'timestamp'     => time(),
            'callback'      => urlencode($callback)
        );

        $data['signature'] = $this->getSignature($data);

        $ret   = $this->request(self::AUTH_PAGE);

        return $this->prettyRet($ret);
    }

    /**
     * 生成签名
     * @param
     * params  Array  要签名的参数
     * @return String 签名的MD5串
     */
    private function getSignature($params) {
        ksort($params);
        $str = '';

        foreach ( $params as $key => $value ) {
            $str .= "$key=$value";
        }

        return md5($str . $this->app_key);
    }

    /**
     * 返回错误消息
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * 返回错误码
     * @return string
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * 处理返回信息
     * @return Mix
     */
    private function prettyRet($ret) {
        if ( is_string($ret) ) {
            return $ret;
        }

        $this->code = isset($ret['status'])? $ret['status'] : false;

        if(isset($this->errorCode[$this->code])){
            $this->message = $this->errorCode[$this->code];
        }else{
            $this->message = isset($ret['description']) ? $ret['description'] : 'UNKNOW ERROR';
        }

        return $ret;
    }


    /**
     * 生成请求连接，用于发起GET请求
     * @param
     * action_url    String    请求api地址
     * data          Array     请求参数
     * @return String
     **/
    private function gen_get_url($action_url, $data) {
        return self::BASE_URL . $action_url. '?' . http_build_query($data);
    }


    /**
     * 发送HTTP请求到洋葱服务器
     * @param
     * url      String  API 的 URL 地址
     * method   Sting   HTTP方法，POST | GET
     * data     Array   发送的参数，如果 method 为 GET，留空即可
     * @return  Mix
     **/
    private function request($url, $method = 'GET', $data = array()) {
        if ( !function_exists('curl_init') ) {
            die('Need to open the curl extension');
        }

        if ( !$url || !in_array($method, array('GET', 'POST')) ) {
            return false;
        }

        $ci = curl_init();

        curl_setopt($ci, CURLOPT_URL, $url);
        curl_setopt($ci, CURLOPT_USERAGENT, 'PHP SDK for yangcong/v2.0 (yangcong.com)');
        curl_setopt($ci, CURLOPT_HEADER, FALSE);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_TIMEOUT, 30);
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);

        if ( $method == 'POST' ) {
            curl_setopt($ci, CURLOPT_POST, TRUE);
            curl_setopt($ci, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $response   = curl_exec($ci);

        if ( curl_errno($ci) ) {
            return curl_error($ci);
        }

        $ret    = json_decode($response, true);
        if ( !$ret ) {
            return 'response is error, can not be json decode: ' . $response;
        }

        return $ret;
    }

}
