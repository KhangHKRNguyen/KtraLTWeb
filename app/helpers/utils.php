<?php

if (!function_exists('slugify')) {
    function slugify($text) {
        $text = strtolower($text);
        $text = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $text);
        $text = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $text);
        $text = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $text);
        $text = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $text);
        $text = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $text);
        $text = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $text);
        $text = preg_replace('/(đ)/', 'd', $text);
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/([\s_-]+)/', '-', $text);
        $text = trim($text, '-');
        return $text;
    }
}

if (!function_exists('generateAcronymSKU')) {

    function generateAcronymSKU($text) {
        $text_upper = strtoupper(trim($text));
        
        if (strpos($text_upper, 'IPHONE') === 0) {
            $rest = substr($text_upper, 6);
            $rest = preg_replace('/[\s\W_]+/', '', $rest); 
            return 'IP' . $rest; // "IP" + "16PRM"
        }
        
        if (strpos($text_upper, 'SAMSUNG GALAXY') === 0) {
            $rest = substr($text_upper, 14);
            $rest = preg_replace('/[\s\W_]+/', '', $rest);
            return 'SSGLX' . $rest; // "SSGLX" + "S25"
        }        
        $words = preg_split('/[\s_-]+/', $text_upper);
        $sku = '';
        foreach ($words as $word) {
            if (empty($word)) continue;
            
            // Kiểm tra xem từ là chữ+số (như 16PRM) hoặc toàn số
            if (is_numeric($word) || (ctype_alnum($word) && !ctype_alpha($word))) {
                 $sku .= $word; 
            } else {
                 $sku .= substr($word, 0, 1); // Lấy chữ cái đầu
            }
        }
        
        return $sku;
    }
}

if (!function_exists('sanitizeString')) {
    function sanitizeString($str) {
        if ($str === null) {
            return '';
        }
        $str = trim($str); 
        $str = strip_tags($str);
        return $str;
    }
}
?>