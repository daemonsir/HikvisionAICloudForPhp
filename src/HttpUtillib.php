<?php
namespace Hik\FaceApi;
/**
 * Class HttpUtillib
 * @package HikFace
 * @author hs96.cn@gmail.com
 */
class HttpUtilLib
{
    protected $appKey      = '';
    protected $appSecret   = '';
    public    $time;
    public    $contentType = 'application/json;charset=UTF-8';
    public    $accept       = 'application/json';
    public    $charset       = 'utf-8';

    public function __construct ($appkey ,$secret ,$time)
    {
        //init charset and time and
        header('Content-type:text/html; Charset=utf-8');
        date_default_timezone_set('PRC');
        if ($appkey != '') $this->appKey = $appkey;
        if ($secret != '') $this->appSecret = $secret;
        $this->time   = $time;
    }

    /**
     * @param $postData
     * @param $url
     * @return string
     * 以appSecret为密钥，使用HmacSHA256算法对签名字符串生成消息摘要，对消息摘要使用BASE64算法生成签名（签名过程中的编码方式全为UTF-8）
     */
    function getSign ($postData ,$url)
    {
        return base64_encode(hash_hmac('sha256' ,$this->getSignStr($postData ,$url) ,$this->appSecret ,true)); //生成消息摘要
    }

    /**
     * @param $postData
     * @param $url
     * @return string
     */
    function getSignStr ($postData ,$url)
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
        $str  = "POST" . $next .$this->accept . $next . $this->contentType . $next;
        $str  .= "x-ca-key:" . $this->appKey . $next;
        $str  .= "x-ca-timestamp:" . $this->time . $next;
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
                $v = $this->characet($v ,$this->charset);
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
    function getMessage ($postData)
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
            $fileType = $this->charset;
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
        //此处curl php5.4不兼容
        //https请求 不验证证书和host
        curl_setopt($ch ,CURLOPT_SSL_VERIFYPEER ,false);
        curl_setopt($ch ,CURLOPT_SSL_VERIFYHOST ,false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}