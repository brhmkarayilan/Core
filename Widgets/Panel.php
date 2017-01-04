<?php namespace exface\Core\Widgets;

use exface\Core\Interfaces\Widgets\iHaveButtons;
use exface\Core\Interfaces\Widgets\iCollapsible;
use exface\Core\Interfaces\Widgets\iHaveIcon;
use exface\Core\Interfaces\Widgets\iSupportLazyLoading;
use exface\Core\CommonLogic\UxonObject;
use exface\Core\Interfaces\Widgets\iFillEntireContainer;

class Panel extends Container implements iSupportLazyLoading, iHaveButtons, iHaveIcon, iCollapsible, iFillEntireContainer {
	
	private $lazy_loading = false; // A panel will not be loaded via AJAX by default
	private $lazy_loading_action = 'exface.Core.ShowWidget';
	private $button_widget_type = 'Button'; // Which type of Buttons should be used. Can be overridden by inheriting widgets
	private $collapsible = false;
	private $buttons =  array();
	private $icon_name = null;
	private $column_number = null;
	private $column_stack_on_smartphones = null;
	private $column_stack_on_tablets = null;
	
	public function get_collapsible() {
		return $this->collapsible;
	}
	
	public function set_collapsible($value) {
		$this->collapsible = $value;
	}    
	
	/**
	 * (non-PHPdoc)
	 * @see \exface\Core\Interfaces\Widgets\iHaveButtons::get_buttons()
	 * @return Button[]
	 */
	public function get_buttons() {
		return $this->buttons;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \exface\Core\Interfaces\Widgets\iHaveButtons::set_buttons()
	 */
	public function set_buttons(array $buttons_array) {
		if (!is_array($buttons_array)) return false;
		foreach ($buttons_array as $b){
			$button = $this->get_page()->create_widget($this->get_button_widget_type(), $this, UxonObject::from_anything($b));
			$this->add_button($button);
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \exface\Core\Interfaces\Widgets\iHaveButtons::add_button()
	 */
	public function add_button($button_widget){
		$button_widget->set_parent($this);
		$button_widget->set_meta_object_id($this->get_meta_object()->get_id());
		
		// If the button has an action, that is supposed to modify data, we need to make sure, that the panel
		// contains alls system attributes of the base object, because they may be needed by the business logic
		if ($button_widget->get_action() && $button_widget->get_action()->implements_interface('iModifyData')){
			/* @var $attr \exface\Core\CommonLogic\Model\Attribute */
			foreach ($this->get_meta_object()->get_attributes()->get_system() as $attr){
				if (count($this->find_children_by_attribute($attr)) <= 0){
					$widget = $this->get_page()->create_widget('InputHidden', $this);
					$widget->set_attribute_alias($attr->get_alias());
					if ($attr->is_uid_for_object()){
						$widget->set_aggregate_function(EXF_AGGREGATOR_LIST);
					} else {
						$widget->set_aggregate_function($attr->get_default_aggregate_function());
					}
					$this->add_widget($widget);
				}
			}
		}
		
		$this->buttons[] = $button_widget;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \exface\Core\Interfaces\Widgets\iHaveButtons::remove_button()
	 */
	public function remove_button(Button $button_widget){
		if(($key = array_search($button_widget, $this->buttons)) !== false) {
			unset($this->buttons[$key]);
		}
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \exface\Core\Interfaces\Widgets\iHaveIcon::get_icon_name()
	 */
	public function get_icon_name() {
		return $this->icon_name;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \exface\Core\Interfaces\Widgets\iHaveIcon::set_icon_name()
	 */
	public function set_icon_name($value) {
		$this->icon_name = $value;
	}

	/**
	 * (non-PHPdoc)
	 * @see \exface\Core\Interfaces\Widgets\iSupportLazyLoading::get_lazy_loading()
	 */
	public function get_lazy_loading() {
		return $this->lazy_loading;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \exface\Core\Interfaces\Widgets\iSupportLazyLoading::set_lazy_loading()
	 */
	public function set_lazy_loading($value) {
		$this->lazy_loading = $value;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \exface\Core\Interfaces\Widgets\iSupportLazyLoading::get_lazy_loading_action()
	 */
	public function get_lazy_loading_action() {
		return $this->lazy_loading_action;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \exface\Core\Interfaces\Widgets\iSupportLazyLoading::set_lazy_loading_action()
	 */
	public function set_lazy_loading_action($value) {
		$this->lazy_loading_action = $value;
		return $this;
	} 
	
	/**
	 * Returns the class of the used buttons. Regular panels and forms use ordinarz buttons, but
	 * Dialogs use special DialogButtons capable of closing the Dialog, etc. This special getter
	 * function allows all the logic to be inherited from the panel while just replacing the
	 * button class.
	 * @return string
	 */
	public function get_button_widget_type(){
		return $this->button_widget_type;
	}
	
	/**
	 * 
	 * @param string $string
	 * @return \exface\Core\Widgets\Panel
	 */
	public function set_button_widget_type($string){
		$this->button_widget_type = $string;
		return $this;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \exface\Core\Widgets\Container::get_children()
	 */
	public function get_children(){		
		return array_merge(parent::get_children(), $this->get_buttons());
	}
	
	public function get_column_number() {
		return $this->column_number;
	}
	
	public function set_column_number($value) {
		$this->column_number = $value;
		return $this;
	} 
	
	/**
	 * Returns TRUE if the columns should be stacked on small screens and FALSE otherwise. Returns NULL if the creator of the widget
	 * had made no preference and thus the stacking is completely upto the template.
	 * @return boolean
	 */
	public function get_column_stack_on_smartphones() {
		return $this->column_stack_on_smartphones;
	}
	
	/**
	 * Determines wether columns should be stacked on smaller screens (TRUE) or left side-by-side (FALSE). Setting this to NULL will
	 * leave it upto the template to decide.
	 * @param boolean $value
	 */
	public function set_column_stack_on_smartphones($value) {
		$this->column_stack_on_smartphones = $value;
		return $this;
	}  
	
	/**
	 * Returns TRUE if the columns should be stacked on midsize screens and FALSE otherwise. Returns NULL if the creator of the widget
	 * had made no preference and thus the stacking is completely upto the template.
	 * @return boolean
	 */
	public function get_column_stack_on_tablets() {
		return $this->column_stack_on_tablets;
	}
	
	/**
	 * Determines wether columns should be stacked on midsize screens (TRUE) or left side-by-side (FALSE). Setting this to NULL will
	 * leave it upto the template to decide.
	 * @param boolean $value
	 */
	public function set_column_stack_on_tablets($value) {
		$this->column_stack_on_tablets = $value;
		return $this;
	} 
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \exface\Core\Interfaces\Widgets\iHaveButtons::has_buttons()
	 */
	public function has_buttons() {
		if (count($this->buttons)) return true;
		else return false;
	}
	
	/**
	 * {@inheritDoc}
	 * 
	 * If the parent widget of a panel has other children (siblings of the panel), they should be moved to the panel itself, once it is
	 * added to it's paren.
	 * 
	 * @see \exface\Core\Interfaces\Widgets\iFillEntireContainer::get_alternative_container_for_orphaned_siblings()
	 * @return Panel
	 */
	public function get_alternative_container_for_orphaned_siblings(){
		return $this;
	}
}
?>