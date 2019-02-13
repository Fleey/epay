<?php
namespace app\admin\behavior;

use think\Controller;

class GroupCheck extends Controller
{
    public function _initialize()
    {
        $loginName = session('loginName', '', 'admin');
        $aid       = session('aid', '', 'admin');
        $isAjax    = request()->isAjax();

        if (is_null($loginName) || is_null($aid)) {
            $isLogin = true;
        } else {
            $isLogin = false;
        }
        if ($isLogin) {
            if ($isAjax) {
                json(['status' => 0, 'msg' => '验证已经过期,请重新登录再试'])->send();
            } else {
                redirect(url('/Admin/Login', '', false))->send();
            }
            exit();
        }
    }
}