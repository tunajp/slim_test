<?php
/*
 * ユーティリティ関数群
 *
 * @author Mitsunori Inaba <m-inaba@phoenixdesign.jp>
 * Copyright(C) 2013 DesignStudioPhoenix Corporation. All Rights Reserved.
 */

namespace PhoenixDesign\Lib;

class Util
{
    /**
     * ASCIIか.
     * @param string $str チェックしたい文字列
     * @return boolean trueなら全部ASCII falseならASCII以外が含まれている
     */
    public static function isAscii( $str )
    {
        if ( 0 == strlen( $str ) ) {
            return true;
        } else if ( preg_match( "/^[\x01-\x7E]+$/", $str ) ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 全ての文字が全角で構成されているか
     * @param string $str
     * @return boolean
     */
    public static function isAllZenkaku( $str )
    {
        if (!preg_match("/(?:\xEF\xBD[\xA1-\xBF]|\xEF\xBE[\x80-\x9F])|[\x20-\x7E]/", $str)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 全ての文字がひらがなで構成されているか
     * @param string $str
     * @return boolean
     */
    public static function isAllHiragana( $str )
    {
        if (preg_match("/^[ぁ-ん]+$/u", $str)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 全ての文字がカタカナで構成されているか
     * @param string $str
     * @return boolean
     */
    public static function isAllKatakana( $str )
    {
        if (preg_match("/^[ァ-ヾ]+$/u", $str)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * メールアドレスチェック(子飼弾版).
     * @param string $str チェックしたい文字列
     * @return boolean メールアドレスならtrue
     */
    public static function isMailAddr( $str )
    {
        if ( preg_match( '/^(?:(?:(?:(?:[a-zA-Z0-9_!#\$\%&\'*+\/=?\^`{}~|\-]+)(?:\.(?:[a-zA-Z0-9_!#\$\%&\'*+\/=?\^`{}~|\-]+))*)|(?:"(?:\\[^\r\n]|[^\\"])*")))\@(?:(?:(?:(?:[a-zA-Z0-9_!#\$\%&\'*+\/=?\^`{}~|\-]+)(?:\.(?:[a-zA-Z0-9_!#\$\%&\'*+\/=?\^`{}~|\-]+))*)|(?:\[(?:\\\S|[\x21-\x5a\x5e-\x7e])*\])))$/', $str ) ) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * ダブルクォーテーション.
     * @param string $string 文字列
     * @return 
     */
    public static function quat( $string )
    {
        $string = str_replace( array( '"' ), '”', $string );
        return $string;
    }

    /**
     * ランダムな文字列を生成する.
     * @param int $length_required 必要な文字列長。省略すると8文字
     * @return ランダムな文字列
     */
    public static function getRandomString( $length_required = 8 )
    {
        $character_list = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_';
        mt_srand();
        $result = '';
        for ($i = 0; $i < $length_required; $i++) {
            $result .= $character_list[(mt_rand( 0, strlen( $character_list ) - 1 ) )];
        }
        return $result;
    }

    /**
     * WordPressのesc_html互換関数
     * @param string $str 文字列
     * @return string サニタイズ後の文字列
     */
    public static function escHtml( $str )
    {
        if (0 != strlen($str)) {
            return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
        } else {
            return $str;
        }
    }
    
    /**
     * サニタイズ
     * @param string $str
     * @return string サニタイズ後の文字列
     */
    public static function h($str)
    {
        if (0 != strlen($str)) {
            return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
        } else {
            return $str;
        }
    }

    /**
     * お問合せ番号の生成
     * @param boolean $error true時にはエラーがあります
     * @param string $result エラー時にはエラー文言がはいります
     * @return お問合わせ番号 エラー時には-1を返します
     */
    public static function newOrderNum($logfile, $result)
    {
        //@chmod($logfile, 0666);
        $nowDate = date("ymd");
        $fp = @fopen( $logfile, "r+" ); // ファイル開く
        if (!$fp) {
            $result="お問合せ番号ファイルをオープンできませんでした。";
            return -1;
        }
        // 排他ロックをかける
        if (!@flock($fp, LOCK_EX)) {
            $result="お問合せ番号ファイルをロックできませんでした。";
            return -1;
        }
        $data = fgets($fp, 128); // 読み取り
        list($count, $lastDate) = explode("<>", $data);
        if ($nowDate != $lastDate) {
            $count = 0;
        }
        $count++; // カウントアップ
        $dat = "$count<>$nowDate<>"; 
        rewind( $fp ); // ファイルポインタを先頭に戻す
        fputs( $fp, $dat ); // 値書き込み
        fclose( $fp ); // ファイル閉じる
        $num = $nowDate . sprintf('%04d', $count);
        return $num;
    }

    /**
     * 復号化可能な暗号化を行う
     * @param string $key
     * @param string $input
     * @return string
     */
    public static function mycrypt($key, $input)
    {
        $td = mcrypt_module_open('tripledes', '', 'ecb', '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $encrypted_data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        return base64_encode($encrypted_data);
    }

    /**
     * 復号化を行う
     * @param string $key
     * @param string $input
     */
    public static function mydecrypt($key, $input)
    {
        $input = base64_decode($input);

        $td = mcrypt_module_open('tripledes', '', 'ecb', '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $deencrypted_data = mdecrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        return $deencrypted_data;
    }

    /**
     * 曜日を取得する
     * @param string $month
     * @param string $day
     * @param string $year
     * @return string 日本語の日から月を返す
     */
    public static function getWeek($month, $day, $year)
    {
        $week = array("日", "月", "火", "水", "木", "金", "土");
        $time = mktime(0, 0, 0, $month, $day, $year);
        $w = date("w", $time);
        return $week[$w];
    }
    
    /**
     * 指定された日が月の第何週目にあたるかを取得します
     * echo "第" . Util::getNWeek(strtotime("2013-06-13")) . "週目";
     * @param time $it_time
     * @return int
     */
    public static function getNWeek($it_time)
    {
        $saturday = 6;
        $week_day = 7;
        $w = intval(date('w', $it_time));
        $d = intval(date('d', $it_time));
        if ($w != $saturday) {
            $w = ($saturday - $w) + $d;
        } else {
            // 土曜日の場合
            $w = $d;
        }
        return ceil($w/$week_day);                
    }

    /**
     * X営業日後を求めます
     * $d = getFourEigyoubigo();
     * echo date('Y-m-d', $d);
     * 
     * @return type
     */
    public static function getXEigyoubigo($x_eigyoubi)
    {
        $holiday = array(
        '2007-01-01',//=>'元日',
        '2007-01-08',//=>'成人の日',
        '2007-02-11',//=>'建国記念の日',
        '2007-02-12',//=>'振替休日',
        '2007-03-21',//=>'春分の日',
        '2007-04-29',//=>'昭和の日',
        '2007-04-30',//=>'振替休日',
        '2007-05-03',//=>'憲法記念日',
        '2007-05-04',//=>'みどりの日',
        '2007-05-05',//=>'こどもの日',
        '2007-07-16',//=>'海の日',
        '2007-09-17',//=>'敬老の日',
        '2007-09-23',//=>'秋分の日',
        '2007-09-24',//=>'振替休日',
        '2007-10-08',//=>'体育の日',
        '2007-11-03',//=>'文化の日',
        '2007-11-23',//=>'勤労感謝の日',
        '2007-12-23',//=>'天皇誕生日',
        '2007-12-24',//=>'振替休日',

        '2008-01-01',//=>'元日',
        '2008-01-14',//=>'成人の日',
        '2008-02-11',//=>'建国記念の日',
        '2008-03-20',//=>'春分の日',
        '2008-04-29',//=>'昭和の日',
        '2008-05-03',//=>'憲法記念日',
        '2008-05-04',//=>'みどりの日',
        '2008-05-05',//=>'こどもの日',
        '2008-05-06',//=>'振替休日',
        '2008-07-21',//=>'海の日',
        '2008-09-15',//=>'敬老の日',
        '2008-09-23',//=>'秋分の日',
        '2008-10-13',//=>'体育の日',
        '2008-11-03',//=>'文化の日',
        '2008-11-23',//=>'勤労感謝の日',
        '2008-11-24',//=>'振替休日',
        '2008-12-23',//=>'天皇誕生日',

        '2009-01-01',//=>'元日',
        '2009-01-12',//=>'成人の日',
        '2009-02-11',//=>'建国記念の日',
        '2009-03-20',//=>'春分の日',
        '2009-04-29',//=>'昭和の日',
        '2009-05-03',//=>'憲法記念日',
        '2009-05-04',//=>'みどりの日',
        '2009-05-05',//=>'こどもの日',
        '2009-05-06',//=>'振替休日',
        '2009-07-20',//=>'海の日',
        '2009-09-21',//=>'敬老の日',
        '2009-09-22',//=>'国民の休日',
        '2009-09-23',//=>'秋分の日',
        '2009-10-12',//=>'体育の日',
        '2009-11-03',//=>'文化の日',
        '2009-11-23',//=>'勤労感謝の日',
        '2009-12-23',//=>'天皇誕生日',

        '2010-01-01',//=>'元日',
        '2010-01-11',//=>'成人の日',
        '2010-02-11',//=>'建国記念の日',
        '2010-03-21',//=>'春分の日',
        '2010-03-22',//=>'振替休日',
        '2010-04-29',//=>'昭和の日',
        '2010-05-03',//=>'憲法記念日',
        '2010-05-04',//=>'みどりの日',
        '2010-05-05',//=>'こどもの日',
        '2010-07-19',//=>'海の日',
        '2010-09-20',//=>'敬老の日',
        '2010-09-23',//=>'秋分の日',
        '2010-10-11',//=>'体育の日',
        '2010-11-03',//=>'文化の日',
        '2010-11-23',//=>'勤労感謝の日',
        '2010-12-23',//=>'天皇誕生日',

        '2011-01-01',//=>'元日',
        '2011-01-10',//=>'成人の日',
        '2011-02-11',//=>'建国記念の日',
        '2011-03-21',//=>'春分の日',
        '2011-04-29',//=>'昭和の日',
        '2011-05-03',//=>'憲法記念日',
        '2011-05-04',//=>'みどりの日',
        '2011-05-05',//=>'こどもの日',
        '2011-07-18',//=>'海の日',
        '2011-09-19',//=>'敬老の日',
        '2011-09-23',//=>'秋分の日',
        '2011-10-10',//=>'体育の日',
        '2011-11-03',//=>'文化の日',
        '2011-11-23',//=>'勤労感謝の日',
        '2011-12-23',//=>'天皇誕生日',

        '2012-01-01',//=>'元日',
        '2012-01-02',//=>'振替休日',
        '2012-01-09',//=>'成人の日',
        '2012-02-11',//=>'建国記念の日',
        '2012-03-20',//=>'春分の日',
        '2012-04-29',//=>'昭和の日',
        '2012-04-30',//=>'振替休日',
        '2012-05-03',//=>'憲法記念日',
        '2012-05-04',//=>'みどりの日',
        '2012-05-05',//=>'こどもの日',
        '2012-07-16',//=>'海の日',
        '2012-09-17',//=>'敬老の日',
        '2012-09-22',//=>'秋分の日',
        '2012-10-08',//=>'体育の日',
        '2012-11-03',//=>'文化の日',
        '2012-11-23',//=>'勤労感謝の日',
        '2012-12-23',//=>'天皇誕生日',
        '2012-12-24',//=>'振替休日',

        '2013-01-01',//=>'元日',
        '2013-01-14',//=>'成人の日',
        '2013-02-11',//=>'建国記念の日',
        '2013-03-20',//=>'春分の日',
        '2013-04-29',//=>'昭和の日',
        '2013-05-03',//=>'憲法記念日',
        '2013-05-04',//=>'みどりの日',
        '2013-05-05',//=>'こどもの日',
        '2013-05-06',//=>'振替休日',
        '2013-07-15',//=>'海の日',
        '2013-09-16',//=>'敬老の日',
        '2013-09-23',//=>'秋分の日',
        '2013-10-14',//=>'体育の日',
        '2013-11-03',//=>'文化の日',
        '2013-11-04',//=>'振替休日',
        '2013-11-23',//=>'勤労感謝の日',
        '2013-12-23',//=>'天皇誕生日',

        '2014-01-01',//=>'元日',
        '2014-01-13',//=>'成人の日',
        '2014-02-11',//=>'建国記念の日',
        '2014-03-21',//=>'春分の日',
        '2014-04-29',//=>'昭和の日',
        '2014-05-03',//=>'憲法記念日',
        '2014-05-04',//=>'みどりの日',
        '2014-05-05',//=>'こどもの日',
        '2014-05-06',//=>'振替休日',
        '2014-07-21',//=>'海の日',
        '2014-09-15',//=>'敬老の日',
        '2014-09-23',//=>'秋分の日',
        '2014-10-13',//=>'体育の日',
        '2014-11-03',//=>'文化の日',
        '2014-11-23',//=>'勤労感謝の日',
        '2014-11-24',//=>'振替休日',
        '2014-12-23',//=>'天皇誕生日',

        '2015-01-01',//=>'元日',
        '2015-01-12',//=>'成人の日',
        '2015-02-11',//=>'建国記念の日',
        '2015-03-21',//=>'春分の日',
        '2015-04-29',//=>'昭和の日',
        '2015-05-03',//=>'憲法記念日',
        '2015-05-04',//=>'みどりの日',
        '2015-05-05',//=>'こどもの日',
        '2015-05-06',//=>'振替休日',
        '2015-07-20',//=>'海の日',
        '2015-09-21',//=>'敬老の日',
        '2015-09-22',//=>'国民の休日',
        '2015-09-23',//=>'秋分の日',
        '2015-10-12',//=>'体育の日',
        '2015-11-03',//=>'文化の日',
        '2015-11-23',//=>'勤労感謝の日',
        '2015-12-23',//=>'天皇誕生日',

        '2016-01-01',//=>'元日',
        '2016-01-11',//=>'成人の日',
        '2016-02-11',//=>'建国記念の日',
        '2016-03-20',//=>'春分の日',
        '2016-03-21',//=>'振替休日',
        '2016-04-29',//=>'昭和の日',
        '2016-05-03',//=>'憲法記念日',
        '2016-05-04',//=>'みどりの日',
        '2016-05-05',//=>'こどもの日',
        '2016-07-18',//=>'海の日',
        '2016-09-19',//=>'敬老の日',
        '2016-09-22',//=>'秋分の日',
        '2016-10-10',//=>'体育の日',
        '2016-11-03',//=>'文化の日',
        '2016-11-23',//=>'勤労感謝の日',
        '2016-12-23',//=>'天皇誕生日',

        '2017-01-01',//=>'元日',
        '2017-01-02',//=>'振替休日',
        '2017-01-09',//=>'成人の日',
        '2017-02-11',//=>'建国記念の日',
        '2017-03-20',//=>'春分の日',
        '2017-04-29',//=>'昭和の日',
        '2017-05-03',//=>'憲法記念日',
        '2017-05-04',//=>'みどりの日',
        '2017-05-05',//=>'こどもの日',
        '2017-07-17',//=>'海の日',
        '2017-09-18',//=>'敬老の日',
        '2017-09-23',//=>'秋分の日',
        '2017-10-09',//=>'体育の日',
        '2017-11-03',//=>'文化の日',
        '2017-11-23',//=>'勤労感謝の日',
        '2017-12-23',//=>'天皇誕生日',

        '2018-01-01',//=>'元日',
        '2018-01-08',//=>'成人の日',
        '2018-02-11',//=>'建国記念の日',
        '2018-02-12',//=>'振替休日',
        '2018-03-21',//=>'春分の日',
        '2018-04-29',//=>'昭和の日',
        '2018-04-30',//=>'振替休日',
        '2018-05-03',//=>'憲法記念日',
        '2018-05-04',//=>'みどりの日',
        '2018-05-05',//=>'こどもの日',
        '2018-07-16',//=>'海の日',
        '2018-09-17',//=>'敬老の日',
        '2018-09-23',//=>'秋分の日',
        '2018-09-24',//=>'振替休日',
        '2018-10-08',//=>'体育の日',
        '2018-11-03',//=>'文化の日',
        '2018-11-23',//=>'勤労感謝の日',
        '2018-12-23',//=>'天皇誕生日',
        '2018-12-24',//=>'振替休日',

        '2019-01-01',//=>'元日',
        '2019-01-14',//=>'成人の日',
        '2019-02-11',//=>'建国記念の日',
        '2019-03-21',//=>'春分の日',
        '2019-04-29',//=>'昭和の日',
        '2019-05-03',//=>'憲法記念日',
        '2019-05-04',//=>'みどりの日',
        '2019-05-05',//=>'こどもの日',
        '2019-05-06',//=>'振替休日',
        '2019-07-15',//=>'海の日',
        '2019-09-16',//=>'敬老の日',
        '2019-09-23',//=>'秋分の日',
        '2019-10-14',//=>'体育の日',
        '2019-11-03',//=>'文化の日',
        '2019-11-04',//=>'振替休日',
        '2019-11-23',//=>'勤労感謝の日',
        '2019-12-23',//=>'天皇誕生日',

        '2020-01-01',//=>'元日',
        '2020-01-13',//=>'成人の日',
        '2020-02-11',//=>'建国記念の日',
        '2020-03-20',//=>'春分の日',
        '2020-04-29',//=>'昭和の日',
        '2020-05-03',//=>'憲法記念日',
        '2020-05-04',//=>'みどりの日',
        '2020-05-05',//=>'こどもの日',
        '2020-05-06',//=>'振替休日',
        '2020-07-20',//=>'海の日',
        '2020-09-21',//=>'敬老の日',
        '2020-09-22',//=>'秋分の日',
        '2020-10-12',//=>'体育の日',
        '2020-11-03',//=>'文化の日',
        '2020-11-23',//=>'勤労感謝の日',
        '2020-12-23',//=>'天皇誕生日'
        );
        $now = time();
        $nextday = $now;
        $i = $x_eigyoubi;
        while ( 1 ) {
            if ( $i==0 ) {
                break;
            }
            $nextday = $nextday + (24*60*60);
            $n_y = date('Y', $nextday);
            $n_m = date('m', $nextday);
            $n_d = date('d', $nextday);
            $n_youbi = Util::getWeek($n_m, $n_d, $n_y);
            if ($n_youbi=="土" || $n_youbi=="日") {
                continue;
            }
            if ( in_array(date("Y-m-d", $nextday), $holiday) ) {
                continue;
            }
            $i = $i - 1;
        }
        return $nextday;
    }

    /**
     * 正しいクレジットカード番号かチェック
     * @param string $card_number
     * @return boolean
     */
    public static function checkCard($card_number)
    {
        //変数の初期化
        $check = "";
        $card_number = str_replace("-", "", $card_number);

        //クレジットカードのタイプ 最初の一桁
        //$type = array(
        //    0=>"予備",
        //    1=>"航空",
        //    2=>"航空",
        //    3=>"旅行・娯楽",
        //    4=>"銀行・金融",
        //    5=>"銀行・金融",
        //    6=>"商品輸送・銀行",
        //    7=>"石油",
        //    8=>"通信",
        //    9=>"国ごとの割り当て分");

        //数値以外はエラーを返す。
        if (!is_numeric($card_number)) {
            return false;
        }
        //桁数が足りなければエラーを返す
        if (16 != strlen($card_number)) {
            return false;
        }

        //文字列を配列に分解
        $card = str_split($card_number);

        //数字の数だけループする
        for ($i = 0; $i < 16; $i++) {
            //奇数の場合のみ2倍する
            if ($i % 2 == 0) {
                $card[$i] = $card[$i] * 2;
            }
            //2桁の場合は分割して足す
            if (mb_strlen( $card[$i] ) != 1) {
                $split = str_split($card[$i]);
                $card[$i] = $split[0] + $split[1];
            }
            $check += $card[$i];
        }

        if ($check % 10 == 0) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * メール送信
     * 
     * @param array $to_array to=>toname
     * @param array $cc_array cc=>ccname
     * @param array $bcc_array bcc=>bccname
     * @param string $from
     * @param string $fromname
     * @param string $subject
     * @param string $body
     * @param bool $isSMTP
     * @param array $attach_array 添付ファイル名配列
     * @param string $attach_type string or binary(default binary)
     * @throws \Exception
     */
    public static function sendmail($to_array, $cc_array, $bcc_array,
            $from, $fromname, $subject, $body , $isSMTP = false,
            $attach_array = nulll, $attach_type = "binary")
    {
        try {
            mb_language("japanese");
            mb_internal_encoding("UTF-8");
            $mail = new \PHPMailer(true);
            $mail->CharSet = "utf-8";
            foreach ($to_array as $to => $toname) {
                $toname = mb_encode_mimeheader($toname, "UTF-8", 'B');
                $to = Util::removeCrLf($to);
                $mail->AddAddress($to, $toname);
            }
            if ($cc_array != null) {
                foreach ($cc_array as $cc => $ccname) {
                    $ccname = mb_encode_mimeheader($ccname, "UTF-8", 'B');
                    $cc = Util::removeCrLf($cc);
                    $mail->addCC($cc, $ccname);
                }
            }
            if ($bcc_array != null) {
                foreach ($bcc_array as $bcc => $bccname) {
                    $bccname = mb_encode_mimeheader($bccname, "UTF-8", 'B');
                    $bcc = Util::removeCrLf($bcc);
                    $mail->addBCC($bcc, $bccname);
                }
            }
            $mail->From = $from;
            $mail->FromName = mb_encode_mimeheader($fromname, "UTF-8", 'B');
            $mail->Subject = mb_encode_mimeheader($subject, "UTF-8", 'B');
            $mail->Body  = $body;
            if ($attach_array != null) {
                foreach ($attach_array as $attach) {
                    if ($attach_type == "binary") {
                        $mail->addAttachment($attach);
                    } else {
                        $mail->addStringAttachment($attach->data, $attach->filename);
                    }
                }
            }
            //$mail->AddAttachment($attachfile);
            if ($isSMTP) {
                //$mail->$SMTPDebug = 2;
                $mail->IsSMTP();
                $mail->SMTPAuth = true;
                //
                // TODO:直す
                //
                global $SMTP_MAIL_SERVER_HOST;
                global $SMTP_MAIL_SERVER_PORT;
                global $SMTP_MAIL_USERNAME;
                global $SMTP_MAIL_PASSWORD;
                $mail->Host = $SMTP_MAIL_SERVER_HOST;
                $mail->Port = $SMTP_MAIL_SERVER_PORT;
                $mail->Username = $SMTP_MAIL_USERNAME;
                $mail->Password = $SMTP_MAIL_PASSWORD;
                //$mail->Host = "smtp.gmail.com";
                //$mail->Username = "example＠gmail.com";
                //$mail->Password = "password";
            }
            $mail->Send();
        } catch (\Exception $excep) {
            throw $excep;
        }
    }
    
    /**
     * カレンダー表示
     * http://shanabrian.com/
     * 
     * @param type $year
     * @param type $month
     * @return string
     */
    public static function calendar($year = "", $month = "")
    {
        if (empty($year) && empty($month)) {
            $year = date("Y");
            $month = date("n");
        }
        //月末の取得
        $l_day = date("j", mktime(0, 0, 0, $month + 1, 0, $year));
        //初期出力
        $tmp = <<<EOM
<table class="calendar">
    <caption>{$year}年{$month}月</caption>
    <tr>
        <th class="sun">日</th>
        <th>月</th>
        <th>火</th>
        <th>水</th>
        <th>木</th>
        <th>金</th>
        <th class="sat">土</th>
    </tr>\n
EOM;
        $lc = 0;
        //月末分繰り返す
        for ($i=1; $i<$l_day+1; $i++) {
            //曜日の取得
            $week = date("w", mktime(0, 0, 0, $month, $i, $year));
            //曜日が日曜日の場合
            if ($week == 0) {
                $tmp .= "\t<tr>\n";
                $lc++;
            }
            //1日の場合
            if ($i == 1) {
                if ($week != 0) {
                    $tmp .= "\t<tr>\n";
                    $lc++;
                }
                $tmp .= self::calRepeat($week);
            }
            if ($i == date("j") && $year == date("Y") && $month == date("n")) {
                //現在の日付の場合
                $tmp .= "\t\t<td class=\"today\">{$i}</td>\n";
            } else {
                //現在の日付ではない場合
                $tmp .= "\t\t<td>{$i}</td>\n";
            }
            //月末の場合
            if ($i == $l_day) {
                $tmp .= self::calRepeat(6 - $week);
            }
            //土曜日の場合
            if ($week == 6) {
                $tmp .= "\t</tr>\n";
            }
        }
        if ($lc < 6) {
            $tmp .= "\t<tr>\n";
            $tmp .= self::calRepeat(7);
            $tmp .= "\t</tr>\n";
        }
        if ($lc == 4) {
            $tmp .= "\t<tr>\n";
            $tmp .= self::calRepeat(7);
            $tmp .= "\t</tr>\n";
        }
        $tmp .= "</table>\n";
        return $tmp;
    }

    private static function calRepeat($n)
    {
        return str_repeat("\t\t<td> </td>\n", $n);
    }
    
    /**
     * 改行を取り除く
     * 主にメールアドレスに改行+CCのようなデータを受け取ると困るため
     * 
     * @param type $str
     * @return type
     */
    public static function removeCrLf($str)
    {
        $str = preg_replace("/\r/", "", $str);
        $str = preg_replace("/\n/", "", $str);
        return $str;
    }
    
    /**
     * アップロード失敗理由取得
     * 
     * @param type $code
     * @return string
     */
    public static function getFileuploadError($code)
    {
        $message = "";
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini"; 
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;
            default:
                $message = "Unknown upload error";
                break;
        }
        return $message;
    }

    /**
     * バイト数をフォーマットする
     * http://qiita.com/suin/items/0090ab167bbdb3d77181
     *
     * @param integer $bytes
     * @param integer $precision
     * @param array $units
     */
    public static function formatBytes($bytes, $precision = 2, array $units = null)
    {
        if (abs($bytes) < 1024) {
            $precision = 0;
        }

        if (is_array($units) === false) {
            $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        }

        if ($bytes < 0) {
            $sign = '-';
            $bytes = abs($bytes);
        } else {
            $sign = '';
        }

        $exp   = floor(log($bytes) / log(1024));
        $unit  = $units[$exp];
        $bytes = $bytes / pow(1024, floor($exp));
        $bytes = sprintf('%.'.$precision.'f', $bytes);
        return $sign.$bytes.' '.$unit;
    }

    /**
     * 簡易BASIC認証
     * 
     * @param type $id 通すID
     * @param type $pass 通すPASSWORD
     */
    public static function simpleBasicAuth($id, $pass)
    {
        if (!isset($_SERVER["PHP_AUTH_USER"])) {
            header("WWW-Authenticate: Basic realm=\"Please Enter Your Password\"");
            header("HTTP/1.0 401 Unauthorized");
            //キャンセル時の表示
            echo "Authorization Required";
            exit;
        } else {
            if ($_SERVER["PHP_AUTH_USER"] === $id && $_SERVER["PHP_AUTH_PW"] === $pass) {
                //認証成功後の処理
            } else {
                //認証エラーの処理
                //キャンセル時の表示
                echo "Authorization Required";
                exit;
            }
        }
    }
    
    /**
     * ファイルダウンロード用のヘッダーを出力
     * 
     * これを読んだ後は速やかにファイルコンテンツを出力して、exit()する事
     * @param type $filename クライアントに渡すファイル名
     */
    public static function downloadHeader($filename)
    {
        header("Cache-Control: public");
        header("Pragma: public");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=" . $filename);
    }

}

