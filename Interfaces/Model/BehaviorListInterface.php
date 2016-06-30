<?php namespace exface\Core\Interfaces\Model;

use exface\Core\Interfaces\iCanBeConvertedToUxon;
use exface\Core\Model\Object;
use exface\Core\Interfaces\EntityListInterface;

interface BehaviorListInterface extends iCanBeConvertedToUxon, \IteratorAggregate {
	
	/**
	 * @return Object
	 */
	public function get_object();
	
	/**
	 *
	 * @param Object $value
	 * @return BehaviorListInterface
	 */
	public function set_object(Object $value);
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \exface\Core\Interfaces\EntityListInterface::get_all()
	 * @return BehaviorInterface[]
	 */
	public function get_all();
	
	/**
	 * {@inheritDoc}
	 * @see \exface\Core\Interfaces\EntityListInterface::remove()
	 * @param BehaviorInterface $entity
	 * @return BehaviorListInterface
	 */
	public function remove($entity);
	
	/**
	 * Returns the current number of entities in the list.
	 * @return integer
	 */
	public function count();
	
	/**
	 * Returns TRUE, if the list is empty and FALSE otherwise
	 * @return boolean
	 */
	public function is_empty();
	
	/**
	 * Uses the given array of UXON objects to populate the entity list. Each UXON object in the array
	 * will be instantiated and added to the list.
	 * @param array $uxon
	 * @return void
	 */
	public function import_uxon_array(array $uxon);
	  
}
?>