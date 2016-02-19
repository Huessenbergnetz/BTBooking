<?php

function btc_gen_attr(&$ret, $attr, $val = null) {
	if (!empty($val)) {
		if (is_bool($val)) {
			$ret .= " $attr=\"$attr\"";
		} elseif (is_array($val) && $attr == 'style') {
			$ret .= btc_gen_style_attr($val);
		} else {
			$ret .= " $attr=\"$val\"";
		}
	}
}

function btc_gen_style_attr($styles) {

	$ret = "";

	if (is_array($styles)) {
		$ret .= " style=\"";
		foreach($styles as $key => $value) {
			$ret .= "$key:$value;";
		}
		$ret .= "\"";
	} elseif (is_string($styles)) {
		$ret = " style=\"$styles\"";
	}

	return $ret;
}

?>