<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhoenixDesign\Lib;

abstract class Validation extends \PhoenixDesign\Lib\Api{
    protected function required($input_data, $data)
    {
        if (!isset($input_data[$data]) || 0 == strlen($input_data[$data])) {
            return false;
        }
        return true;
    }
    
    protected function leastRequired($input_data, $data_array)
    {
        for ($i=0; $i<count($data_array); $i++) {
            if (isset($input_data[$data_array[$i]])) {
                if (0 != strlen($input_data[$data_array[$i]])) {
                    return true;
                }
            }
        }
        return false;
    }
    
    protected function mailaddr($input_data, $data)
    {
        if (!isset($input_data[$data])) {
            return true;
        }
        return Util::isMailAddr(str_replace(' ', '', $input_data[$data]));
    }
    protected function allAscii($input_data, $data)
    {
        if (!isset($input_data[$data])) {
            return true;
        }
        return Util::isAscii(str_replace(' ', '', $input_data[$data]));
    }
    protected function allZenkaku($input_data, $data)
    {
        if (!isset($input_data[$data])) {
            return true;
        }
        return Util::isAllZenkaku(str_replace(' ', '', $input_data[$data]));
    }
    protected function allHiragana($input_data, $data)
    {
        if (!isset($input_data[$data])) {
            return true;
        }
        return Util::isAllHiragana(str_replace(' ', '', $input_data[$data]));
    }
    protected function allKatakana($input_data, $data)
    {
        if (!isset($input_data[$data])) {
            return true;
        }
        return Util::isAllKatakana(str_replace(' ', '', $input_data[$data]));
    }
}