<?php

namespace app\admin\controller;

use think\App;
use think\Controller;
use think\Db;

class AD extends Controller
{
    public function __construct(App $app = null)
    {
        parent::__construct($app);
        if ($this->request->action() != 'redirects') {
            $username = session('username', '', 'admin');
            if (empty($username))
                exit(json_encode(['status' => 0, 'msg' => '您需要登录后才能操作']));
        }
    }

    public function Redirects(int $id)
    {
        $selectResult = Db::table('epay_ad_content')->cache(60)->where('id', $id)->limit(1)->select();
        if (empty($selectResult))
            return redirect('/');
        Db::table('epay_ad_content')->where('id',$id)->limit(1)->inc('visitsCount',1)->update();
        return redirect($selectResult[0]['hrefUrl']);
    }

    public function postAdd()
    {
        $title  = input('post.title/s');
        $href   = input('post.href/s');
        $imgUrl = input('post.imgUrl/s');
        $status = input('post.status/d');

        if (empty($title))
            return json(['status' => 0, 'msg' => '标题不能为空']);
        if (mb_strlen($title) > 128)
            return json(['status' => 0, 'msg' => '标题长度不能超过128个字符串']);
        if (empty($href))
            return json(['status' => 0, 'msg' => '外部转跳链接不能为空']);
        if (empty($imgUrl))
            return json(['status' => 0, 'msg' => '图片链接不能为空']);

        $insertResult = Db::table('epay_ad_content')->insertGetId([
            'title'       => $title,
            'hrefUrl'     => $href,
            'imgUrl'      => $imgUrl,
            'status'      => $status,
            'visitsCount' => 0,
            'createTime'  => getDateTime()
        ]);
        if (!$insertResult)
            return json(['status' => 0, 'msg' => '创建广告失败,数据库异常']);
        return json(['status' => 1, 'msg' => '创建广告成功']);
    }

    public function postUpdate()
    {
        $id     = input('post.id/d');
        $title  = input('post.title/s');
        $href   = input('post.href/s');
        $status = input('post.status/d');
        $imgUrl = input('post.imgUrl/s');

        if (empty($id))
            return json(['status' => 0, 'msg' => '广告ID不能为空']);
        if (empty($title))
            return json(['status' => 0, 'msg' => '标题不能为空']);
        if (mb_strlen($title) > 128)
            return json(['status' => 0, 'msg' => '标题长度不能超过128个字符串']);
        if (empty($href))
            return json(['status' => 0, 'msg' => '外部转跳链接不能为空']);
        if (empty($imgUrl))
            return json(['status' => 0, 'msg' => '图片链接不能为空']);

        $updateResult = Db::table('epay_ad_content')->where('id', $id)->limit(1)->update([
            'title'      => $title,
            'hrefUrl'    => $href,
            'imgUrl'     => $imgUrl,
            'status'     => $status,
            'createTime' => getDateTime()
        ]);

        return json(['status' => $updateResult ? 1 : 0, 'msg' => '更新' . ($updateResult ? '成功' : '失败')]);
    }

    public function postDelete()
    {
        $id = input('post.id/d');
        if (empty($id))
            return json(['status' => 0, 'msg' => '广告ID不能为空']);

        $deleteResult = Db::table('epay_ad_content')->where('id', $id)->limit(1)->delete();

        if (!$deleteResult)
            return json(['status' => 0, 'msg' => '删除失败']);
        return json(['status' => 1, 'msg' => '删除成功']);
    }

    public function getInfo()
    {
        $id = input('get.id/d');
        if (empty($id))
            return json(['status' => 0, 'msg' => '广告ID不能为空']);

        $selectResult = Db::table('epay_ad_content')->field('title,hrefUrl,imgUrl,status')->where('id', $id)->limit(1)->select();
        if (empty($selectResult))
            return json(['status' => 0, 'msg' => '查询失败，数据不存在']);
        return json(['status' => 1, 'msg' => '查询成功', 'data' => $selectResult[0]]);
    }
}