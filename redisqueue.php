<?php

class Redisqueue
{
    public function __construct()
    {
        $this->redis = new redis();
        $this->redis->connect('127.0.0.1', 6379);
    }

    public function tolist()
    {
        //检索队列长度
        $size = $this->redis->lSize('snatchList');

        if ($size < 10) {
            $this->redis->rPush('snatchList', mt_rand(1000, 9999));
            print_r($this->redis->lrange('snatchList',0,-1));
            die('抢购成功');
        } else {
            die('已抢完');
        }
    }

    public function deal()
    {
        //检索队列长度
        $size = $this->redis->lSize('snatchList');

        if ($size) {
            $result = $this->redis->lPop('snatchList');
            print_r($this->redis->lrange('snatchList',0,-1));
            if ($result) {
                // TODO::这里执行业务逻辑代码
                die('出队成功，还剩'.$size.'个');
            }
        } else {
            die('全部处理完成');
        }
    }
}

$redisqueue = new Redisqueue();
// 入队
$redisqueue->tolist();
// 出队
$redisqueue->deal();