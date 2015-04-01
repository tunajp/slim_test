<?php
/*
 * Form
 *
 * @author Mitsunori Inaba <m-inaba@phoenixdesign.jp>
 * Copyright(C) 2013 DesignStudioPhoenix Corporation. All Rights Reserved.
 */

namespace PhoenixDesign\Lib;

/**
 * Form
 */
abstract class Form
{
    /**
     * Template
     */
    protected $template;

    /**
     * index method
     */
    abstract public function index(array $command);
    
    /**
     * コンストラクタ
     * Smartyオブジェクトの作成、http/httpsのリダイレクト
     * 
     * @param string protocol "http"/"https"/""ならばリダイレクトしない
     */
    public function __construct($protocol)
    {
        $pro = strtoupper($protocol);
        if ("HTTP" == $pro || "HTTPS" == $pro) {
            $this->protocol($protocol);
        }
        
        /*
         * Template Object
         */
        try {
            $this->template = new \Smarty();
        } catch (Exception $e) {
            global $SERVER_ENV;
            if ($SERVER_ENV == 'PRODUCT') {
                Error::error_display("システムエラーが発生しました");
            } else {
                Error::errorDisplay(htmlspecialchars($e->getMessage()));
            }
            exit();
        }
        $this->template->setTemplateDir('./app/templates/default');
        $this->template->setCompileDir('./app/templates_c/');
    }

    /**
     * 指定したプロトコルでなければ指定したプロトコルにリダイレクトします
     * リダイレクトする場合これ以降の処理は中断終了します
     * 
     * @global type $HTTP_ROOT_URL
     * @global type $HTTPS_ROOT_URL
     * @param string $protocol "http"/"https"
     */
    protected function protocol($protocol)
    {
        global $HTTP_ROOT_URL;
        global $HTTPS_ROOT_URL;
        
        $http_url = "http://" . $_SERVER['HTTP_HOST'];
        $https_url = "https://" . $_SERVER['HTTP_HOST'];
        
        if (isset($HTTP_ROOT_URL)) {
            $http_url = $HTTP_ROOT_URL;
        }
        if (isset($HTTPS_ROOT_URL)) {
            $https_url = $HTTPS_ROOT_URL;
        }
        
        if ("HTTP" == strtoupper($protocol)) {
            if (!empty($_SERVER['HTTPS'])) {
                //header("Location: http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
                header("Location: {$http_url}{$_SERVER['REQUEST_URI']}");
                exit;
            }
        } else if ("HTTPS" == strtoupper($protocol)) {
            if (empty($_SERVER['HTTPS'])) {
                //header("Location: https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
                header("Location: {$https_url}{$_SERVER['REQUEST_URI']}");
                exit;
            }
        }
    }
    
    /**
     * BASEタグ用のURLを取得します(例：http://example.com/application/)
     * 
     * @global \PhoenixDesign\Lib\type $HTTP_ROOT_URL
     * @global \PhoenixDesign\Lib\type $HTTPS_ROOT_URL
     * @global type $ROOT_URL
     * @return type
     */
    protected function getBaseUrl()
    {
        global $HTTP_ROOT_URL;
        global $HTTPS_ROOT_URL;
        global $ROOT_URL;
        
        $http_url = "http://" . $_SERVER['HTTP_HOST'];
        $https_url = "https://" . $_SERVER['HTTP_HOST'];
        
        if (isset($HTTP_ROOT_URL)) {
            $http_url = $HTTP_ROOT_URL;
        }
        if (isset($HTTPS_ROOT_URL)) {
            $https_url = $HTTPS_ROOT_URL;
        }
        if (empty($_SERVER['HTTPS'])) {
            return $http_url . $ROOT_URL;
        } else {
            return $https_url . $ROOT_URL;
        }
    }

    /**
     * クライアント端末がスマートフォンか(iPadはスマートフォンに含まない)
     * Smartyのテンプレート切り替え用に使用
     * http://st.benefiss.com/document.html
     * 
     * @return boolean スマートフォンならばtrue
     */
    protected function isSmartPhone()
    {
        $isSmartphone = false;
        // PCとスマートフォン端末との振り分け処理
        // スマートフォン端末用フラグの初期化（true='smartphone'/false='PC'）
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($agent, 'iPhone') !== false && strpos($agent, 'iPad') === false) {
            $isSmartphone = true;
        } elseif (strpos($agent, 'iPod') !== false) {
            $isSmartphone = true;
        } elseif (strpos($agent, 'Android') !== false) {
            $isSmartphone = true;
        } else {
            $isSmartphone = false; // PC
        }
        return $isSmartphone;
    }

    /**
     * エラー表示をして処理を中断します
     * 
     * @global string $SERVER_ENV
     * @param Exeption $e Exceptionインスタンス
     */
    protected function errordisplay($e)
    {
        $this->template->assign( 'title', "Error" );

        $log = new \Monolog\Logger('name');
        $log->pushHandler(new \Monolog\Handler\StreamHandler('./app/logs/my.log', \Monolog\Logger::WARNING));
        //$log->addWarning('Foo');
        $log->addError('ErrorMessage:' . $e->getMessage() . ' StackTrace:' . $e->getTraceAsString());

        global $SERVER_ENV;
        if ($SERVER_ENV == 'PRODUCT') {
            $this->template->assign( 'body', "システムエラーが発生しました" );
        } else {
            $this->template->assign( 'body', "ErrorMessage：<br>" . Util::h($e->getMessage()) . "<br>StackTrace<br>：" . Util::h($e->getTraceAsString()) );
        }
        $tpl = $this->template->display( 'error.tpl' );
        exit();
    }
    
    /**
     * ページネーション
     * 
     * @param int $max データの総件数
     * @param int $current_page 現在のページ
     * @param int $limit 1ページあたりの表示件数
     * @param string $base_url ベースURL(/で終わること)
     * @return string htmlタグを返します
     */
    protected function pagenation($max, $current_page, $limit, $base_url)
    {
        $offset = ($current_page-1)*$limit;
        $max_page = (int)($max/$limit +1);
        
        $pager = '<ul class="pagination">' . "\r\n";
        for ($i=0; $i<$max_page; $i++) {
            if ($current_page == 1 && $i==0) {
                $pager .= '<li class="disabled"><a href="#">&laquo;</a></li>' . "\r\n";
            } else if ($i==0) {
                $pager .= '<li><a href="' . $base_url . ($current_page-1). '">&laquo;</a></li>' . "\r\n";
            }
            if ($i+1 == $current_page) {
                //active
                $pager .= '<li class="active"><a href="' . $base_url . ($i+1). '">' . ($i+1) . '</a></li>' . "\r\n";
            } else {
                $pager .= '<li><a href=' . $base_url . ($i+1). '>' . ($i+1) . '</a></li>' . "\r\n";
            }
            if ($current_page == $max_page && ($i+1)==$max_page) {
                $pager .= '<li class="disabled"><a href="#">&raquo;</a></li>' . "\r\n";
            } else if (($i+1)==$max_page){
                $pager .= '<li><a href="' . $base_url . ($current_page+1). '">&raquo;</a></li>' . "\r\n";
            }
        }
        $pager .= "</ul>";
        return $pager;
        
    }
}
