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

每隔3分钟执行  系统订单统计与补单
```code
#!/bin/sh
cd  /www/wwwroot/网站目录
php think syncOrder
```

每天, 0点1分 执行 系统凌晨结算

```code
#!/bin/sh
cd  /www/wwwroot/网站目录
php think settle
```