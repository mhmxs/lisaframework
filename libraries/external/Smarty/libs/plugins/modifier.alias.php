<?php
function is_utf8($str) {
    $c=0; $b=0;
    $bits=0;
    $len=strlen($str);
    for($i=0; $i<$len; $i++){
        $c=ord($str[$i]);
        if($c > 128){
            if(($c >= 254)) return false;
            elseif($c >= 252) $bits=6;
            elseif($c >= 248) $bits=5;
            elseif($c >= 240) $bits=4;
            elseif($c >= 224) $bits=3;
            elseif($c >= 192) $bits=2;
            else return false;
            if(($i+$bits) > $len) return false;
            while($bits > 1){
                $i++;
                $b=ord($str[$i]);
                if($b < 128 || $b > 191) return false;
                $bits--;
            }
        }
    }
    return true;
}

function lowerUtf8 ($string)
{
    $string     = mb_strtolower($string, "UTF-8");
    $chars_from = array("ö", "ü", "ó", "ő", "ú", "é", "á", "ű", "í", "û", "õ");
    $chars_to   = array("o", "u", "o", "o", "u", "e", "a", "u", "i", "u", "o");
    $string     = str_replace($chars_from, $chars_to, $string);

	return $string;
}

function smarty_modifier_alias($string)
{
    	$string     = trim($string);
    	$string     = (is_utf8($string) == true) ? lowerUtf8($string) : strtolower($string);
	    $string     = preg_replace("/[^a-z0-9-]/i", "-", $string);

	    do {
	        $string = str_replace("--", "-", $string);
	    } while (ereg("--", $string));

	    $string = trim($string, "-");

	    return $string;
}

?>
