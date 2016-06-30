<?php namespace exface\Apps\exface\Core\Formulas;

/**
 * Creates an HTML-Progressbar for object states in alexa.
 * @author aka
 *
 */
class alexastate extends \exface\Core\Model\Formula {
	
	function run($state, $substate, $object){
		if (!$state) return '';
		$return = '<div style="width:100%;border:1px solid #ccc;position:relative;color:transparent; padding-left:3px;">' . $this->get_state_description($state, $substate, $object) .
    			'<div style="position:absolute; left:0; top:0; z-index:100; width:' . ($state ? $state / 99 * 100 : 0) . '%;' . $this->get_style($state, $substate, $object) . '">&nbsp;</div>' .
				'<div style="position:absolute; left:0; top:0; z-index:101; color:black; padding:0 3px; width:100%">' . $this->get_state_description($state, $substate, $object) . '</div>' .
    			'</div>';
        return $return;
	}
	
	function get_style($state, $substate, $object){
		$colors['30.10'] = 'background-color:red;';
		$colors['30.17'] = 'background-color:#E0F906;';
		$colors['90'] = 'background-color:#ddd;';
		$colors['99'] = 'background-color:#ddd;';
		
		$special_style = $colors[$state . ($substate ? '.'.$substate : '')];
		if ($special_style) return $special_style;
		return 'background-color:#BFD297;';
	}
	
	function get_state_description($state, $substate, $object){
		$states = array();
		$states['CONTRACT'] = array(
				'10' => 'Angelegt',
				'20' => 'Freigegeben',
				'30.10' => 'Zur Prüfung',
				'30.16'=> 'Geprüft',
				'30.17' => 'Zahlung offen',
				'30.20' => 'Bestätigt',
				'30.30' => 'Unterwegs',
				'90' => 'Storniert',
				'99' => 'Abgeschlossen'
		);
		$states['CAMPAIGN'] = array(
			'10' => 'Angelegt',
			'20' => 'Freigegeben',
			'50' => 'Aktiv',
			'99' => 'Abgeschlossen'
		);
		$state = $state . ($substate ? '.'.$substate : '');
		return $state . ' ' . $states[$object][$state];
	}
}
?>