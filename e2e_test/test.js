/*
 * E2E test
 * node_modules\.bin\mocha ./e2e_test/* --compilers js:mocha-traceur
 * jenkinsではnode bin/application.jsをやってから上を呼ぶ(nodeはジョブ終了と同時に死ぬと思われる
 *
 * @author Mitsunori Inaba <m-inaba@phoenixdesign.jp>
 * Copyright(C) 2015 DesignStudioPhoenix Corporation. All Rights Reserved.
 */

(function() {
  var test = require("selenium-webdriver/testing");
  var assert = require("assert")

  function writeScreenshot(data, name) {
    name = name || 'ss.png';
    var screenshotPath = './tmp/';
    var fs = require('fs');
    fs.writeFileSync(screenshotPath + name, data, 'base64');
  };

  test.describe('E2E test', function() {
    var driver;
    var By;
    var until;


    test.before(function(){
      this.timeout(15000);

      var webdriver = require('selenium-webdriver');
      By = require('selenium-webdriver').By;
      until = require('selenium-webdriver').until;
      
      driver = new webdriver.Builder()
          //.forBrowser('firefox')
          //.forBrowser('chrome')
          //.forBrowser('phantomjs')
          .withCapabilities({"browserName": "phantomjs", "phantomjs.cli.args": "--config=config.json"})
          .build();
    });

    test.it('top page', function(){
      this.timeout(15000);

      driver.get('http://localhost');
      driver.sleep(3000);

      driver.takeScreenshot().then(function(data) {
        writeScreenshot(data, 'out1.png');
        assert.equal('', '');
      });

    });

    test.it('test page', function(){
      this.timeout(15000);

      driver.get('http://localhost/test/');
      driver.sleep(3000);

      driver.takeScreenshot().then(function(data) {
        writeScreenshot(data, 'out2.png');
        assert.equal('', '');
      });

    });

    test.it('test page button', function(){
      this.timeout(15000);

      driver.get('http://localhost/test/').then(() => {
        driver.findElement(By.id('name')).sendKeys('お名前');
        driver.findElement(By.id('age')).sendKeys('99');
        driver.findElement(By.id('man')).click();
        driver.findElement(By.id('submit')).click();
      });
      driver.sleep(1000);

      driver.takeScreenshot().then(function(data) {
        writeScreenshot(data, 'out3.png');
        assert.equal('', '');
      });

    });

    test.after(function(){
      this.timeout(15000);
      driver.quit();
    });
  });
  
}());
