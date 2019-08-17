<?php

namespace app\admin\model;

use think\Db;

class FileModel
{
    /**
     * 查询数据库获取文件ID
     * @param string $fileHash
     * @return int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getFileID(string $fileHash): int
    {
        if (empty($fileHash))
            return 0;
        $result = Db::table('epay_file_info')->where('hash', $fileHash)->limit(1)->field('id')->select();
        if (empty($result))
            return 0;
        return $result[0]['id'];
    }

    /**
     * 获取文件的绝对路径
     * @param int $fileID //文件ID
     * @param bool $isWebPath //是否获网站路径 就是web浏览器访问那种
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getFilePath(int $fileID, bool $isWebPath = false): string
    {
        if (empty($fileID))
            return '';
        $result = Db::table('epay_file_info')->where('id', $fileID)->field('path,fileType')->select();
        if (empty($result))
            return '';
        if ($isWebPath)
            $filePath = '/static/uploads/' . $result[0]['path'];
        else
            $filePath = env('root_path') . 'public/static/uploads/' . $result[0]['path'];
        return $filePath;
    }

    /**
     * 保存文本信息
     * @param string $content
     * @param string $fileType
     * @param int $uid
     * @return int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function saveString(string $content, string $fileType, int $uid = 1): int
    {
        $fileHash = hash('sha256', $content);

        $fileID = self::getFileID($fileHash);
        if ($fileID != 0)
            return $fileID;

        $filePath = substr($fileHash, 0, 2) . '/' . substr($fileHash, 2, 64) . '.' . $fileType;
        //build file path
        $fileDir = env('root_path') . 'public/static/uploads/' . substr($fileHash, 0, 2) . '/';
        //build fileDir

        if (!is_dir($fileDir))
            mkdir($fileDir, 0777, true);
        //check dir is exist
        $putResult = file_put_contents(env('root_path') . 'public/static/uploads/' . $filePath, $content, LOCK_EX);
        if ($putResult === false)
            return 0;
        //保存文件失败
        $insertResult = Db::table('epay_file_info')->insertGetId([
            'uid'        => $uid,
            'hash'       => $fileHash,
            'path'       => $filePath,
            'fileType'   => $fileType,
            'createTime' => getDateTime()
        ]);
        if (!$insertResult)
            return 0;
        return $insertResult;
    }
}