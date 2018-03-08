<?php
namespace su\file;

//文件处理的接口类
interface File
{
    /**
     * 描述:上传方法
     * @param $filePath     文件上传的临时路径
     * @return mixed
     */
    public function upload($filePath);
}