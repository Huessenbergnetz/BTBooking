<?php

require_once 'class.btc-form-basic.php';

class BTCHtmlInput extends BTCHtmlForm {

	protected $type = '';

	public $autocomplete = false;

	public $list = '';

	public $pattern = '';

	public $placeholder = '';

	public $readonly = false;

	public $size = 0;

	protected $tag_name = 'input';

	protected function _render() {

		parent::_render();

		$this->add_attr('type', $this->type);
		$this->add_attr('autocomplete', $this->autocomplete);
		$this->add_attr('list', $this->list);
		$this->add_attr('pattern', $this->pattern);
		$this->add_attr('placeholder', $this->placeholder);
		$this->add_attr('readonly', $this->readonly);
		$this->add_attr('size', $this->size);
	}

}


?>