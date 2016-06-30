<?php namespace exface\Core\Actions;

use exface\Core\Widgets\Dialog;

class MassEditDialog extends ShowDialog {
	private $affected_counter_widget_id = NULL;
	
	protected function init(){
		$this->set_input_rows_min(null);
		$this->set_input_rows_max(null);
		$this->set_icon_name('mass-edit');
	}
	
	public function set_input_data_sheet($data_sheet){
		$result = parent::set_input_data_sheet($data_sheet);
		$data_sheet = $this->get_input_data_sheet();
		if($this->get_widget()){
			$this->get_widget()->set_caption(intval($data_sheet->count_rows()));
			if ($counter = $this->get_widget()->find_child_recursive($this->get_affected_counter_widget_id())){
				$counter->set_text($this->get_affected_counter_text());
			}
		}
		return $result;
	}
	
	protected function enhance_dialog_widget(Dialog $dialog){
		// Add a message widget that displays what exactly we are editing here
		$counter_widget = $this->get_called_on_ui_page()->create_widget('Message', $dialog);
		$this->set_affected_counter_widget_id($counter_widget->get_id());
		$counter_widget->set_caption('Affected objects');
		$counter_widget->set_text($this->get_affected_counter_text());
		$dialog->add_widget($counter_widget, 0);
		// TODO Add a default save button that uses filter contexts 
		return parent::enhance_dialog_widget($dialog);
	}
	
	protected function get_affected_counter_text(){
		if ($this->get_input_data_sheet()){
			if ($this->get_input_data_sheet()->count_rows()){
				return 'Editing ' . ($this->get_input_data_sheet() ? $this->get_input_data_sheet()->count_rows() : 0) . ' objects';
			} else {
				$filters = array();
				$filter_conditions = array_merge($this->get_input_data_sheet()->get_filters()->get_conditions(), $this->get_app()->exface()->context()->get_scope_window()->get_filter_context()->get_conditions($this->get_input_data_sheet()->get_meta_object()));
				if (is_array($filter_conditions) && count($filter_conditions) > 0){
					foreach ($filter_conditions as $cond){
						$filters[$cond->get_expression()->to_string()] = $cond->get_expression()->get_attribute()->get_name() . ' ' . $cond->get_comparator() . ' ' . $cond->get_value();
					}
					return 'Editing all objects matching the following filters: ' . implode($filters, ' AND ');
				} else {
					return 'Editing all objects';
				}
				
			}
		}
	}
	
	public function get_affected_counter_widget_id() {
		return $this->affected_counter_widget_id;
	}
	
	public function set_affected_counter_widget_id($value) {
		$this->affected_counter_widget_id = $value;
		return $this;
	}  
	
	protected function prefill_widget(){
		$result = parent::prefill_widget();
		// This is a bit of a hack: we need the panel to hold system attributes of multiple rows (UIDs in this case). So we just
		// look for the UID widget and override it's value _after_ the normal prefill was done.
		// TODO Perhaps it would be a good idea to save lists of system attributes in the panel by default. Maybe by using a special
		// widget like "InputHiddenList" which would prefill itself by all rows, not just the first one...
		foreach ($this->get_dialog_widget()->get_widgets()[0]->get_widgets() as $widget){
			if ($widget->get_attribute_alias() == $this->get_input_data_sheet()->get_uid_column()->get_attribute_alias()){
				$widget->set_value(implode(',', $this->get_input_data_sheet()->get_uid_column()->get_values(false)));
			}
		}
		return $result;
	}
}
?>