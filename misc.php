<?php

function randomcolor(){
	$red = intval((mt_rand(0,256)+160)/2);
	$green = intval((mt_rand(0,256)+160)/2);
	$blue = intval((mt_rand(0,256)+160)/2);
	return array( 'red' => $red , 'green' => $green , 'blue' => $blue );
}

function rgb2hsv($rgb){
	$r = (float) $rgb['red'] / 255;
	$g = (float) $rgb['green'] / 255;
	$b = (float) $rgb['blue'] / 255;

	$max = max($r, $g, $b);
	$min = min($r, $g, $b);
	$v = $max;

	if($max === $min){
		$h = 0;
	} else if($r === $max){
		$h = 60 * ( ($g - $b) / ($max - $min) ) + 0;
	} else if($g === $max){
		$h = 60 * ( ($b - $r) / ($max - $min) ) + 120;
	} else {
		$h = 60 * ( ($r - $g) / ($max - $min) ) + 240;
	}
	if($h < 0) $h = $h + 360;

	$s = ($v != 0) ? ($max - $min) / $max : 0;

	$hsv = array("h" => $h, "s" => $s, "v" => $v);
	return $hsv;
}

function hsv2rgb($h, $s, $v){
	if ( $s == 0 ) {
		$r = $v * 255;
		$g = $v * 255;
		$b = $v * 255;
	} else {
		$h = ($h >= 0) ? $h % 360 / 360 : $h % 360 / 360 + 1;
		$var_h = $h * 6;
		$i = (int)$var_h;
		$f = $var_h - $i;

		$p = $v * ( 1 - $s );
		$q = $v * ( 1 - $s * $f );
		$t = $v * ( 1 - $s * ( 1 - $f ) );

		switch($i){
			case 0:
				$var_r = $v;
				$var_g = $t;
				$var_b = $p;
				break;
			case 1:
				$var_r = $q;
				$var_g = $v;
				$var_b = $p;
				break;
			case 2:
				$var_r = $p;
				$var_g = $v;
				$var_b = $t;
				break;
			case 3:
				$var_r = $p;
				$var_g = $q;
				$var_b = $v;
				break;
			case 4:
				$var_r = $t;
				$var_g = $p;
				$var_b = $v;
				break;
			default:
				$var_r = $v;
				$var_g = $p;
				$var_b = $q;
		}
		$r = intval($var_r * 255);
		$g = intval($var_g * 255);
		$b = intval($var_b * 255);
	}
	return array('red'=>$r, 'green'=>$g, 'blue'=>$b);
}

function highsaturation($color){
	$hsv = rgb2hsv($color);
	return hsv2rgb($hsv['h'],1-(1-$hsv['s'])*(1-$hsv['s']),$hsv['v']);
}

function rgbcss($color){
	return 'rgb('.$color['red'].','.$color['green'].','.$color['blue'].')';
}

function console_log( $data ){
	echo '<script>';
	echo 'console.log('. json_encode( $data ) .')';
	echo '</script>';
}

?>