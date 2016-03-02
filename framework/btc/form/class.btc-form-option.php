<?php

require_once(__DIR__.'/../class.btc-html-basic.php');

class BTCFormOption extends BTCHtml {

	public $value = '';

	public $label = '';

	public $disabled = false;

	public $selected = false;

	public $content = '';

	protected $tag_name = 'option';

	protected function _render() {

		parent::_render();

		$this->add_attr('value', $this->value);
		$this->add_attr('label', $this->label);
		$this->add_attr('disabled', $this->disabled);
		$this->add_attr('selected', $this->selected);

		$this->output .= '>';

		if (!empty($this->content)) {
			$this->output .= $this->content;
		}

		$this->closeTag();
	}

}

?>
