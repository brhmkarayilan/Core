<?php namespace exface\Apps\exface\Core\Formulas;

/**
 * Shows weather icon based on yahoo condition code (e.g. 28 = cloudy).
 * @author aka
 *
 */
class Weathericon extends \exface\Core\Model\Formula {
	
	function run($condition_code){
		global $exface;
		if (!$condition_code) return '';
		$return = '<img src="' . $exface->get_config_value('path_to_images') . '/weather/' . $condition_code . '.png" />';
        return $return;
	}
}
?>