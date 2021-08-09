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
$gots = $redis->get("gots"); //已抢数量
$robTotal = 100; //抢购总数量
if ($gots < $robTotal) {
    $redis->watch("gots"); //监听key
    $redis->multi(); // 开启事务
    $redis->hSet("userList", "user_id_".$gots, time()); //插入抢购数据
    $redis->set("gots", $gots + 1); //抢购到+1
    $robResult = $redis->exec(); //执行事务
    if ($robResult) {
        echo "抢购成功！<br/>";
        echo "剩余数量：" . ($robTotal - ($redis->get("gots"))) . "<br/>";
        echo "用户列表：<pre>";
        var_dump($redis->hGetAll("userList"));
        // 插入数据库，测试
        $sql = "insert into gots(name) values('success')";
        $mysqli->query($sql);
    } else {
        echo "手气不好，再抢购！";
    }
} else {
    echo "已卖完";
}