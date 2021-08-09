<?php
/**
 * string类型 (key, value)
 * 使用场景：
 * 1.储存单值 set get / mset mget
 * 2.单值自增/自减（计数器） incr decr
 * 3.存储对象 user_id:{json} / user_id_field:value ...
 * 4.分布式锁 setnx 当key不存在时，设值并返回1，当key已经存在时，不设值并返回0
 *
 * hash类型 (key, field, value)
 * 使用场景：
 * 1.存储对象 hset hget / hmset hmget
 *
 * list类型 (双向链表结构)
 * 使用场景：
 * 1.消息队列 rpush lpop / lpush rpop
 * 2.排行榜 lrange
 * 3.最新列表 lpush lrange
 *
 * set类型 (无序集群)
 * 使用场景：
 * 1.朋友圈点赞列表 sAdd smembers sRem
 * 2.标签
 * 3.抽奖
 * 4.社交关注
 *
 * sorted set类型 (有序集群)
 * 使用场景：
 * 1.排名，排行榜 zAdd zRange
 *
 * HyperLogLog类型
 * 使用场景：
 * 1.纯计数（统计注册IP数、统计每日访问IP数、统计在线用户数等） PFADD PFCOUNT PFMERGE
 *
 * stream类型
 * 使用场景：
 * 1.消息队列
 *
 * geo类型
 * 使用场景：
 * 1.储存地理位置信息 geoadd geopos geodist georadius georadiusbymember geohash
 *
 * bitmap类型 setBit getBit bitCount
 * 使用场景：
 * 1.签到
 * 2.统计活跃用户
 */

header("content-type:text/html;charset=utf-8");

$redis = new redis();
$redis->connect('127.0.0.1', 6379);

//$cacheKey = 'bitmap';
//$redis->setBit($cacheKey, 12706, 1);
//$redis->setBit($cacheKey, 12707, 1);
//$redis->setBit($cacheKey, 12708, 1);
//$value = $redis->getBit($cacheKey,12706);
//print_r($value);

//$res = $redis->pfAdd('c1', ['aaa']);
//$res = $redis->pfAdd('c1', ['bbb']);
//$res = $redis->pfAdd('c1', ['ccc']);
//print_r($redis->pfCount('c1'));


//$redis->zAdd('yx','11','a','44','b','33','c');
//$yxlist = $redis->zRange('yx',0,-1,WITHSCORES);
//print_r($yxlist);


//$redis->sAdd('jihe','a','b','c','d','aaa','bbb','eee');
//$redis->sRem('jihe','c');
//$jihe = $redis->smembers('jihe');
//print_r($jihe);


//$redis->rpush('list', mt_rand(1000, 9999));
//$redis->lPop('list');
//print_r($redis->lrange('list',0,-1));


//$redis->hset('user_2','id',1);
//$redis->hset('user_2','name','aaa');
//$redis->hset('user_2','age',110);
//print_r($redis->hgetall('user_2'));


//$aaa = $redis->setnx('bbb','aaa');
//echo $aaa; exit;
//$redis->setnx('aaa','bbb');


//$count = $redis->incr('count');
//echo $count;

/*
$arr = [
    'id'=>1,
    'name'=>'wansichao',
    'age'=>25,
    'card'=>360103199205103810
];

$redis->set('user_1', json_encode($arr));
$user_1 = json_decode($redis->get('user_1'),true);
$user_1['name'] = '万思超';
$redis->set('user_1', json_encode($user_1));
$user_1 = json_decode($redis->get('user_1'),true);
print_r($user_1);
*/

