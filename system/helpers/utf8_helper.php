<?php if(! defined('BASEPATH')) exit('not access script allowed');

/*
 * it use language vietnamese, remove chart to normal chart
 * Author: write by tinhphaistc
 * Email: tinhphaistc@gmail.com
 * Y & skype: tinhphaistc
 */
if(!function_exists('remove_utf8'))
{
    function remove_utf8($string)
    {
        $trans = array(
        "đ"=>"d","ă"=>"a","â"=>"a","á"=>"a","à"=>"a",
        "ả"=>"a","ã"=>"a","ạ"=>"a",
        "ấ"=>"a","ầ"=>"a","ẩ"=>"a","ẫ"=>"a","ậ"=>"a",
        "ắ"=>"a","ằ"=>"a","ẳ"=>"a","ẵ"=>"a","ặ"=>"a",
        "é"=>"e","è"=>"e","ẻ"=>"e","ẽ"=>"e","ẹ"=>"e",
        "ế"=>"e","ề"=>"e","ể"=>"e","ễ"=>"e","ệ"=>"e",
        "í"=>"i","ì"=>"i","ỉ"=>"i","ĩ"=>"i","ị"=>"i",
        "ư"=>"u","ô"=>"o","ơ"=>"o","ê"=>"e",
        "Ư"=>"u","Ô"=>"o","Ơ"=>"o","Ê"=>"e",
        "ú"=>"u","ù"=>"u","ủ"=>"u","ũ"=>"u","ụ"=>"u",
        "ứ"=>"u","ừ"=>"u","ử"=>"u","ữ"=>"u","ự"=>"u",
        "ó"=>"o","ò"=>"o","ỏ"=>"o","õ"=>"o","ọ"=>"o",
        "ớ"=>"o","ờ"=>"o","ở"=>"o","ỡ"=>"o","ợ"=>"o",
        "ố"=>"o","ồ"=>"o","ổ"=>"o","ỗ"=>"o","ộ"=>"o",
        "ú"=>"u","ù"=>"u","ủ"=>"u","ũ"=>"u","ụ"=>"u",
        "ứ"=>"u","ừ"=>"u","ử"=>"u","ữ"=>"u","ự"=>"u",
        "ý"=>"y","ỳ"=>"y","ỷ"=>"y","ỹ"=>"y","ỵ"=>"y",
        "Ý"=>"Y","Ỳ"=>"Y","Ỷ"=>"Y","Ỹ"=>"Y","Ỵ"=>"Y",
        "Đ"=>"D","Ă"=>"A","Â"=>"A","Á"=>"A","À"=>"A",
        "Ả"=>"A","Ã"=>"A","Ạ"=>"A",
        "Ấ"=>"A","Ầ"=>"A","Ẩ"=>"A","Ẫ"=>"A","Ậ"=>"A",
        "Ắ"=>"A","Ằ"=>"A","Ẳ"=>"A","Ẵ"=>"A","Ặ"=>"A",
        "É"=>"E","È"=>"E","Ẻ"=>"E","Ẽ"=>"E","Ẹ"=>"E",
        "Ế"=>"E","Ề"=>"E","Ể"=>"E","Ễ"=>"E","Ệ"=>"E",
        "Í"=>"I","Ì"=>"I","Ỉ"=>"I","Ĩ"=>"I","Ị"=>"I",
        "Ư"=>"U","Ô"=>"O","Ơ"=>"O","Ê"=>"E",
        "Ư"=>"U","Ô"=>"O","Ơ"=>"O","Ê"=>"E",
        "Ú"=>"U","Ù"=>"U","Ủ"=>"U","Ũ"=>"U","Ụ"=>"U",
        "Ứ"=>"U","Ừ"=>"U","Ử"=>"U","Ữ"=>"U","Ự"=>"U",
        "Ó"=>"O","Ò"=>"O","Ỏ"=>"O","Õ"=>"O","Ọ"=>"O",
        "Ớ"=>"O","Ờ"=>"O","Ở"=>"O","Ỡ"=>"O","Ợ"=>"O",
        "Ố"=>"O","Ồ"=>"O","Ổ"=>"O","Ỗ"=>"O","Ộ"=>"O",
        "Ú"=>"U","Ù"=>"U","Ủ"=>"U","Ũ"=>"U","Ụ"=>"U",
        "Ứ"=>"U","Ừ"=>"U","Ử"=>"U","Ữ"=>"U","Ự"=>"U",);
      
        //remove any '-' from the string they will be used as concatonater
        $str = str_replace('-', ' ', $string);
        $str = strtr($str, $trans);
        // remove any duplicate whitespace, and ensure all characters are alphanumeric
        $str = preg_replace(array('/\s+/','/[^A-Za-z0-9\-]/'), array('-',''), $str);
        // lowercase and trim
        $str = trim(strtolower($str));
        return $str;
    }
}

/* End of file uft8_helper.php */
/* Location: ./system/helpers/uft8_helper.php */