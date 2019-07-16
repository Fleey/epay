<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>快速申请页面</title>
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
<div class="container" style="padding-top: 3rem;">
    <div style="margin-bottom: 2rem;">
        <h1>查询申请状态（建议两分钟查一次）</h1>
        <form method="post" action="/Wxx/api/SelectOrderStatus">
            <div class="form-group row">
                <label for="applyNumber" class="col-sm-2 col-form-label">申请单号</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="applyNumber" placeholder="申请单号">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-10">
                    <button type="submit" class="btn btn-primary">发起查询</button>
                </div>
            </div>
        </form>
    </div>
    <div style="margin-bottom: 2rem;">
        <h1>重新发起提现（提现卡无效修改后使用）</h1>
        <form method="post" action="/Wxx/api/ReAutoWithDrawByDate">
            <div class="form-group row">
                <label for="subMchID" class="col-sm-2 col-form-label">小微商户ID</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="subMchID" placeholder="小微商户ID">
                </div>
            </div>
            <div class="form-group row">
                <label for="date" class="col-sm-2 col-form-label">自动提现单提现日期</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="date" placeholder="自动提现单提现日期  YYYYMMDD  20180602">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-10">
                    <button type="submit" class="btn btn-primary">发起申请</button>
                </div>
            </div>
        </form>
    </div>
    <div style="margin-bottom: 2rem;">
        <h1>修改联系信息（小微商户ID 就是之前15开头的哪个）</h1>
        <form method="post" action="/Wxx/api/ModifyContactInfo">
            <div class="form-group row">
                <label for="subMchID" class="col-sm-2 col-form-label">小微商户ID</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="subMchID" placeholder="小微商户ID">
                </div>
            </div>
            <div class="form-group row">
                <label for="mobilePhone" class="col-sm-2 col-form-label">手机号</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="mobilePhone" placeholder="手机号 留空则不修改">
                </div>
            </div>
            <div class="form-group row">
                <label for="email" class="col-sm-2 col-form-label">邮箱</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="email" placeholder="邮箱 留空则不修改">
                </div>
            </div>
            <div class="form-group row">
                <label for="merchantName" class="col-sm-2 col-form-label">商户简称</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="merchantName" placeholder="商户简称 留空则不修改">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-10">
                    <button type="submit" class="btn btn-primary">提交修改</button>
                </div>
            </div>
        </form>
    </div>
    <div style="margin-bottom: 2rem;">
        <h1>修改结算银行卡（小微商户ID 就是之前15开头的哪个）</h1>
        <form method="post" action="/Wxx/api/ModifyArchives">
            <div class="form-group row">
                <label for="subMchID" class="col-sm-2 col-form-label">小微商户ID</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="subMchID" placeholder="小微商户ID">
                </div>
            </div>
            <div class="form-group row">
                <label for="accountNo" class="col-sm-2 col-form-label">银行卡号</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="accountNo" placeholder="银行卡号">
                </div>
            </div>
            <div class="form-group row">
                <label for="accountBank" class="col-sm-2 col-form-label">开户银行</label>
                <div class="col-sm-10">
                    <select class="custom-select my-1 mr-sm-2" name="accountBank">
                        <option selected>请选择开户银行</option>
                        <option value="工商银行">工商银行</option>
                        <option value="交通银行">交通银行</option>
                        <option value="招商银行">招商银行</option>
                        <option value="民生银行">民生银行</option>
                        <option value="中信银行">中信银行</option>
                        <option value="浦发银行">浦发银行</option>
                        <option value="兴业银行">兴业银行</option>
                        <option value="光大银行">光大银行</option>
                        <option value="广发银行">广发银行</option>
                        <option value="平安银行">平安银行</option>
                        <option value="北京银行">北京银行</option>
                        <option value="华夏银行">华夏银行</option>
                        <option value="农业银行">农业银行</option>
                        <option value="建设银行">建设银行</option>
                        <option value="邮政储蓄银行">邮政储蓄银行</option>
                        <option value="中国银行">中国银行</option>
                        <option value="宁波银行">宁波银行</option>
                        <option value="其他银行">其他银行</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="bankName" class="col-sm-2 col-form-label">开户银行全称</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="bankName" placeholder="开户银行全称（含支行）">
                </div>
            </div>
            <div class="form-group row">
                <label for="bankAddressCode" class="col-sm-2 col-form-label">开户银行省市编码</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="bankAddressCode" placeholder="开户银行省市编码">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-10">
                    <button type="submit" class="btn btn-primary">提交修改</button>
                </div>
            </div>
        </form>
    </div>
    <div>
        <h1>申请账户</h1>
        <a href="https://pay.weixin.qq.com/wiki/doc/api/xiaowei.php?chapter=19_2" target="_blank">填写教程</a>
        <form method="post" action="/Wxx/api/Apply" enctype="multipart/form-data">
            <div class="form-group row">
                <label for="idCardCopy" class="col-sm-2 col-form-label">身份证正面</label>
                <div class="col-sm-10">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="idCardCopy">
                        <label class="custom-file-label" for="idCardCopy">点击上传 身份证正面</label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="idCardNational" class="col-sm-2 col-form-label">身份证国徽面照片</label>
                <div class="col-sm-10">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="idCardNational">
                        <label class="custom-file-label" for="idCardNational">点击上传 身份证国徽面照片</label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="idCardName" class="col-sm-2 col-form-label">身份证姓名</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="idCardName" placeholder="身份证姓名">
                </div>
            </div>
            <div class="form-group row">
                <label for="idCardNumber" class="col-sm-2 col-form-label">身份证号码</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="idCardNumber" placeholder="身份证号码">
                </div>
            </div>
            <div class="form-group row">
                <label for="idCardValidTime" class="col-sm-2 col-form-label">身份证有效期限</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="idCardValidTime"
                           placeholder='身份证有效期限  例子 ["1970-01-01","长期"]'>
                </div>
            </div>
            <div class="form-group row">
                <label for="accountName" class="col-sm-2 col-form-label">开户名称</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="accountName" placeholder='开户名称'>
                </div>
            </div>
            <div class="form-group row">
                <label for="accountBank" class="col-sm-2 col-form-label">开户银行</label>
                <div class="col-sm-10">
                    <select class="custom-select my-1 mr-sm-2" name="accountBank">
                        <option selected>请选择开户银行</option>
                        <option value="工商银行">工商银行</option>
                        <option value="交通银行">交通银行</option>
                        <option value="招商银行">招商银行</option>
                        <option value="民生银行">民生银行</option>
                        <option value="中信银行">中信银行</option>
                        <option value="浦发银行">浦发银行</option>
                        <option value="兴业银行">兴业银行</option>
                        <option value="光大银行">光大银行</option>
                        <option value="广发银行">广发银行</option>
                        <option value="平安银行">平安银行</option>
                        <option value="北京银行">北京银行</option>
                        <option value="华夏银行">华夏银行</option>
                        <option value="农业银行">农业银行</option>
                        <option value="建设银行">建设银行</option>
                        <option value="邮政储蓄银行">邮政储蓄银行</option>
                        <option value="中国银行">中国银行</option>
                        <option value="宁波银行">宁波银行</option>
                        <option value="其他银行">其他银行</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="bankAddressCode" class="col-sm-2 col-form-label">开户银行省市编码</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="bankAddressCode" placeholder='开户银行省市编码'>
                </div>
            </div>
            <div class="form-group row">
                <label for="accountNumber" class="col-sm-2 col-form-label">银行账号</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="accountNumber" placeholder='银行账号'>
                </div>
            </div>
            <div class="form-group row">
                <label for="storeName" class="col-sm-2 col-form-label">门店名称</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="storeName" placeholder='门店名称'>
                </div>
            </div>
            <div class="form-group row">
                <label for="merchantShortName" class="col-sm-2 col-form-label">商户简称</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="merchantShortName" placeholder='商户简称'>
                </div>
            </div>
            <div class="form-group row">
                <label for="servicePhone" class="col-sm-2 col-form-label">客服电话</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="servicePhone" placeholder='客服电话'>
                </div>
            </div>
            <div class="form-group row">
                <label for="productDesc" class="col-sm-2 col-form-label">服务描述</label>
                <div class="col-sm-10">
                    <select class="custom-select my-1 mr-sm-2" name="productDesc">
                        <option selected>请选择对应 售卖商品/提供服务描述</option>
                        <option value="其他">其他</option>
                        <option value="交通出行">交通出行</option>
                        <option value="休闲娱乐">休闲娱乐</option>
                        <option value="居民生活服务">居民生活服务</option>
                        <option value="线下零售">线下零售</option>
                        <option value="餐饮">餐饮</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="rate" class="col-sm-2 col-form-label">费率</label>
                <div class="col-sm-10">
                    <select class="custom-select my-1 mr-sm-2" name="rate">
                        <option selected>请选择对应费率</option>
                        <option value="0.38%">0.38%</option>
                        <option value="0.39%">0.39%</option>
                        <option value="0.4%">0.4%</option>
                        <option value="0.45%">0.45%</option>
                        <option value="0.48%">0.48%</option>
                        <option value="0.49%">0.49%</option>
                        <option value="0.5%">0.5%</option>
                        <option value="0.55%">0.55%</option>
                        <option value="0.58%">0.58%</option>
                        <option value="0.59%">0.59%</option>
                        <option value="0.6%">0.6%</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="contact" class="col-sm-2 col-form-label">联系人姓名</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="contact" placeholder='联系人姓名'>
                </div>
            </div>
            <div class="form-group row">
                <label for="contactPhone" class="col-sm-2 col-form-label">手机号码</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="contactPhone" placeholder='手机号码'>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-10">
                    <button type="submit" class="btn btn-primary">发起申请</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
<script src="https://cdn.bootcss.com/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
<script src="https://cdn.bootcss.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
</body>
</html>