易支付
--------

本系统完美支持 `支付宝` `微信` `QQ钱包` 原生支付

同时可使用易中央支付系统`承接其他支付系统`


## 安装方法

直接删除网站目录下/config/install.lock文件

然后访问域名/install 即可安装

然后设置定时任务即可保证系统安全稳定运行

每月, 15日 1点30分执行  删除15日前的记录
```code
#!/bin/sh
cd /www/wwwroot/网站目录
php think deleteRecord
```

每隔3分钟执行  系统订单统计
```code
#!/bin/sh
cd  /www/wwwroot/网站目录
php think syncOrder
```

每隔1分钟执行 自动补单
```code
#!/bin/sh
cd /home
java -jar autoCallback.jar --database-port=3306 --database-host=数据库地址 --database-name=数据库名 --database-username=数据库账号 --database-password=数据库密码 --callback-count=自动补发次数
```

每 1时1分 执行 系统结算

```code
#!/bin/sh
cd  /www/wwwroot/网站目录
php think settle
```

用户控制台
> http://域名地址/user


后台地址
> http://域名地址/admin

测试支付地址
> http://域名地址/test


## 界面展示

后台界面
![后台界面](https://raw.githubusercontent.com/Fleey/epay/master/exampleImage/adminCtrl.png)

开户界面
![开户界面](https://raw.githubusercontent.com/Fleey/epay/master/exampleImage/adminUserInfo.png)

登录界面
![登录界面](https://raw.githubusercontent.com/Fleey/epay/master/exampleImage/loginMenu.png)

测试支付界面
![测试支付](https://raw.githubusercontent.com/Fleey/epay/master/exampleImage/testPay.png)

用户界面
![用户界面](https://raw.githubusercontent.com/Fleey/epay/master/exampleImage/userCtrl.png)