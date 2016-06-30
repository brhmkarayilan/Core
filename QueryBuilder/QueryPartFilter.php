<?php
namespace exface\Core\QueryBuilder;
use exface\Core\Model\Condition;
use exface\Core\Model\RelationPath;
/**
 * The filter query part represents one filter within a query (in SQL it translates to a WHERE-statement). Filter query parts
 * implement the general filter interface and thus can be aggregated to filter groups with logical operators like AND, OR, etc.
 * @author aka
 *
 */
class QueryPartFilter extends QueryPartAttribute {
	private $compare_value = null;
	private $comparator = null;
	private $condition = NULL;
	
	function __construct($alias, AbstractQueryBuilder &$query){
		parent::__construct($alias, $query);
		// If we filter over an attribute, which actually is a reverse relation, we need to explicitly tell the query, that
		// it is a relation and not a direct attribute. Concider the case of CUSTOMER<-CUSTOMER_CARD. If we filter CUSTOMERs over 
		// CUSTOMER_CARD, it would look as if the CUSTOMER_CARD is an attribute of CUSTOMER. We need to detect this and transform
		// the filter into CUSTOMER_CARD__UID, which would clearly be a relation.
		if ($this->get_attribute()->is_relation() && $this->get_query()->get_main_object()->get_relation($alias)->get_type() == '1n'){
			$attr = $this->get_query()->get_main_object()->get_attribute(RelationPath::relation_path_add($alias, $this->get_attribute()->get_object()->get_uid_alias()));
			$this->set_attribute($attr);
		}
	}
	
	public function get_compare_value() {
		if (!$this->compare_value) $this->compare_value = $this->get_condition()->get_value();
		return $this->compare_value;
	}
	
	public function set_compare_value($value) {
		$this->compare_value = trim($value);
	}
	
	public function get_comparator() {
		if (!$this->comparator) $this->comparator = $this->get_condition()->get_comparator();
		return $this->comparator;
	}
	
	public function set_comparator($value) {
		$this->comparator = $value;
	}

	public function get_condition() {
		return $this->condition;
	}
	
	public function set_condition(Condition $condition) {
		$this->condition = $condition;
	}  
}
?>