<?php
namespace exface\Core\Model;
class Relation{
	
	// TODO Make all private
	public $id;
	public $alias;
	public $name;
	private $main_object_id;
	private $related_object_id;
	private $related_object_key_attribute_id;
	private $related_object_key_alias;
	private $join_type = 'LEFT';
	private $foreign_key_alias;
	private $type = 'n1';
	private $inherited_from_object_id = null;
	
	/**
	 * 
	 * @param unknown $id
	 * @param unknown $alias
	 * @param unknown $name
	 * @param unknown $main_object
	 * @param unknown $foreign_key_alias
	 * @param unknown $related_object_id
	 * @param string $type 1n n1 or 11
	 */
	function __construct($id, $alias, $name, $main_object_id, $foreign_key_alias, $related_object_id, $related_object_key_attribute_id = null, $type = 'n1'){
		$this->id = $id;
		$this->alias = $alias;
		$this->name = $name;
		$this->main_object_id = $main_object_id;
		$this->foreign_key_alias = $foreign_key_alias;
		$this->related_object_id = $related_object_id;
		$this->related_object_key_attribute_id = $related_object_key_attribute_id;
		$this->type = $type;
	}
    
    function get_related_object(){
    	global $exface;
    	return $exface->model()->get_object($this->related_object_id, $this->get_alias());
    }
    
    public function get_id() {
      return $this->id;
    }
    
    public function set_id($value) {
      $this->id = $value;
    }
    
    public function get_alias() {
      return $this->alias;
    }
    
    public function set_alias($value) {
      $this->alias = $value;
    }
       
    public function get_name() {
      return $this->name;
    }
    
    public function set_name($value) {
      $this->name = $value;
    }
    
    public function get_related_object_id() {
      return $this->related_object_id;
    }
    
    public function set_related_object_id($value) {
      $this->related_object_id = $value;
    }
    
    /**
     * Returns the alias of the foreign key in the main object. E.g. for the relation ORDER->USER it would return USER_UID, which is a attribute of the object ORDER.
     * @return string
     */
    public function get_foreign_key_alias() {
      return $this->foreign_key_alias;
    }
    
    public function set_foreign_key_alias($value) {
      $this->foreign_key_alias = $value;
    }
    
    public function get_join_type() {
      return $this->join_type;
    }
    
    public function set_join_type($value) {
      $this->join_type = $value;
    }
    
    public function get_main_object() {
    	global $exface;
      return $exface->model()->get_object($this->main_object_id);
    }
    
    public function set_main_object(\exface\Core\Model\Object $obj) {
      $this->main_object_id = $obj->get_id();
    }
    
    public function get_type() {
    	return $this->type;
    }
    
    public function set_type($value) {
    	$this->type = $value;
    }
    
    /**
     * Returns the alias of the attribute, that identifies the related object in this relation. In most cases it is the UID
     * of the related object, but can also be another attribute.  
     * E.g. for the relation ORDER->USER it would return UID, which is the alias of the id attribute of the object USER.
     * @return string
     */
    public function get_related_object_key_alias() {
    	// If there is no special related_object_key_alias set, use the UID
    	if (!$this->related_object_key_alias){
    		if ($this->related_object_key_attribute_id){
    			$this->related_object_key_alias = $this->get_related_object()->get_attributes()->get_by_attribute_id($this->related_object_key_attribute_id)->get_alias();
    		} else {
    			$this->related_object_key_alias = $this->get_related_object()->get_uid_alias();
    		}
    	}
    	return $this->related_object_key_alias;
    }
    
    public function set_related_object_key_alias($value) {
    	$this->related_object_key_alias = $value;
    }
    
    public function get_main_object_key_attribute(){
    	return $this->get_main_object()->get_attribute($this->get_foreign_key_alias());
    }
    
    public function get_related_object_key_attribute(){
    	return $this->get_related_attribute($this->get_related_object_key_alias());
    	// Backup of an old version, that returned an attribute withou a relation path
    	// return $this->get_related_object()->get_attribute($this->get_related_object_key_alias());
    }
    
    public function get_inherited_from_object_id() {
    	return $this->inherited_from_object_id;
    }
    
    public function set_inherited_from_object_id($value) {
    	$this->inherited_from_object_id = $value;
    }  
	
    /**
     * Returns a related attribute as if it was queried via $object->get_attribute("this_relation_alias->attribute_alias").
     * An attribute returned by this function has a relation path relative to the main object of this relation!
     * @param string $attribute_alias
     * @return \exface\Core\Model\attribute
     */
    public function get_related_attribute($attribute_alias){
    	return $this->get_main_object()->get_attribute(RelationPath::relation_path_add($this->get_alias(), $attribute_alias));
    }
    
    /**
     * Returns the relation in the opposite direction: ORDER->POSITION will become POSITION->ORDER
     * @return \exface\Core\Model\relation | boolean
     */
    public function get_reversed_relation(){
    	if ($this->get_type() == 'n1'){
    		// If it is a regular relation, it will be a reverse one from the point of view of the related object. That is identified by the
    		// alias of the object it leads to (in our case, the current object)
    		$reverse = $this->get_related_object()->get_relation($this->get_main_object()->get_alias(), $this->get_alias());
    	} elseif ($this->get_type() == '1n' || $this->get_type() == '11'){
    		// If it is a reverse relation, it will be a regular one from the point of view of the related object. That is identified by its alias.
    		// TODO Will it also work for one-to-one relations?
    		$reverse = $this->get_related_object()->get_relation($this->get_foreign_key_alias());
    	} else {
    		$reverse = false;
    	}
    	return $reverse;
    }
    
    /**
     * Clones the attribute keeping the model and object
     */
    public function copy(){
    	return $this->get_main_object()->exface()->utils()->deep_copy($this, array('model'));
    }
}
?>