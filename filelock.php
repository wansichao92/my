<?php

class FileLock
{
    /**
     * 阻塞模式（后面的进程会一直等待前面的进程执行完毕）
     */
    public function lock1()
    {
        $file = fopen(__DIR__.'/lock.txt','w+');
        if (flock($file,LOCK_EX)) {
            // TODO::这里写抢购逻辑...
            flock($file,LOCK_UN);
        }
        fclose($file);
    }

    /**
    * 非阻塞模式（只要当前文件有锁存在，那么直接返回）
    */
    public function lock2()
    {
        // 链接mysql
        $mysqli = new Mysqli('106.52.89.88', 'root', 'wansichao9187', 'wsc');
        if ($mysqli->connect_errno) die("链接错误:".$mysqli->connect_error);
        $mysqli->set_charset('utf8');

        // 设置锁
        $file = fopen(__DIR__.'/lock.txt','w+');

        // 加锁
        if (flock($file,LOCK_EX|LOCK_NB)) {
            // 开始抢购逻辑
            $sql="select name from gots where id=1";
            $rs = $mysqli->query($sql);
            $row = $rs->fetch_assoc();
            // 库存是否大于0
            if ($row['name']>0) {
                $sql = "update gots set name=name-1 where id=1"; //库存-1
                $store_rs = $mysqli->query($sql);
                if ($store_rs) {
                    echo '抢购成功';
                    flock($file,LOCK_UN); //解锁
                } else {
                    echo '错误';
                }
            } else {
                echo '已卖完';
            }
        } else {
            echo "手气不好，再抢购！";
        }

        // 关闭文件
        fclose($file);
    }
}

$fileLock = new FileLock();
// 执行非阻塞模式防止并发
$fileLock->lock2();