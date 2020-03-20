<?php
//hs:海康类库集成,手动注册到composer映射中。
//hs:此类库依赖guzzleHttp
namespace Hik\FaceApi;
/**
 * Class HikFaceApi
 * @package HikFace
 * @author hs96.cn@gmail.com
 */
class HikFaceApi
{
    private   $_pre_url;
    private   $_http_util_lib = '';
    public    $_accept        = "application/json";//accept
    public    $_content_type  = "application/json;charset=UTF-8";//类型
    protected $_app_key       = "";
    protected $_time          = "";

    public function __construct ($appkey ,$secret ,$ip ,$port ,$isHttps)
    {
        //hs:注入验签等工具类中
        list($msec ,$sec) = explode(' ' ,microtime());
        $this->_time = (float)sprintf('%.0f' ,(floatval($msec) + floatval($sec)) * 1000);

        if ($appkey != '') $this->_app_key = $appkey;
        if ($secret != '') $this->_app_secret = $secret;
        if ($isHttps) {
            $this->_pre_url .= "https://";
        } else {
            $this->_pre_url .= "http://";
        }
        if ($ip) $this->_pre_url .= $ip;
        if ($port) $this->_pre_url .= ':'.$port;

        $this->_http_util_lib = new HttpUtillib($appkey ,$secret ,$this->_time);
    }

    //获取资源列表
    public function getresourceslistV2 ($body)
    {
        $uri  = "/artemis/api/irds/v2/deviceResource/resources";
        $sign = $this->_http_util_lib->get_sign($body ,$uri);
        // 填充Url
        $options = [
            CURLOPT_HTTPHEADER => [
                "Accept:" . $this->_accept ,
                "Content-Type:" . $this->_content_type ,
                "x-Ca-Key:" . $this->_app_key ,
                "X-Ca-Signature:" . $sign ,
                "X-Ca-Timestamp:" . $this->_time ,
                "X-Ca-Signature-Headers:" . "x-ca-key,x-ca-timestamp" ,
            ]
        ];
        $result  = $this->_http_util_lib->curlPost($this->_pre_url . $uri ,json_encode($body) ,$options);
        return json_decode($result ,true);
    }
}