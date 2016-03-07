<?php

require_once 'class.btc-form-basic.php';


class BTCFormSelect extends BTCHtmlForm {

	public $size = 0;

	public $multiple = false;

	public $options = array();

	protected $tag_name = 'select';

	public function __construct(array $options = array(), array $attrs = array()) {

		$this->options = $options;
		if (!empty($attrs)) {
			foreach($attrs as $key => $value) {
				$this->$key = $value;
			}
		}
	}

	protected function _render() {

		parent::_render();

		$this->add_attr('size', $this->size);
		$this->add_attr('multiple', $this->multiple);

		$this->output .= '>';

		if (!empty($this->options)) {

			if ($this->multiple) {
				if (!is_array($this->value)) {
					if ($this->value) {
						$this->value = array($this->value);
					} else {
						$this->value = array();
					}
				}
			}

			foreach($this->options as $value => $text) {

				if (is_scalar($text)) {

					$this->output .= '<option value="' . $value . '"';

					if ($this->multiple) {
						if (in_array($value, $this->value)) $this->output .= ' selected';
					} else {
						if ($value == $this->value) $this->output .= ' selected';
					}

					$this->output .= '>' . $text . '</option>' ;

				} else if (is_object($text)) {

					if ($this->multiple) {
						if (in_array($text->value, $this->value)) $text->selected = true;
					} else {
						if ($text->value == $this->value) $text->selected = true;
					}

					$this->output .= $text->render(false);
				}
			}
		}

		$this->closeTag();
	}
}

?>