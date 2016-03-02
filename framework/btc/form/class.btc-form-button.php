<?php

require_once 'class.btc-form-basic.php';


class BTCFormButton extends BTCHtmlForm {

	public $type = 'submit';

	public $content = null;

	public $formaction = '';

	public $formmethod = '';

	public $formenctype = '';

	public $formnovalidate = false;

	protected $tag_name = 'button';

	public function __construct(array $attrs = array(), $content =  null) {

		$this->content = $content;
		if (!empty($attrs)) {
			foreach($attrs as $key => $value) {

				$this->$key = $value;
			}
		}
	}


	protected function _render() {

		parent::_render();

		$this->add_attr('type', $this->type);
		$this->add_attr('formaction', $this->formaction);
		$this->add_attr('formmethod', $this->formmethod);
		$this->add_attr('formenctype', $this->formenctype);
		$this->add_attr('formnovalidate', $this->formnovalidate);

		$this->output .= '>';

		if ($this->content) {
			if (is_object($this->content)) {
				$this->output .= $this->content->render(false);
			} elseif (is_scalar($this->content)) {
				$this->output .= $this->content;
			}
		}

		$this->closeTag();
	}
}

?>