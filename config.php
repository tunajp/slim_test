<?php

/*
 * config file
 *
 * @author Mitsunori Inaba <m-inaba@phoenixdesign.jp>
 * Copyright(C) 2013 DesignStudioPhoenix Corporation. All Rights Reserved.
 */

$PHOENIX_DESIGN_FRAMEWORK_VER = 3;

date_default_timezone_set('Asia/Tokyo');

class Config
{
    public static $USE_SMTP = false;
    // SMTPメールを使用する場合のサーバー情報 -start
    public static $SMTP_MAIL_SERVER_HOST = "";
    public static $SMTP_MAIL_SERVER_PORT = 25;
    public static $SMTP_MAIL_USERNAME = "";
    public static $SMTP_MAIL_PASSWORD = "";
    // SMTPメールを使用する場合のサーバー情報 -end
    public static $SERVER_ENV;
    public static $DBHOST;
    public static $DBUSER;
    public static $DBPASSWORD;
    public static $DBNAME;
    public static $DBENCODING;
    
    public static $INPUT_ENCODING = 'UTF-8';
}

class MailConfig
{
    public static $admin_mail_to = array("m-inaba@phoenixdesign.jp"=>"管理者");
    public static $admin_mail_cc = array();
    public static $admin_mail_bcc = array();
    public static $admin_mail_from_addr = "admin@example.com";
    public static $admin_mail_from_name = "○○運営委員会ホスティングサービス";
    public static $admin_mail_title = "タイトル";
    
    public static $customer_mail_from_addr = "admin@example.com";
    public static $customer_mail_from_name = "○○運営委員会";
    public static $customer_mail_title = "タイトル";
}

if ($_SERVER['SERVER_NAME']=='www.hoge.exsample' ){ // 本番環境
    error_reporting(0); // 全てのエラー出力をオフにする

    Config::$SERVER_ENV = 'PRODUCT';

    Config::$DBHOST	 = 'localhost';
    Config::$DBUSER	 = 'root';
    Config::$DBPASSWORD = '';
    Config::$DBNAME	 = 'admmail';
    Config::$DBENCODING = 'UTF-8';
} else { // 開発環境
    error_reporting(E_ALL); // 全ての PHP エラーを表示する
    ini_set('scream.enabled', true); // エラー制御演算子 を無効化してすべてのエラーを報告させるようにする
    
    Config::$SERVER_ENV = 'DEVELOP';

    Config::$DBHOST	 = 'localhost';
    Config::$DBUSER	 = 'root';
    Config::$DBPASSWORD = '';
    Config::$DBNAME	 = 'admmail';
    Config::$DBENCODING = 'UTF-8';
}
