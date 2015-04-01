<?php
/*
 * BaseDatabase
 * 
 * @author Mitsunori Inaba <m-inaba@phoenixdesign.jp>
 * Copyright(C) 2013 DesignStudioPhoenix Corporation. All Rights Reserved.
 */

namespace PhoenixDesign\Lib;

/**
 * BaseDatabase
 * 
 * @author Mitsunori Inaba <m-inaba@phoenixdesign.jp>
 */
interface BaseDatabase
{
    /**
     * インスタンス取得
     */
    public static function getInstance();
    /**
     * このインスタンスの複製を許可しないようにする
     */
    public function __clone();
    
    /**
     *  条件を指定せずにテーブルのデータ数を取得します
     */
    public static function getAllCount($table_name);
}