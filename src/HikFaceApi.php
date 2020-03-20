<?php

namespace Hik\FaceApi;
/**
 * Class HikFaceApi
 * @package HikFace
 * @author hs96.cn@gmail.com
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

    //获取资源列表
    public function getResourcesListV2 ($pageNo ,$pageSize ,$resourceType)
    {
        $body    = ['pageNo' => $pageNo ,'pageSize' => $pageSize ,'resourceType' => $resourceType];
        $uri     = "/artemis/api/irds/v2/deviceResource/resources";
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

    /**
     * @param $taskType string 下载任务类型 (5:卡片+人脸)
     * @return mixed
     * 创建下载任务
     */
    public function addTask ($taskType)
    {
        $body    = ['taskType' => $taskType];
        $uri     = '/artemis/api/acps/v1/authDownload/task/addition';
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

    /// <summary>
    /// 人脸评分
    /// a) 传入一张人脸图片，检测人脸图片是否合格，注意，人脸评分是检测人脸质量的辅助手段，只有人脸评分失败（返回错误码0x1f913016）时才认为是人脸图片不合格可通过statusCode查询不合格原因，其它情况如调用超时等情况，图片只是未检测，不能作为人脸不可用的依据。
    /// b) tagId仅用来记录日志
    /// c) 注：此接口需要额外授权
    /// </summary>
    /// <param name="faceimg">人脸人脸图片的二进制数据经过Base64编码后的字符串</param>
    /// <returns></returns>
    ///
    /**
     * @param $base64FaceImg
     * @return array|mixed
     */
    public function checkFace ($base64FaceImg)
    {
        $body    = ['facePicBinaryData' => $base64FaceImg];
        $uri     = '/artemis/api/frs/v1/face/picture/check';
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

    /// <summary>
    /// 获取卡信息
    /// </summary>
    /// <param name="cardno"></param>
    /// <returns></returns></returns>
    /**
     * @param $personIds
     * @return array|mixed
     *
     */
    public function selectCard ($cardno)
    {
        $body    = ['cardNo' => $cardno];
        $uri     = '/artemis/api/irds/v1/card/cardInfo';
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

    /// <summary>
    /// 修改人员
    /// </summary>
    /// <param name="body">{ \"personId\": \"003\",\"personName\": \"name\",\"gender\": \"0\",\"orgIndexCode\": \"orgindexcode\",\"certificateType\": \"990\",\"certificateNo\": \"certificateno \"}</param>
    /// <returns></returns>
    /**
     * @param $personId
     * @param $personName
     * @return array|mixed
     */
    public function updatePerson ($personId ,$personName)
    {
        $body    = ['personId' => $personId ,'personName' => $personName];
        $uri     = '/artemis/api/resource/v1/person/single/update';
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

    /// <summary>
    /// 下载任务中添加数据
    /// </summary>
    /// <param name="taskid">下载任务唯一标识</param>
    /// <param name="personid">人员Id</param>
    /// <param name="resourceinfos">{\"resourceIndexCode\": \"226a2f1790654dfea31ba1fb81dcb60f\",\"resourceType\": \"acsDevice\",\"channelNos\":[1]}</param>
    /// <param name="operatorType">0新增；1修改；2删除</param>
    /// <returns></returns>
    /**
     * @param $taskid
     * @param $personid
     * @param $resourceinfos
     * @param $operatortype
     * @return array|mixed
     */
    public function addTaskData ($taskid ,$personid ,$resourceinfos ,$operatortype)
    {
        $body    = [
            'taskId'        => $taskid ,
            'resourceInfos' => $resourceinfos ,
            'personInfos'   => [
                'personId'     => $personid ,
                'operatorType' => $operatortype
            ]
        ];
        $uri     = '/artemis/api/acps/v1/authDownload/data/addition';
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

        $body   = "{\"taskId\": \"" . $taskid . "\",\"resourceInfos\": [" . $resourceinfos . "] ,\"personInfos\":[{\"personId\": \"" . $personid . "\",\"operatorType\": " . $operatortype . "}]}";
        $result = $this->httpUtilLib->curlPost($this->preUrl . $uri ,$body ,$options);

        if (empty($result)) {
            return ['code' => '1' ,'msg' => "$uri : POST fail"];
        } else {
            return json_decode($result ,true);
        }
    }

    /// <summary>
    /// 开始下载任务
    /// </summary>
    /// <param name="taskid">下载任务唯一标识</param>
    /// <returns></returns>
    /**
     * @param $taskid
     * @return array|mixed
     */
    public function startTask ($taskid)
    {
        $body    = ['taskId' => $taskid];
        $uri     = '/artemis/api/acps/v1/authDownload/task/start';
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

    /// <summary>
    /// 获取组织列表
    /// </summary>
    /// <returns></returns>
    /**
     * @param $pageNo
     * @param $pageSize
     * @return array|mixed
     */
    public function getOrganizationList ($pageNo ,$pageSize)
    {
        $body    = ['pageNo' => $pageNo ,'pageSize' => $pageSize];
        $uri     = '/artemis/api/resource/v1/org/orgList';
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

    /**
     * /// <summary>
     * /// 添加单个人员（带人脸信息）
     * /// </summary>
     * /// <param name="name">姓名</param>
     * /// <param name="orgindexcode">组织</param>
     * /// <param name="certificateno">证件号（不可重复）</param>
     * /// <param name="faceimg">人脸人脸图片的二进制数据经过Base64编码后的字符串(fa)</param>
     * /// <returns></returns>
     * @param $personName
     * @param $orgindexcode
     * @param $certificateno
     * @param $faceimg
     * @return array|mixed
     */
    public function addPerson ($personName ,$orgindexcode ,$certificateno ,$faceimg)
    {
        if ($faceimg == '') {
            $body = "{ \"personName\": \"" . $personName . "\",\"gender\": \"0\",\"orgIndexCode\": \"" . $orgindexcode . "\",\"certificateType\": \"990\",\"certificateNo\": \"" . $certificateno . "\"}";
        } else {
            $body = "{ \"personName\": \"" . $personName . "\",\"gender\": \"0\",\"orgIndexCode\": \"" . $orgindexcode . "\",\"certificateType\": \"990\",\"certificateNo\": \"" . $certificateno . "\",\"faces\": [{\"faceData\":\"" . $faceimg . "\"}]}";
        }

        $uri     = '/artemis/api/resource/v1/person/single/add';
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
        $result  = $this->httpUtilLib->curlPost($this->preUrl . $uri ,$body ,$options);

        if (empty($result)) {
            return ['code' => '1' ,'msg' => "$uri : POST fail"];
        } else {
            return json_decode($result ,true);
        }
    }

    /// <summary>
    /// 获取全部卡片列表（根据personId）
    /// </summary>
    /// <param name="personIds">人员ID集合,多个值使用英文逗号分隔</param>
    /// <returns></returns>
    /**
     * @param $pageNo
     * @param $pageSize
     * @param $personIds
     * @return array|mixed
     */
    public function selectCardList ($pageNo ,$pageSize ,$personIds)
    {
        $body    = ['pageNo' => $pageNo ,'pageSize' => $pageSize ,'personIds' => $personIds];
        $uri     = '/artemis/api/irds/v1/card/advance/cardList';
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

    /// <summary>
    /// 批量删除人员
    /// </summary>
    /// <param name="body">{ \"personIds\": [\"001\"]}</param>
    /// <returns></returns>
    /**
     * @param $personIds
     * @return array|mixed
     */
    public function batchDeletePerson ($personIds)
    {
        $body    = ['personIds' => $personIds];
        $uri     = '/artemis/api/resource/v1/person/batch/delete';
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