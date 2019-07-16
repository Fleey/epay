<?php

namespace app\admin\controller;

use app\admin\model\FileModel;
use think\App;
use think\Controller;
use think\Db;

class File extends Controller
{
    public function __construct(App $app = null)
    {
        parent::__construct($app);

        $isAjax   = $this->request->isAjax();
        $username = session('username', '', 'admin');

        if (empty($username)) {
            if ($isAjax)
                json(['status' => 0, 'msg' => '您需要登录后才能操作'])->send();
            else
                redirect(url('/Login', '', false))->send();
            exit();
        }
    }

    /**
     * hash获取文件ID
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getFileID()
    {
        $fileHash = input('get.hash');
        if (empty($fileHash))
            return json(['status' => 0, 'msg' => '文件不存在']);
        $result = FileModel::getFileID($fileHash);
        if ($result == 0)
            return json(['status' => 0, 'msg' => '文件不存在']);
        return json(['status' => 1, 'fileID' => $result]);
    }

    /**
     * 上传文件批量
     * @return \think\response\Json
     */
    public function postUploadFile()
    {
        $file       = $this->request->file('file');
        $fileSuffix = 'jpg,png,gif';

        $fileInfo = $file->validate(['size' => 1024 * 1024 * 3, 'ext' => $fileSuffix])->rule('sha256')->move(env('ROOT_PATH') . 'public/static/uploads');
        if (!$fileInfo)
            return json(['status' => 0, 'msg' => '上传文件失败 原因 => ' . $fileInfo->getError()]);

        $filePath      = $fileInfo->getSaveName();
        $fileExtension = $fileInfo->getExtension();
        $suffix        = explode('.', $fileExtension);
        $suffix        = $suffix[count($suffix) - 1];
        $hash          = $fileInfo->hash('sha256');
        $fileID        = Db::table('epay_file_info')->insertGetId([
            'uid'        => 1,
            'hash'       => $hash,
            'path'       => $filePath,
            'fileType'   => $suffix,
            'createTime' => getDateTime()
        ]);
        if (empty($fileID))
            return json(['status' => 0, 'msg' => '保存文件失败,请联系管理员处理']);
        return json(['status' => 1, 'fileID' => $fileID]);
    }

    /**
     * 利用文件ID获取文件路径
     * @param $fileID
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getFilePath($fileID = 0)
    {
        if (empty($fileID))
            return json(['status' => 0, 'msg' => '文件ID不存在']);
        $result = Db::table('epay_file_info')->where('id', $fileID)->field('path')->limit(1)->select();
        if (empty($result))
            return json(['status' => 0, 'msg' => '文件ID不存在']);
        return json(['status' => 1, 'path' => $result[0]['path']]);

    }
}