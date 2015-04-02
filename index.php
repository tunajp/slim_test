<?php
/*
 * index.php
 *
 * @author Mitsunori Inaba <m-inaba@phoenixdesign.jp>
 * Copyright(C) 2015 DesignStudioPhoenix Corporation. All Rights Reserved.
 */

require "lib/vendor/autoload.php";
require_once './config.php';

$app = new \Slim\Slim(\Config::$slim_config);

$app->get('/', function() use($app) {
    echo 'Hello';
    echo PhoenixDesign\Lib\Util::isAscii('hoge');

    $app->getLog()->debug('log'); // - level 4
    $app->getLog()->info('log');  // - level 3
    $app->getLog()->warn('log');  // - level 2
    $app->getLog()->error('log'); // - level 1
    $app->getLog()->fatal('log'); // - level 0

});
$app->get('/info', function() {
    phpinfo();
});
$app->notFound(function(){
    echo '404 not found';
});
$app->get('/myclass', function() use($app){
    //(new \Controller\Page($app))->index();
    $c = new \Controller\Page($app);
    $c->index();
});
$app->get('/longlong/:name', function() use($app) {
    myFunction($app);
});
$app->get('/json', function() use($app) {
    $result = array(
        'id' => '100',
        'name' => '名前',
    );

    $app->response->headers->set('Content-Type', 'application/json');
    echo json_encode($result);
});
$app->get('/hello/:name', function($name){
    echo 'Hello ' . $name;
});
$app->get('/test', function() use ($app) {
    $values = array(
        'page_title' => 'ページ',
        'id' => '100',
        'name' => '名前',
    );
    $values['id'] = '101';

    $app->render('test.php', array('values' => $values));
});
$app->post('/post_test', function() use($app) {
    $name = $app->request()->post('name');
    $age = $app->request()->post('age');
    $sex = '';
    if (NULL == $app->request()->post('sex')) echo '性別が選択されていません';
    else $sex = $app->request()->post('sex');
    echo 'test name->' . $name . ',age->' . $age . ',性別->' . $sex;
});

$app->run();

function myFunction($app)
{
    $name = $app->request->get('name');
    echo 'long ' . $name;
}
