<?php

require_once 'btc-functions.php';


/**
 * Base class for all HTML elements.
 */
class BTCHtml {

	public $id = '';

	public $htmlClasses = null;

	public $accesskey = '';

	public $aria = array();

	public $contenteditable = false;

	public $contextmenu = '';

	public $data = array();

	public $dir = '';

	public $draggable = false;

	public $hidden = false;

	public $lang = '';

	public $style = null;

	public $tabindex = '';

	public $title = '';

	public $hide = false;

	protected $tag_name = '';

	protected $output = '';


	public function render($echo = true) {

		if ($echo) {
			$this->_render();
			echo $this->output;
		} else {
			$this->_render();
			return $this->output;
		}

	}

	protected function _render() {

		if ($this->hide) {
			if (is_array($this->style)) {
				$this->style['display'] = 'none';
			} else {
				$this->style .= " display:none;";
			}
		}

		$this->output = '<' . $this->tag_name;

		btc_gen_attr($this->output, 'id', $this->id);
		btc_gen_class_attr($this->output, $this->htmlClasses);
		btc_gen_attr($this->output, 'accesskey', $this->accesskey);
		btc_gen_aria_attrs($this->output, $this->aria);
		btc_gen_attr($this->output, 'contenteditable', $this->contenteditable);
		btc_gen_attr($this->output, 'contextmenu', $this->contextmenu);
		btc_gen_data_attrs($this->output, $this->data);
		btc_gen_attr($this->output, 'dir', $this->dir);
		btc_gen_attr($this->output, 'draggable', $this->draggable);
		btc_gen_attr($this->output, 'hidden', $this->hidden);
		btc_gen_attr($this->output, 'lang', $this->lang);
		btc_gen_style_attr($this->output, $this->style);
		btc_gen_attr($this->output, 'tabindex', $this->tabindex);
		btc_gen_attr($this->output, 'title', $this->title);
	}

	protected function closeTag($endtag = true) {
		if ($endTag) {
			$this->output .= '</' . $this->tag_name . '>';
		} else {
			$this->output .= '>';
		}
	}
}

?>