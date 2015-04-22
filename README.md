# slim_test
My Slim Framework test

フレームワーク疲れ。。。

##環境構築
```
npm install
c:\xampp\php\php tools\composer.phar install --dev
```

##開発環境で各種テストを実行しましょう
```
node_modules\.bin\jscs js\app\app.js
c:\xampp\php\php lib\vendor\phpunit\phpunit\phpunit
c:\xampp\php\php lib\vendor\squizlabs\php_codesniffer\scripts\phpcs --standard=psr2 -v index.php

c:\xampp\php\php -S 0.0.0.0:80
node_modules\.bin\mocha .\e2e_test\* --compilers js:mocha-traceur
```
