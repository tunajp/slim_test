<?php
/*
 * test.php
 *
 * php composer.phar install --dev
 * php lib/vendor/phpunit/phpunit/phpunit
 *
 * @author Mitsunori Inaba <m-inaba@phoenixdesign.jp>
 * Copyright(C) 2015 DesignStudioPhoenix Corporation. All Rights Reserved.
 */

require "lib/vendor/autoload.php";

use PhoenixDesign\Lib as plib;
use Slim\Environment;

class MyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Util test
     *
     * @test
     */
    public function Util()
    {
        $test_ret = plib\Util::isAscii('abcあ');
        $this->assertEquals(true, $test_ret);

        $test_ret = plib\Util::isAscii('あいう');
        $this->assertEquals(false, $test_ret);
    
        $test_ret = plib\Util::isAllZenkaku('てすと');
        $this->assertEquals(true, $test_ret);
        $test_ret = plib\Util::isAllZenkaku('てsuと');
        $this->assertEquals(false, $test_ret);
        
        $test_ret = plib\Util::isAllHiragana('てすと');
        $this->assertEquals(true, $test_ret);
        $test_ret = plib\Util::isAllHiragana('て漢と');
        $this->assertEquals(false, $test_ret);
        
        $test_ret = plib\Util::isAllKatakana('テスト');
        $this->assertEquals(true, $test_ret);
        $test_ret = plib\Util::isAllKatakana('ﾃｽﾄ');
        $this->assertEquals(false, $test_ret);
        
        $test_ret = plib\Util::isMailAddr('m-inaba@phoenixdesign.jp');
        $this->assertEquals(true, $test_ret);
        $test_ret = plib\Util::isAllKatakana('m-inaba');
        $this->assertEquals(false, $test_ret);
        
        $test_ret = plib\Util::quat('""');
        $this->assertEquals("””", $test_ret);
        
        $test_ret = plib\Util::getRandomString();
        $this->assertEquals(8, strlen($test_ret));
        
        $test_ret = plib\Util::escHtml('<b>ボールド</b>');
        $this->assertEquals("&lt;b&gt;ボールド&lt;/b&gt;", $test_ret);
        
        $test_ret = plib\Util::h('<b>ボールド</b>');
        $this->assertEquals("&lt;b&gt;ボールド&lt;/b&gt;", $test_ret);

        $message = "";
        $test_ret = plib\Util::newOrderNum('csv/counter.dat', $message);
        $this->assertEquals(10, strlen($test_ret));
        
        $test_ret = plib\Util::mycrypt('key', 'hogehoge');
        $this->assertEquals("mp22wUnfhxE=", $test_ret);
        
        $test_ret = plib\Util::mydecrypt('key', 'mp22wUnfhxE=');
        $this->assertEquals("hogehoge", $test_ret);
        
        $test_ret = plib\Util::getWeek('12', '15', '2014');
        $this->assertEquals("月", $test_ret);
        
        $test_ret = plib\Util::getNWeek(strtotime("2014/12/15"));
        $this->assertEquals(3, $test_ret);
        
        //plib\Util::getXEigyoubigoは実行する日によって当然変わるのでテストなし
        
        $test_ret = plib\Util::checkCard('4417-1234-5678-9113');
        $this->assertEquals(true, $test_ret);
        $test_ret = plib\Util::checkCard('4417-1234-5678-9114');
        $this->assertEquals(false, $test_ret);
        
        //plib\Util::sendmailは非常にテストし辛い
        
        //plib\Util::calendarは非常にテストし辛い
        
        $test_ret = plib\Util::removeCrLf("てすと\r\nてすと\nてすと\r");
        $this->assertEquals("てすとてすとてすと", $test_ret);
        
        //plib\Util::getFileuploadErrorは非常にテストし辛い
        
        $test_ret = plib\Util::formatBytes(1000);
        $this->assertEquals("1000 B", $test_ret);
        $test_ret = plib\Util::formatBytes(1024);
        $this->assertEquals("1.00 KB", $test_ret);
        $test_ret = plib\Util::formatBytes(1024*1024);
        $this->assertEquals("1.00 MB", $test_ret);
        $test_ret = plib\Util::formatBytes(1024*1024*1024);
        $this->assertEquals("1.00 GB", $test_ret);
        
        //plib\Util::simpleBasicAuthは非常にテストし辛い
        
        //plib\Util::downloadHeaderは非常にテストし辛い
    }

    /**
     * Database test
     *
     * @test
     */
    public function Database()
    {
        // TODO
    }

    /**
     * Json test
     *
     * @test
     */
    public function Json()
    {
        // TODO
    }

    public function request($method, $path, $options = array())
    {
        // Capture STDOUT
        ob_start();

        // Prepare a mock environment
        Environment::mock(array_merge(array(
            'REQUEST_METHOD' => $method,
            'PATH_INFO' => $path,
            'SERVER_NAME' => 'slim-test.dev',
        ), $options));

        $app = new \Slim\Slim();
        $this->app = $app;
        $this->request = $app->request();
        $this->response = $app->response();

        // Return STDOUT
        return ob_get_clean();
    }

    public function get($path, $options = array())
    {
        $this->request('GET', $path, $options);
    }

    public function testPages()
    {
        $this->get('/');
        $this->assertEquals('200', $this->response->status());
        $this->get('/info/');
        $this->assertEquals('200', $this->response->status());
        $this->get('/myclass/');
        $this->assertEquals('200', $this->response->status());
        $this->get('/longlong/hello');
        $this->assertEquals('200', $this->response->status());
    }
}

