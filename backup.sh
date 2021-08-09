#!/bin/bash
#filename:backup.sh
#author:wansichao
#date:2021-01-22
#others:用于备份文件和数据库，每天凌晨2点执行定时任务 0 2 * * * /home/backup/backup.sh

sqlPath="/home/backup/sql/"
erpPath="/home/backup/erp/"
sqlFileName="sqlbackup"
erpFileName="erpbackup"
name=`date -d -0day +%Y%m%d_%H%M%S`
pwd="wansichao9187"

#判断文件夹是否存在  
if [ ! -d "$sqlPath" ]; then  
	mkdir "$sqlPath"
fi

if [ ! -d "$erpPath" ]; then  
	mkdir "$erpPath"
fi

#sql备份
/bin/nice -n 19 /usr/bin/mysqldump -uroot -p$pwd wsc > $sqlPath$sqlFileName-$name.sql
/bin/nice -n 19 tar -czPf $sqlPath$sqlFileName-$name.sql.tar.gz $sqlPath$sqlFileName-$name.sql
/bin/nice -n 19 tar -czPf $erpPath$sqlFileName-$name-'file'.tar.gz /usr/local/mysql/var/wsc
    
#erp备份
/bin/nice -n 19 tar -czPf $erpPath$erpFileName-$name.tar.gz --exclude=web_wansichao/img/* /home/wwwroot/default/web_wansichao

#删除过期备份
find $sqlPath -mtime +3 -name "*.sql.tar.gz" -exec rm -rf {} \;
find $sqlPath -name "*.sql" -exec rm -rf {} \;
find $erpPath -mtime +1 -name "*.tar.gz" -exec rm -rf {} \;