/*
 * E2E test
 * node_modules\.bin\mocha ./e2e_test/* --compilers js:mocha-traceur
 * jenkinsではnode bin/application.jsをやってから上を呼ぶ(nodeはジョブ終了と同時に死ぬと思われる
 *
 * @author Mitsunori Inaba <m-inaba@phoenixdesign.jp>
 * Copyright(C) 2015 DesignStudioPhoenix Corporation. All Rights Reserved.
 */

(function() {
  var os = require('os');
  var test = require("selenium-webdriver/testing");
  var assert = require("assert")

  function writeScreenshot(data, name) {
    name = name || 'ss.png';
    var screenshotPath = './tmp/';
    var fs = require('fs');
    fs.writeFileSync(screenshotPath + name, data, 'base64');
  };

  test.describe('E2E test', function() {
    var driver_chrome;
    var driver_firefox;
    var driver_phantomjs;
    var driver_ie;
    var By;
    var until;


    test.before(function(){
      this.timeout(30000);

      var webdriver = require('selenium-webdriver');
      By = require('selenium-webdriver').By;
      until = require('selenium-webdriver').until;

      if (os.platform() == 'win32') {
        driver_chrome = new webdriver.Builder()
            .forBrowser('chrome')
            .build();
        driver_firefox = new webdriver.Builder()
            .forBrowser('firefox')
            .build();
        driver_phantomjs = new webdriver.Builder()
            .forBrowser('phantomjs')
            .withCapabilities({"browserName": "phantomjs", "phantomjs.cli.args": "--config=config.json"})
            .build();
        driver_ie = new webdriver.Builder()
            .forBrowser('internet explorer')
            .build();
      } else if (os.platform() == 'linux') {
        driver_phantomjs = new webdriver.Builder()
            .forBrowser('phantomjs')
            .withCapabilities({"browserName": "phantomjs", "phantomjs.cli.args": "--config=config.json"})
            .build();
      }
    });

    test.it('top page chrome', function(){
      this.timeout(30000);

      if (os.platform() == 'win32') {
        driver_chrome.get('http://localhost');
        driver_chrome.sleep(3000);

        driver_chrome.takeScreenshot().then(function(data) {
          writeScreenshot(data, 'out1_chrome.png');
          assert.equal('', '');
        });
      }
    });
    test.it('top page firefox', function(){
      this.timeout(30000);

      if (os.platform() == 'win32') {
        driver_firefox.get('http://localhost');
        driver_firefox.sleep(3000);

        driver_firefox.takeScreenshot().then(function(data) {
          writeScreenshot(data, 'out1_firefox.png');
          assert.equal('', '');
        });
      }
    });
    test.it('top page phantomjs', function(){
      this.timeout(30000);

      driver_phantomjs.get('http://localhost');
      driver_phantomjs.sleep(3000);

      driver_phantomjs.takeScreenshot().then(function(data) {
        writeScreenshot(data, 'out1_phantomjs.png');
        assert.equal('', '');
      });
    });
    test.it('top page ie', function(){
      this.timeout(30000);

      if (os.platform() == 'win32') {
        driver_ie.get('http://localhost');
        driver_ie.sleep(3000);

        driver_ie.takeScreenshot().then(function(data) {
          writeScreenshot(data, 'out1_ie.png');
          assert.equal('', '');
        });
      }
    });

    test.it('test page chrome', function(){
      this.timeout(30000);

      if (os.platform() == 'win32') {
        driver_chrome.get('http://localhost/test/');
        driver_chrome.sleep(3000);

        driver_chrome.takeScreenshot().then(function(data) {
          writeScreenshot(data, 'out2_chrome.png');
          assert.equal('', '');
        });
      }
    });
    test.it('test page firefox', function(){
      this.timeout(30000);

      if (os.platform() == 'win32') {
        driver_firefox.get('http://localhost/test/');
        driver_firefox.sleep(3000);

        driver_firefox.takeScreenshot().then(function(data) {
          writeScreenshot(data, 'out2_firefox.png');
          assert.equal('', '');
        });
      }
    });
    test.it('test page phantomjs', function(){
      this.timeout(30000);

      driver_phantomjs.get('http://localhost/test/');
      driver_phantomjs.sleep(3000);

      driver_phantomjs.takeScreenshot().then(function(data) {
        writeScreenshot(data, 'out2_phantomjs.png');
        assert.equal('', '');
      });
    });
    test.it('test page ie', function(){
      this.timeout(30000);

      if (os.platform() == 'win32') {
        driver_ie.get('http://localhost/test/');
        driver_ie.sleep(3000);

        driver_ie.takeScreenshot().then(function(data) {
          writeScreenshot(data, 'out2_ie.png');
          assert.equal('', '');
        });
      }
    });

    test.it('test page button chrome', function(){
      this.timeout(30000);

      if (os.platform() == 'win32') {
        driver_chrome.get('http://localhost/test/').then(() => {
          driver_chrome.findElement(By.id('name')).sendKeys('お名前');
          driver_chrome.findElement(By.id('age')).sendKeys('99');
          driver_chrome.findElement(By.id('man')).click();
          driver_chrome.findElement(By.id('submit')).click();
        });
        driver_chrome.sleep(1000);

        driver_chrome.takeScreenshot().then(function(data) {
          writeScreenshot(data, 'out3_chrome.png');
          assert.equal('', '');
        });
      }
    });
    test.it('test page button firefox', function(){
      this.timeout(30000);

      if (os.platform() == 'win32') {
        driver_firefox.get('http://localhost/test/').then(() => {
          driver_firefox.findElement(By.id('name')).sendKeys('お名前');
          driver_firefox.findElement(By.id('age')).sendKeys('99');
          driver_firefox.findElement(By.id('man')).click();
          driver_firefox.findElement(By.id('submit')).click();
        });
        driver_firefox.sleep(1000);

        driver_firefox.takeScreenshot().then(function(data) {
          writeScreenshot(data, 'out3_firefox.png');
          assert.equal('', '');
        });
      }
    });
    test.it('test page button phantomjs', function(){
      this.timeout(30000);

      driver_phantomjs.get('http://localhost/test/').then(() => {
        driver_phantomjs.findElement(By.id('name')).sendKeys('お名前');
        driver_phantomjs.findElement(By.id('age')).sendKeys('99');
        driver_phantomjs.findElement(By.id('man')).click();
        driver_phantomjs.findElement(By.id('submit')).click();
      });
      driver_phantomjs.sleep(1000);

      driver_phantomjs.takeScreenshot().then(function(data) {
        writeScreenshot(data, 'out3_phantomjs.png');
        assert.equal('', '');
      });
    });
    test.it('test page button ie', function(){
      this.timeout(30000);

      if (os.platform() == 'win32') {
        driver_ie.get('http://localhost/test/').then(() => {
          driver_ie.findElement(By.id('name')).sendKeys('お名前');
          driver_ie.findElement(By.id('age')).sendKeys('99');
          driver_ie.findElement(By.id('man')).click();
          driver_ie.findElement(By.id('submit')).click();
        });
        driver_ie.sleep(1000);

        driver_ie.takeScreenshot().then(function(data) {
          writeScreenshot(data, 'out3_ie.png');
          assert.equal('', '');
        });
      }
    });

    test.after(function(){
      this.timeout(30000);
      if (os.platform() == 'win32') {
        driver_firefox.quit();
        driver_chrome.quit();
        driver_phantomjs.quit();
        driver_ie.quit();
      } else if (os.platform() == 'linux') {
        driver_phantomjs.sleep(3000);
        driver_phantomjs.quit();
      }
    });
  });

}());
