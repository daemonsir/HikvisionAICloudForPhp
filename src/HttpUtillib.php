<?php
namespace Hik\FaceApi;
/**
 * Class HttpUtillib
 * @package HikFace
 * hs:验签等http操作工具类
 */
class HttpUtillib
{
    protected $_app_key      = '';
    protected $_app_secret   = '';
    public    $_time;
    public    $_content_type = 'application/json;charset=UTF-8';
    public    $_accept       = 'application/json';
    public    $_charset       = 'utf-8';

    public function __construct ($appkey ,$secret ,$time)
    {
        //初始化字符集 时间戳
        header('Content-type:text/html; Charset=utf-8');
        date_default_timezone_set('PRC');
        if ($appkey != '') $this->_app_key = $appkey;
        if ($secret != '') $this->_app_secret = $secret;
        $this->_time   = $time;
    }

    /**
     * @param $postData
     * @param $url
     * @return string
     * 以appSecret为密钥，使用HmacSHA256算法对签名字符串生成消息摘要，对消息摘要使用BASE64算法生成签名（签名过程中的编码方式全为UTF-8）
     */
    function get_sign ($postData ,$url)
    {
        return base64_encode(hash_hmac('sha256' ,$this->get_sign_str($postData ,$url) ,$this->_app_secret ,true)); //生成消息摘要
    }

    /**
     * @param $postData
     * @param $url
     * @return string
     */
    function get_sign_str ($postData ,$url)
    {
        /**
         *    注：当请求的body非form表单时，建议调用方对body计算MD5参与签名，将Content-MD5放入请求headers中，value可以为任意值，服务端不会取Content-MD5的value进行校验，而是根据body中的数据计算MD5进行校验。
         *    官方指定格式
         *    HTTP METHOD + "\n" +
         *    Accept + "\n" +     //建议显示设置 Accept Header，部分 Http 客户端当 Accept 为空时会给 Accept
         *    设置默认值：*\/*，导致签名校验失败。
         *    Content-MD5  + "\n" +
         *    Content-Type + "\n" +
         *    Date + "\n" +
         *    Headers +
         *    Url
         */
        $next = "\n";
        $str  = "POST" . $next .$this->_accept . $next . $this->_content_type . $next;
        $str  .= "x-ca-key:" . $this->_app_key . $next;
        $str  .= "x-ca-timestamp:" . $this->_time . $next;
        $str  .= $url;
        return $str;
    }

    /**
     * @param $params
     * @return string
     */
    public function getSignContent ($params)
    {
        ksort($params);
        $stringToBeSigned = "";
        $i                = 0;
        $len              = count($params);
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v ,0 ,1)) {
                // 转换成目标字符集
                $v = $this->characet($v ,$this->_charset);
                if ($i == 0) {
                    $stringToBeSigned .= "?$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }
        unset ($k ,$v);
        return $stringToBeSigned;
    }

    /**
     * @param $postData
     * @return string
     */
    function get_message ($postData)
    {
        $str = str_replace(['{' ,'}' ,'"'] ,'' ,json_encode($postData));
        return base64_encode(md5($str));
    }

    /**
     * 校验$value是否非空
     *  if not set ,return true;
     *    if is null , return true;
     **/
    protected function checkEmpty ($value)
    {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;
        return false;
    }

    /**
     * 转换字符集编码
     * @param $data
     * @param $targetCharset
     * @return string
     */
    function characet ($data ,$targetCharset)
    {
        if (!empty($data)) {
            $fileType = $this->_charset;
            if (strcasecmp($fileType ,$targetCharset) != 0) {
                $data = mb_convert_encoding($data ,$targetCharset ,$fileType);
            }
        }
        return $data;
    }

    /**
     * @param string $url
     * @param string $postData
     * @param array $options
     * @return bool|string
     */
    public function curlPost ($url = '' ,$postData = '' ,$options = [])
    {
        if (is_array($postData)) {
            $postData = http_build_query($postData);
        }
        $ch = curl_init();
        curl_setopt($ch ,CURLOPT_URL ,$url);
        curl_setopt($ch ,CURLOPT_RETURNTRANSFER ,1);
        curl_setopt($ch ,CURLOPT_POST ,1);
        curl_setopt($ch ,CURLOPT_POSTFIELDS ,$postData);
        curl_setopt($ch ,CURLOPT_TIMEOUT ,30); //设置cURL允许执行的最长秒数
        if (!empty($options)) {
            curl_setopt_array($ch ,$options);
        }
        //https请求 不验证证书和host
        curl_setopt($ch ,CURLOPT_SSL_VERIFYPEER ,false);
        curl_setopt($ch ,CURLOPT_SSL_VERIFYHOST ,false);
        $data = curl_exec($ch);
        dump(curl_error($ch));
        dump($data);exit;
        curl_close($ch);
        dump($data);exit;
        return $data;
    }
}