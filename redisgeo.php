<?php

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

//添加或修改
//$redis->geoadd("city", 117.224311, 39.111515, "天津");
//$redis->geoadd("city", 116.40378, 39.91544, "北京", 121.473913, 31.222965, "上海");

//获取
//$res = $redis->geopos("city", "天津", "北京");

//计算两个位置的距离
//$res = $redis->geodist("city", "天津", "北京", "km");

//通过当前经纬度 获取附近的列表
//$res = $redis->georadius("city", 117.224311, 39.111515, 1000, "km", ['WITHDIST','ASC']);
//$res = $redis->georadius("city", 117.224311, 39.111515, 1000, "km", ['WITHCOORD','WITHDIST','ASC','COUNT'=>1]);

//通过成员经纬度 获取成员附近的列表
//$res = $redis->georadiusbymember("city", "天津", 200, "km", ['WITHCOORD', 'WITHDIST', 'ASC']);

//print_r($res);


