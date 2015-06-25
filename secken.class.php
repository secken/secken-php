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
        401 => '动态码过期',
        402 => 'app_id错误',
        403 => '请求签名错误',
        404 => '请你API不存在',
        405 => '请求方法错误',
        406 => '不在应用白名单里',
        407 => '30s离线验证太多次，请重新打开离线验证页面',
        500 => '洋葱系统服务错误',
        501 => '生成二维码图片失败',
        600 => '动态验证码错误',
        601 => '用户拒绝授权',
        602 => '等待用户响应超时，可重试',
        603 => '等待用户响应超时，不可重试',
        604 => '用户不存在'
    );

    /**
     * construct secken object
     */
    public function __construct($app_id, $app_key, $auth_id) {
        $this->app_id   = $app_id;
        $this->app_key  = $app_key;
        $this->auth_id  = $auth_id;
    }

    /**
     * 获取绑定二维码
     * @return array
     * code         成功、错误码
     * message      错误信息
     * qrcode_url   二维码地址
     * uuid         事件id
     */ 
    public function getBinding() {
        $data   = array(
            'app_id'    => $this->app_id,
            'signature' => md5('app_id=' . $this->app_id . $this->app_key)
        );

        $url    = $this->gen_get_url(self::QRCODE_FOR_BINDING, $data);
        $ret    = $this->request($url);

        return $this->prettyRet($ret);
    }

    /**
     * 获取登录二维码
     * @return array
     * code      成功、错误码
     * message   错误信息
     * url       二维码地址
     * uuid      事件id
     */
    public function getAuth() {
        $data   = array(
            'app_id'    => $this->app_id,
            'signature' => md5('app_id=' . $this->app_id . $this->app_key)
        );

        $url    = $this->gen_get_url(self::QRCODE_FOR_AUTH, $data);
        $ret    = $this->request($url);

        return $this->prettyRet($ret);
    }

    /**
     * 查询UUID事件结果
     * @param string $event_id 事件id
     * @return array
     * code    成功、错误码
     * message 错误信息
     * userid  用户ID
     * signature 签名 [MD5(userid=$useridappkey)]
     */
    public function getResult($event_id) {
        $data   = array(
            'app_id'    => $this->app_id,
            'event_id'  => $event_id,
            'signature' => md5('app_id=' . $this->app_id . 'event_id=' . $event_id. $this->app_key)
        );

        $url    = $this->gen_get_url(self::EVENT_RESULT, $data);
        $ret    = $this->request($url);

        return $this->prettyRet($ret);
    }

    /**
     * 一键认证
     * @param string $action_type 请求用户的操作
     * @param string $uid 用户ID
     * @param string $user_ip 用户IP地址(可选)
     * @param string $username 第三方用户名(可选)
     * @return array
     * code     成功、错误码
     * message  错误信息
     * event_id 事件id
     */
    public function realtimeAuth($action_type = 'login', $uid, $user_ip = '', $username = '') {
        $data   = array(
            'action_type'   => $action_type,
            'app_id'        => $this->app_id,
            'uid'           => $uid,
            'user_ip'       => $user_ip, 
            'username'      => $username,
            'signature'     => md5('action_type=' . $action_type . 'appid=' . $this->app_id . 'uid=' . $uid . $this->app_key),
        );

        $url    = self::BASE_URL . self::REALTIME_AUTH;

        $ret    = $this->request($url, 'POST', $data);

        return $this->prettyRet($ret);
    }

    /**
     * 动态码验证
     * @param string $uid 用户ID
     * @param string $dynamic_code 6位数字
     * @return array
     * code    成功、错误码
     * message 错误信息
     */
    public function offlineAuth($uid, $dynamic_code) {
        $data   = array(
            'appid'         => $this->app_id,
            'uid'           => $uid,
            'dynamic_code'  => $dynamic_code,
            'signature'     => md5('appid=' . $this->app_id . 'dynamic_code=' . $dynamic_code. 'uid=' . $uid . $this->app_key),
        );

        $url    = self::BASE_URL . self::OFFLINE_AUTH;

        $ret    = $this->request($url, 'POST', $data);

        return $this->prettyRet($ret);
    }

    /**
     * 洋葱网页授权
     * @param string $callback 回调登陆地址
     * @return string 授权页url
     */
    public function getAuthPage($callback) {
        $_time  = time();
        $data   = array(
            'auth_id'       => $this->auth_id,
            'timestamp'     => $_time,
            'callback'      => $callback,
            'signature'     => md5('appid=' . $this->app_id . 'callback=' . $callback. 'timestamp=' . $_time. $this->app_key),
        );

        unset($_time);

        $url    = self::AUTH_PAGE;
        $ret    = $this->request($url);

        return $this->prettyRet($ret);
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

    private function prettyRet($ret) {
        if ( is_string($ret) ) {
            return $ret;
        }

        $this->code     = isset($ret['status'])? $ret['status'] : false;

        if ( isset($ret['description']) ) {
            $this->message  = $ret['description'];
        } else {
            $this->message  = isset($this->errorCode[$this->code])? $this->errorCode[$this->code] : 'UNKNOW ERROR';
        }

        return $ret;
    }


    /**
     * gen the URL
     *
     **/
    private function gen_get_url($action_url, $data) {
        return self::BASE_URL . $action_url. '?' . http_build_query($data);
    }


    /**
     * send the http request to yangcong API server
     *
     * @param String $url: API 的 URL 地址
     * @param String $method: HTTP方法，POST | GET
     * @param Array $data: 发送的参数，如果 method 为 GET，留空即可
     *
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
