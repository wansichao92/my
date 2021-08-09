<?php
//初始化对象
$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");  
$bulk = new MongoDB\Driver\BulkWrite; //增改删
$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000); //改删

// 插入数据
$bulk->insert(['name'=>'万思超', 'age'=>28, 'sex'=>'男']);
$res = $manager->executeBulkWrite('test.sites', $bulk);

//更新数据
$bulk->update(
    ['name'=>'万思超'], //条件
    ['$set'=>['name'=>'wansichao', 'age'=>29]], //修改内容
    ['multi'=>false, 'upsert'=>false] //multi更新一条或多条，upsert不存在修改记录是否插入
);
$res = $manager->executeBulkWrite('test.sites', $bulk, $writeConcern);

//删除数据
$bulk->delete(['name'=>'万思超'], ['limit'=>1]);   //limit:1删除第一条，limit:0删除所有
$res = $manager->executeBulkWrite('test.sites', $bulk, $writeConcern);

// 查询数据
$filter = ['age'=>['$gt'=>1]]; //条件
$options = [
    'projection'=>['_id'=>0], //显示字段
    'sort'=>['age'=>-1], //排序
];
$query = new MongoDB\Driver\Query($filter, $options);
$res = $manager->executeQuery('test.sites', $query);
foreach ($res as $val) {
    print_r($val);
}
