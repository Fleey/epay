安装方法

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