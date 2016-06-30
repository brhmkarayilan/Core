<?php
namespace exface\Widgets;
use exface\Core\Interfaces\Widgets\iAmClosable;
class Dialog extends Panel implements iAmClosable {
	protected $lazy_loading = true; // The contents of the dialog can be loaded via AJAX by default (if the template supports it)
	protected $button_class = 'DialogButton';
	private $hide_close_button = false;
	private $close_button = null;
	private $maximizable = true;
	private $maximized = false;
	
	protected function init(){
		if (count($this->get_widgets()) == 0){
			$form = $this->get_page()->create_widget('Form', $this);
			parent::add_widget($form);
		}
	}
	
	/**
	 * Adds a widget to the dialog. Input widgets are wrapped in a form automatically,
	 * while containers of all sort are added directly to the dialog.
	 * 
	 * FIXME As such, a form containing other containers should not be a problem,
	 * however this does not work in jEasyUI: tabs within a form are not displayed properly.
	 * Perhaps relying on forms for input dialogs is not a good idea in general. Should
	 * make own serializers in JS to avoid such kind of problems and include all children
	 * of the widget - regardless of weather they are compatible to HTML forms or not. 
	 * 
	 * @see Panel::add_widget()
	 */
	public function add_widget(AbstractWidget $widget, $position = NULL){
		if ($widget instanceof Container){
			parent::add_widget($widget, $position);
		} else {
			$form = $this->get_widgets()[0];
			$form->add_widget($widget, $position);
		}
	}
	
	/**
	 * Sets the caption of the close button. A dialog always has a close button, but it can
	 * be renamed dependig on the context of the dialog.
	 * @param string $value
	 */
	public function set_close_button_caption($value){
		$this->get_close_button()->set_caption($value);
	}
	
	/**
	 * If TRUE, the automatically generated close button for the dialog is not shown
	 * @return boolean
	 */
	public function get_hide_close_button() {
		return $this->hide_close_button;
	}
	
	/**
	 * If set to TRUE, the automatically generated close button will not be shown in this dialog
	 * @param boolean $value
	 */
	public function set_hide_close_button($value) {
		$this->hide_close_button = $value;
	}  
	
	/**
	 * Returns a special dialog button, that just closes the dialog without doing any other action
	 * @return \exface\Widgets\DialogButton
	 */
	public function get_close_button(){
		if (!($this->close_button instanceof DialogButton)) {
			/* @var $btn DialogButton */
			$btn = $this->get_page()->create_widget('DialogButton', $this);
			$btn->set_close_dialog_after_action_succeeds(true);
			$btn->set_refresh_input(false);
			$btn->set_icon_name('cancel');
			if ($this->get_hide_close_button()) $btn->set_hidden(true);
			$this->close_button = $btn;
		}
		return $this->close_button;
	}
	
	/**
	 * Returns an array of dialog buttons. The close button is always added to the end of the button list.
	 * This ensures, that the other buttons can be rearranged without an impact on the close buttons last
	 * position.
	 * @see \exface\Widgets\Panel::get_buttons()
	 */
	public function get_buttons(){
		$btns = parent::get_buttons();
		$btns[] = $this->get_close_button();
		return $btns;
	}
	
	/**
	 * Returns the widgets the dialog contains. It is just a better readable alias for get_widgets().
	 * @return AbstractWidget[]
	 */
	public function get_contents(){
		return $this->get_widgets();
	}
	
	public function get_maximizable(){
		return $this->maximizable;
	}
	
	public function set_maximizable($value) {
		$this->maximizable = $value;
		return $this;
	}
	
	public function get_maximized() {
		return $this->maximized;
	}
	
	public function set_maximized($value) {
		$this->maximized = $value;
		return $this;
	}  
}
?>