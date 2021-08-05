<?php

namespace Hik\HikDoorApi;
/**
 * Class HikDoorApi
 * @package Hik
 * @author hc
 */
class HikFaceApi
{
    private   $preUrl;
    private   $httpUtilLib = '';
    public    $accept      = 'application/json';//accept
    public    $contentType = 'application/json;charset=UTF-8';//类型
    protected $appKey      = '';
    protected $appSecret   = '';
    protected $time        = '';

    public function __construct ($appKey ,$secret ,$ip ,$port ,$isHttps)
    {
        //hs:注入验签等工具类中
        list($msec ,$sec) = explode(' ' ,microtime());
        $this->time = (float)sprintf('%.0f' ,(floatval($msec) + floatval($sec)) * 1000);

        if ($appKey != '') $this->appKey = $appKey;
        if ($secret != '') $this->appSecret = $secret;
        if ($isHttps) {
            $this->preUrl .= 'https://';
        } else {
            $this->preUrl .= 'http://';
        }
        if ($ip) $this->preUrl .= $ip;
        if ($port) $this->preUrl .= ':' . $port;

        $this->httpUtilLib = new HttpUtillib($appKey ,$secret ,$this->time);
    }

   //查询门禁设备列表v2
    public function acsDevice ($pageNo ,$pageSize)
    {
        $body    = ['pageNo' => $pageNo ,'pageSize' => $pageSize];
        $uri     = "/artemis/api/resource/v2/acsDevice/search";
        $sign    = $this->httpUtilLib->getSign($body ,$uri);
        $options = [
            CURLOPT_HTTPHEADER => [
                'Accept:' . $this->accept ,
                'Content-Type:' . $this->contentType ,
                'x-Ca-Key:' . $this->appKey ,
                'X-Ca-Signature:' . $sign ,
                'X-Ca-Timestamp:' . $this->time ,
                'X-Ca-Signature-Headers:' . 'x-ca-key,x-ca-timestamp' ,
            ]
        ];

        $result  = $this->httpUtilLib->curlPost($this->preUrl . $uri ,json_encode($body) ,$options);
        if (empty($result)) {
            return ['code' => '1' ,'msg' => "$uri : POST fail"];
        } else {
            return json_decode($result ,true);
        }

    }

    //查询门禁点事件v2
    public function doorEvents ($pageNo ,$pageSize)
    {
        //时间（事件开始时间，采用ISO8601时间格式，与endTime配对使用，不能单独使用，时间范围最大不能超过3个月)
        $body    = ['pageNo' => $pageNo ,'pageSize' => $pageSize,
                    'startTime'=>'2021-05-6T12:00:00+08:00',
                    'endTime'=>'2021-08-6T12:00:00+08:00',
                    'receiveStartTime'=>'2021-05-6T12:00:00+08:00',
                    'receiveEndTime'=>'2021-08-6T12:00:00+08:00'
            ];
        $uri     = "/artemis/api/acs/v2/door/events";
        $sign    = $this->httpUtilLib->getSign($body ,$uri);
        $options = [
            CURLOPT_HTTPHEADER => [
                'Accept:' . $this->accept ,
                'Content-Type:' . $this->contentType ,
                'x-Ca-Key:' . $this->appKey ,
                'X-Ca-Signature:' . $sign ,
                'X-Ca-Timestamp:' . $this->time ,
                'X-Ca-Signature-Headers:' . 'x-ca-key,x-ca-timestamp' ,
            ]
        ];

        $result  = $this->httpUtilLib->curlPost($this->preUrl . $uri ,json_encode($body) ,$options);
        if (empty($result)) {
            return ['code' => '1' ,'msg' => "$uri : POST fail"];
        } else {
            return json_decode($result ,true);
        }

    }

    //查询门禁点状态
    public function doorStates ()
    {
        $body    = ['doorIndexCodes'=>[]];
        $uri     = "/artemis/api/acs/v1/door/states";
        $sign    = $this->httpUtilLib->getSign($body ,$uri);
        $options = [
            CURLOPT_HTTPHEADER => [
                'Accept:' . $this->accept ,
                'Content-Type:' . $this->contentType ,
                'x-Ca-Key:' . $this->appKey ,
                'X-Ca-Signature:' . $sign ,
                'X-Ca-Timestamp:' . $this->time ,
                'X-Ca-Signature-Headers:' . 'x-ca-key,x-ca-timestamp' ,
            ]
        ];

        $result  = $this->httpUtilLib->curlPost($this->preUrl . $uri ,json_encode($body) ,$options);
        if (empty($result)) {
            return ['code' => '1' ,'msg' => "$uri : POST fail"];
        } else {
            return json_decode($result ,true);
        }

    }

    //查询门禁点列表v2
    public function doorSearch ($pageNo ,$pageSize)
    {
        $body    = ['pageNo' => $pageNo ,'pageSize' => $pageSize];
        $uri     = "/artemis/api/resource/v2/door/search";
        $sign    = $this->httpUtilLib->getSign($body ,$uri);
        $options = [
            CURLOPT_HTTPHEADER => [
                'Accept:' . $this->accept ,
                'Content-Type:' . $this->contentType ,
                'x-Ca-Key:' . $this->appKey ,
                'X-Ca-Signature:' . $sign ,
                'X-Ca-Timestamp:' . $this->time ,
                'X-Ca-Signature-Headers:' . 'x-ca-key,x-ca-timestamp' ,
            ]
        ];

        $result  = $this->httpUtilLib->curlPost($this->preUrl . $uri ,json_encode($body) ,$options);
        if (empty($result)) {
            return ['code' => '1' ,'msg' => "$uri : POST fail"];
        } else {
            return json_decode($result ,true);
        }

    }
}
