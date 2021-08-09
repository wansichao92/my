<?php

header("content-type:text/html;charset=utf-8");

// 链接redis
$redis = new redis();
$redis->connect('127.0.0.1', 6379);

// 链接mysql
$mysqli = new Mysqli('106.52.89.88', 'root', 'wansichao9187', 'wsc');
if ($mysqli->connect_errno) die("链接错误:".$mysqli->connect_error);
$mysqli->set_charset('utf8');

// 清空缓存，重新测试
if ($_GET['reset']==1) {
    $redis->del("gots");
    $redis->del("userList");
    die('redis缓存已清空，可以重新测试了！');
}

// 开始抢购逻辑
$lockKey = time(); //设置锁id
$gots = $redis->get("gots"); //已抢数量
$robTotal = 100; //抢购总数量
if ($gots < $robTotal) {
    $lock = $redis->setNX('lock', $lockKey); // 设置分布式锁，返回bool来判断是否获得了锁
    $redis->expire('lock', 2); // 设置锁的有效期
    if ($lock) {
        try {
            //todo::开启一个子线程做计划任务：每隔X秒钟判断当前锁是否存在，如果存在则重新设置锁的到期时间（给锁续命，避免线程执行时间超过锁的到期时间）
            // 插入数据库，测试
            $sql = "insert into gots(name) values('success')";
            $mysqli->query($sql);
            $redis->set("gots", $gots + 1); //抢购到+1
            // 判断锁的key一致才能解锁
            if ($lockKey==$redis->get('lock')) $redis->del('lock');
            //echo "抢购成功！<br/>";
            //echo "剩余数量：" . ($robTotal - ($redis->get("gots"))) . "<br/>";
        } catch (\Exception $e) {
            // 程序错误也要把锁解开
            if ($lockKey==$redis->get('lock')) $redis->del('lock');
        }
    } else {
        $sql = "insert into gots(name) values('fail')";
        $mysqli->query($sql);
        //echo "手气不好，再抢购！";
    }
} else {
    $sql = "insert into gots(name) values('over')";
    $mysqli->query($sql);
    //echo "已卖完";
}