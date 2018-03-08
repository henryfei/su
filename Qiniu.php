<?php

namespace su\file;

//七牛文件处理类
use bc\util\CommonFunction;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use bc\util\Util;
use yii\db\Exception;
use Qiniu\Storage\BucketManager;

class Qiniu implements File
{
    //基础的配置参数
    private $accessKey;
    private $secretKey;
    private $auth;
    private $upToken;               //上传凭证
    private $uploadMgr;             //上传管理类
    private $BucketManager;         //主要涉及了空间资源管理及批量操作接口的实现，具体的接口规格可以参考
    private $bucket;                //目标资源空间
    private $key;                   //目标的文件名
    private $expires    =   3600;   //上传凭证的有效期
    private $policy;                //上传的策略
    //"scope":               "<Bucket                   string>",
    //"isPrefixalScope":     "<IsPrefixalScope          int>",
    //"deadline":            "<UnixTimestamp            uint32>",
    //"insertOnly":          "<AllowFileUpdating        int>",
    //"endUser":             "<EndUserId                string>",
    //"returnUrl":           "<RedirectURL              string>",
    //"returnBody":          "<ResponseBodyForAppClient string>",
    //"callbackUrl":         "<RequestUrlForAppServer   string>",
    //"callbackHost":        "<RequestHostForAppServer  string>",
    //"callbackBody":        "<RequestBodyForAppServer  string>",
    //"callbackBodyType":    "<RequestBodyTypeForAppServer  string>",
    //"persistentOps":       "<persistentOpsCmds        string>",
    //"persistentNotifyUrl": "<persistentNotifyUrl      string>",
    //"persistentPipeline":  "<persistentPipeline       string>",
    //"saveKey":             "<SaveKey                  string>",
    //"fsizeMin":            "<FileSizeMin              int64>",
    //"fsizeLimit":          "<FileSizeLimit            int64>",
    //"detectMime":          "<AutoDetectMimeType       int>",
    //"mimeLimit":           "<MimeLimit                string>",
    //"fileType":           "<fileType                  int>"


    public function __construct( $accessKey,$secretKey,$bucket,$policy=null )
    {
        $this->accessKey    =   $accessKey;
        $this->secretKey    =   $secretKey;
        $this->bucket       =   $bucket;
        $this->auth         =   new Auth($this->accessKey, $this->secretKey);
        $this->policy       =   $policy;
    }
    /**
     * 描述:通过文件的临时存储路径上传     $_FILE[filename][tmp_name]
     * @param  $filePath    文件上传的临时路径
     * @return array
     */
    public function upload( $filePath )
    {
        $this->uploadMgr    =   new UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传。
        list($ret, $err) = $this->uploadMgr->putFile($this->upToken, $this->key, $filePath);
        return $this->resultFormat( $ret, $ret );
    }

    /**
     * @return
     * 描述:通过七牛云的api fetch接口上传   第三方资源抓取 从指定 URL 抓取资源，并将该资源存储到指定空间中。每次只抓取一个文件，抓取时可以指定保存空间名和最终资源名
     * @param $url  需要上传的资源url
     */
    public function uploadByFetchApi( $url ){
        $this->BucketManager = new BucketManager($this->auth);
        list($ret, $err) = $this->BucketManager->fetch($url, $this->bucket, $this->key);
        return $this->resultFormat( $ret, $ret );
    }

    /**
     * 描述:返回值通用方法
     * @param $ret
     * @param $err
     * @return array
     */
    private function resultFormat( $ret, $err ) {
        return [
            'ret'=>$ret,
            'err'=>$err
        ];
    }
}