<?php

require_once(__DIR__.'/../class.btc-html-basic.php');


class BTCFormLabel extends BTCHtml {

	public $for = '';

	public $form = '';

	public $content = '';

	public $contentObject;

	public $lineBreak = false;

	protected $tag_name = 'label';

	public function __construct(array $attrs = array(), $content = '', $contentObject = null) {

		$this->content = $content;
		$this->contentObject = $contentObject;
		if (!empty($attrs)) {
			foreach($attrs as $key => $value) {

				$this->$key = $value;
			}
		}
	}

	protected function _render() {

		parent::_render();

		if (empty($this->for) && $this->contentObject) {
			$this->for = $this->contentObject->id;
		}

		$this->add_attr('form', $this->form);

		$this->output .= '>';

		if ($this->contentObject) {
			$this->output .= $this->contentObject->render(false);
			$this->output .= ' ';
		}

		if (!empty($this->content)) {

			if (is_scalar($this->content)) {
				$this->output .= $this->content;
			}
		}

		$this->closeTag();

		if ($this->lineBreak) {
			$this->output .= '<br>';
		}

		return $ret;
	}
}

?>