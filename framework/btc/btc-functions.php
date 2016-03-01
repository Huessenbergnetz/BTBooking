<?php

function btc_gen_attr(&$ret, $attr, $val = null) {
	if (!empty($val)) {
		if (is_bool($val)) {
			$ret .= " $attr=\"$attr\"";
		} else {
			$ret .= " $attr=\"$val\"";
		}
	}
}

function btc_gen_style_attr(&$ret, $styles) {

	if (!empty($styles)) {
		if (is_array($styles)) {
			$ret .= " style=\"";
			foreach($styles as $key => $value) {
				$ret .= "$key:$value;";
			}
			$ret .= "\"";
		} elseif (is_string($styles)) {
			$ret .= " style=\"$styles\"";
		}
	}
}

function btc_gen_aria_attrs(&$ret, $arias) {

	if (!empty($arias) && is_array($arias)) {
		foreach($arias as $tag => $value) {
			$ret .= sprintf(' aria-%s="%s"', $tag, $value);
		}
	}
}


function btc_gen_data_attrs(&$ret, $datas) {

	if (!empty($datas) && is_array($datas)) {
		foreach($datas as $tag => $value) {
			$ret .= sprintf(' data-%s="%s"', $tag, $value);
		}
	}
}


function btc_gen_class_attr($ret, $htmlClasses) {

	if (!empty($htmlClasses)) {
		if (is_array($htmlClasses)) {
			$_classes = implode(" ", $htmlClasses);
			$ret .= " class=\"$_classes\"";
		} else {
			$ret .= " class=\"$htmlClasses\"";
		}
	}
}

?>