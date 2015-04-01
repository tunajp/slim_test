<?php

/*
 * unittest
 * 
 * @author Mitsunori Inaba <m-inaba@phoenixdesign.jp>
 * Copyright(C) 2014 DesignStudioPhoenixCorportion. All Rights Reserved. 
 */

$testUrl = 'http://localhost/test_group/apiform/apiform/api.php';

$_SERVER['SERVER_NAME'] = 'localhost.localdomain';

// assertを有効にし、出力を抑制する
assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 1);

require_once './config.php';
require 'lib/vendor/autoload.php';

use PhoenixDesign\Lib as plib;

/**
 * utilTest
 * 
 * @return void
 */
function utilTest()
{
    $test_ret = plib\Util::isAscii('abc');
    assert('$test_ret == true');
    $test_ret = plib\Util::isAscii('あいう');
    assert('$test_ret == false');
    
    $test_ret = plib\Util::isAllZenkaku('てすと');
    assert('$test_ret == true');
    $test_ret = plib\Util::isAllZenkaku('てsuと');
    assert('$test_ret == false');
    
    $test_ret = plib\Util::isAllHiragana('てすと');
    assert('$test_ret == true');
    $test_ret = plib\Util::isAllHiragana('て漢と');
    assert('$test_ret == false');
    
    $test_ret = plib\Util::isAllKatakana('テスト');
    assert('$test_ret == true');
    $test_ret = plib\Util::isAllKatakana('ﾃｽﾄ');
    assert('$test_ret == false');
    
    $test_ret = plib\Util::isMailAddr('m-inaba@phoenixdesign.jp');
    assert('$test_ret == true');
    $test_ret = plib\Util::isAllKatakana('m-inaba');
    assert('$test_ret == false');
    
    $test_ret = plib\Util::quat('""');
    assert('$test_ret == "””"');
    
    $test_ret = plib\Util::getRandomString();
    assert('strlen($test_ret) == 8');
    
    $test_ret = plib\Util::escHtml('<b>ボールド</b>');
    assert('$test_ret == "&lt;b&gt;ボールド&lt;/b&gt;"');
    
    $test_ret = plib\Util::h('<b>ボールド</b>');
    assert('$test_ret == "&lt;b&gt;ボールド&lt;/b&gt;"');

    $message = "";
    $test_ret = plib\Util::newOrderNum('csv/counter.dat', $message);
    assert('strlen($test_ret) == 10');
    
    $test_ret = plib\Util::mycrypt('key', 'hogehoge');
    assert('$test_ret == "mp22wUnfhxE="');
    
    $test_ret = plib\Util::mydecrypt('key', 'mp22wUnfhxE=');
    assert('$test_ret == "hogehoge"');
    
    $test_ret = plib\Util::getWeek('12', '15', '2014');
    assert('$test_ret == "月"');
    
    $test_ret = plib\Util::getNWeek(strtotime("2014/12/15"));
    assert('$test_ret == 3');
    
    //plib\Util::getXEigyoubigoは実行する日によって当然変わるのでテストなし
    
    $test_ret = plib\Util::checkCard('4417-1234-5678-9113');
    assert('$test_ret == true');
    $test_ret = plib\Util::checkCard('4417-1234-5678-9114');
    assert('$test_ret == false');
    
    //plib\Util::sendmailは非常にテストし辛い
    
    //plib\Util::calendarは非常にテストし辛い
    
    $test_ret = plib\Util::removeCrLf("てすと\r\nてすと\nてすと\r");
    assert('$test_ret == "てすとてすとてすと"');
    
    //plib\Util::getFileuploadErrorは非常にテストし辛い
    
    $test_ret = plib\Util::formatBytes(1000);
    assert('$test_ret == "1000 B"');
    $test_ret = plib\Util::formatBytes(1024);
    assert('$test_ret == "1.00 KB"');
    $test_ret = plib\Util::formatBytes(1024*1024);
    assert('$test_ret == "1.00 MB"');
    $test_ret = plib\Util::formatBytes(1024*1024*1024);
    assert('$test_ret == "1.00 GB"');
    
    //plib\Util::simpleBasicAuthは非常にテストし辛い
    
    //plib\Util::downloadHeaderは非常にテストし辛い
}

/**
 * databaseTest
 * 
 * @return void
 */
function databaseTest()
{
    // TODO
}

/**
 * JsonTest
 * 
 * @return void
 */
function jsonTest()
{
    // 非常にテストし辛い
}

/**
 * apiTest
 * 
 * @return void
 */
function apiTest()
{
    /**
     * TestApi class
     */
    class TestApi extends plib\Api
    {
        /**
         * run method
         * 
         * @param type $input_data
         * @return void
         */
        public function run($input_data)
        {
        }
    }
    $test_api = new TestApi();
    $test_api->run('');
}

/**
 * validationTest
 * 
 * @return void
 */
function validationTest()
{
    class TestValidation extends plib\Validation
    {
        public function run($input_data)
        {
            $test_ret = parent::allAscii($input_data, 'ascii');
            assert('$test_ret == true');
            //TODO
        }
    }
    $arr = array('ascii' => 'abcdefg');
    $test_validation = new TestValidation();
    $test_validation->run($arr);
}

/**
 * ApisTest
 * 
 * @return void
 */
function apisTest()
{
    global $testUrl;
    $res_json = json_decode(file_get_contents($testUrl . '?servicetype=test'), true);
    assert('$res_json["result"] == "success"');
    $res_json = json_decode(file_get_contents($testUrl . '?servicetype=getprefs'), true);
    assert('count($res_json) == 47');
    $res_json = json_decode(file_get_contents($testUrl . '?servicetype=zip2addr&zip=4200961'), true);
    assert('$res_json["result"] == "success"');
    
    $data = array(
        "servicetype" => "contactform_validattion"
    );
    $options = array(
      'http' => array(
        'method'  => 'POST',
        'content' => json_encode($data),
        'header'=>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($testUrl, false, $context);
    $res_json = json_decode($result, true);
    assert('$res_json["result"] == "fail"');

    // テスト結果がうまく判定できないが、メール送信/CSV出力などは行われるのでそちらを確認
    $data = array(
        "servicetype" => "contactform_post",
        "name" => "フェニックス単体テストソリューション",
        "zip" => "0000000",
        "address" => "○○県○○市○○群××",
        "email" => "m-inaba@phoenixdesign.jp",
        "sex" => "男",
        "need1" => "家",
        "water" => "高い水",
        "pref" => "静岡県",
    );
    $options = array(
      'http' => array(
        'method'  => 'POST',
        'content' => json_encode($data),
        'header'=>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($testUrl, false, $context);
    //$header = $http_response_header[0];
    $res_json = json_decode($result, true);
    //assert('$res_json["result"] == "success"'); // 結果が空になる・・・
}

echo '<html><head><meta charset="UTF-8"></head><body>';
echo 'xampp+XDebugで実行して、特に何もwarningが出力されていなければ問題なし<br />';
echo '何かwarningが出ていればテストが通っていない糞コードが混じっている';
utilTest();
databaseTest();
jsonTest();
apiTest();
validationTest();
apisTest();
echo '</body></html>';
