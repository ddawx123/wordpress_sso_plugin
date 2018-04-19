<?php

class Common {

    static private $_instance = null; //定义实例化空白对象
    private $_apiurl = 'https://passport.dingstudio.cn/api?format=json&action=verify'; //定义SSO服务端地址

    /**
     * 构造函数
     */
    private function __construct() {
        header('Content-Type: application/json; charset=UTF-8');
    }
    
    /**
     * 统一实例入口
     * @return mixed 实例对象
     */
    static public function getInstance() {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 从已有SSO会话获取用户资料
     * @param string $token 会话密钥
     * @return mixed 如未登录则返回false，已登录则返回用户资料数据集合
     */
    public function getUserInfo($token) {
        $HttpsContextOptions = array(
            'ssl'	=>	array(
                'verify_peer'	=>	false,
                'verify_peer_name'	=>	false
            )
        );
        $user = file_get_contents($this->_apiurl.'&token='.$token.'&reqtime='.sha1(date('YmdHis', time())), false, stream_context_create($HttpsContextOptions));
        $userinfo = json_decode($user);
        if (!isset($userinfo->data->username) && !isset($userinfo->data->newtoken)) {
            return false;
        }
        else {
            return $userinfo->data;
        }
    }

    /**
     * 拼装JSON响应报文
     * @param integer $code 响应状态码
     * @param string $message 响应消息内容
     * @param array $data 响应包含数据
     * @return string JSON字符串
     */
    public function generateMsg($code = 200, $message = '', $data = array()) {
        self::httpStatus($code);
        $arr = array(
            'code'  =>  $code,
            'message'   =>  $message,
            'data'  =>  $data,
            'requestId' =>  date('YmdHis', time())
        );
        return json_encode($arr);
    }

    /**
     * 设置http状态码
     * @param integer $num 状态码
     * @return mixed http_header信息修改
     */
    public function httpStatus($num) {
        static $http = array (
            100 => "HTTP/1.1 100 Continue",
            101 => "HTTP/1.1 101 Switching Protocols",
            200 => "HTTP/1.1 200 OK",
            201 => "HTTP/1.1 201 Created",
            202 => "HTTP/1.1 202 Accepted",
            203 => "HTTP/1.1 203 Non-Authoritative Information",
            204 => "HTTP/1.1 204 No Content",
            205 => "HTTP/1.1 205 Reset Content",
            206 => "HTTP/1.1 206 Partial Content",
            300 => "HTTP/1.1 300 Multiple Choices",
            301 => "HTTP/1.1 301 Moved Permanently",
            302 => "HTTP/1.1 302 Found",
            303 => "HTTP/1.1 303 See Other",
            304 => "HTTP/1.1 304 Not Modified",
            305 => "HTTP/1.1 305 Use Proxy",
            307 => "HTTP/1.1 307 Temporary Redirect",
            400 => "HTTP/1.1 400 Bad Request",
            401 => "HTTP/1.1 401 Unauthorized",
            402 => "HTTP/1.1 402 Payment Required",
            403 => "HTTP/1.1 403 Forbidden",
            404 => "HTTP/1.1 404 Not Found",
            405 => "HTTP/1.1 405 Method Not Allowed",
            406 => "HTTP/1.1 406 Not Acceptable",
            407 => "HTTP/1.1 407 Proxy Authentication Required",
            408 => "HTTP/1.1 408 Request Time-out",
            409 => "HTTP/1.1 409 Conflict",
            410 => "HTTP/1.1 410 Gone",
            411 => "HTTP/1.1 411 Length Required",
            412 => "HTTP/1.1 412 Precondition Failed",
            413 => "HTTP/1.1 413 Request Entity Too Large",
            414 => "HTTP/1.1 414 Request-URI Too Large",
            415 => "HTTP/1.1 415 Unsupported Media Type",
            416 => "HTTP/1.1 416 Requested range not satisfiable",
            417 => "HTTP/1.1 417 Expectation Failed",
            500 => "HTTP/1.1 500 Internal Server Error",
            501 => "HTTP/1.1 501 Not Implemented",
            502 => "HTTP/1.1 502 Bad Gateway",
            503 => "HTTP/1.1 503 Service Unavailable",
            504 => "HTTP/1.1 504 Gateway Time-out"
        );
        if (isset($http[$num])) {
            header($http[$num]);
        }
        else {
            header($http[200]);
        }
    }
}