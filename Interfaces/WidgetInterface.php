<?php
namespace exface\Core\Interfaces;

use exface\Core\Interfaces\DataSheets\DataSheetInterface;
use exface\Core\CommonLogic\Model\Object;
use exface\Core\CommonLogic\WidgetLink;
use exface\Core\Interfaces\Actions\ActionInterface;
use exface\Core\CommonLogic\WidgetDimension;
use exface\Core\CommonLogic\Model\RelationPath;
use exface\Core\Exceptions\Widgets\WidgetPropertyInvalidValueError;
use exface\Core\Exceptions\Widgets\WidgetConfigurationError;
use exface\Core\CommonLogic\UxonObject;
use exface\Core\Widgets\Container;

interface WidgetInterface extends ExfaceClassInterface, iCanBeCopied
{

    /**
     * Loads data from a standard object (stdClass) into any widget using setter functions.
     * E.g. calls $this->setId($source->id) for every property of the source object. Thus the behaviour of this
     * function like error handling, input checks, etc. can easily be customized by programming good
     * setters.
     *
     * @param \stdClass $source            
     */
    public function importUxonObject(\stdClass $source);

    /**
     * Returns the UXON description of the widget.
     * If the widget was described by a user, the original description
     * is returned. If the widget was built via API, a description is automatically generated.
     *
     * @return UxonObject
     */
    public function exportUxonObject();

    /**
     * Prefills the widget with values of a data sheet
     *
     * @triggers \exface\Core\Events\WidgetEvent [object_alias].Widget.Prefill.Before
     * @triggers \exface\Core\Events\WidgetEvent [object_alias].Widget.Prefill.After
     *
     * @param \exface\Core\Interfaces\DataSheets\DataSheetInterface $data_sheet            
     * @return void
     */
    function prefill(DataSheetInterface $data_sheet);

    /**
     * Adds attributes, filters, etc.
     * to a given data sheet, so that it can be used to fill the widget with data
     *
     * @param DataSheet $data_sheet            
     * @return DataSheetInterface
     */
    public function prepareDataSheetToRead(DataSheetInterface $data_sheet = null);

    /**
     * Adds attributes, filters, etc.
     * to a given data sheet, so that it can be used to prefill the widget
     *
     * @param DataSheet $data_sheet            
     * @return DataSheetInterface
     */
    public function prepareDataSheetToPrefill(DataSheetInterface $data_sheet = null);

    /**
     * Sets the widget caption/title
     *
     * @param string $caption            
     */
    public function setCaption($caption);

    /**
     * Returns the UID of the base meta object for this widget
     *
     * @return string
     */
    public function getMetaObjectId();

    /**
     *
     * @param string $id            
     */
    public function setMetaObjectId($id);

    /**
     * Returns the widget id specified for this widget explicitly (e.g.
     * in the UXON description). Returns NULL if there was no id
     * explicitly specified! Use get_id() instead, if you just need the currently valid widget id.
     *
     * @return string
     */
    public function getIdSpecified();

    /**
     * Returns the widget id generated automatically for this widget.
     * This is not neccesserily the actual widget id - if an id was
     * specified explicitly (e.g. in the UXON description), it will be used instead.
     * Use get_id() instead, if you just need the currently valid widget id.
     *
     * @return string
     */
    public function getIdAutogenerated();

    /**
     * Sets the autogenerated id for this widget
     *
     * @param string $value            
     * @return \exface\Core\Interfaces\WidgetInterface
     */
    public function setIdAutogenerated($value);

    /**
     * Specifies the id of the widget explicitly, overriding any previos values.
     * The given id must be unique
     * within the page. It will not be modified automatically in any way.
     *
     * @param string $value            
     * @return WidgetInterface
     */
    public function setId($value);

    /**
     * Retruns the id space of this widget.
     *
     * @return string
     */
    public function getIdSpace();

    /**
     * Sets the id space for this widget.
     * This means, all ids, links, etc. of it's children will
     * be resolved within this id space.
     *
     * The id space allows to reuse complex widgets with live references and other links multiple
     * times on a single page. A complex oject editor, for example, can be used by the create,
     * update and dublicate buttons on one page. To make the links within the editor work, each
     * button must have it's own id space.
     *
     * @param string $value            
     * @return WidgetInterface
     */
    public function setIdSpace($value);

    /**
     * Returns true if current widget is a container, false otherwise
     *
     * @return boolean
     */
    public function isContainer();

    /**
     *
     * @return string
     */
    public function getCaption();

    /**
     * Returns TRUE if the caption is supposed to be hidden
     *
     * @return boolean
     */
    public function getHideCaption();

    /**
     *
     * @param unknown $value            
     * @return WidgetInterface
     */
    public function setHideCaption($value);

    /**
     *
     * @throws WidgetConfigurationError
     * @return \exface\Core\CommonLogic\Model\Object
     */
    public function getMetaObject();

    /**
     * Sets the given object as the new base object for this widget
     *
     * @param Object $object            
     */
    public function setMetaObject(Object $object);

    /**
     * Returns the id of this widget
     *
     * @return string
     */
    public function getId();

    /**
     * Returns the widget type (e.g.
     * DataTable)
     *
     * @return string
     */
    public function getWidgetType();

    /**
     *
     * @return boolean
     */
    public function isDisabled();

    /**
     *
     * @param boolean $value            
     */
    public function setDisabled($value);

    /**
     * Returns a dimension object representing the height of the widget.
     *
     * @return WidgetDimension
     */
    public function getWidth();

    /**
     * Sets the width of the widget.
     * The width may be specified in relative ExFace units (in this case, the value is numeric)
     * or in any unit compatible with the current template (in this case, the value is alphanumeric because the unit must be
     * specified directltly).
     *
     * @param float|string $value            
     * @return WidgetInterface
     */
    public function setWidth($value);

    /**
     * Returns a dimension object representing the height of the widget.
     *
     * @return WidgetDimension
     */
    public function getHeight();

    /**
     * Sets the height of the widget.
     * The height may be specified in relative ExFace units (in this case, the value is numeric)
     * or in any unit compatible with the current template (in this case, the value is alphanumeric because the unit must be
     * specified directltly).
     *
     * @param float|string $value            
     * @return WidgetInterface
     */
    public function setHeight($value);

    /**
     *
     * @param string $qualified_alias_with_namespace            
     */
    public function setObjectAlias($qualified_alias_with_namespace);

    /**
     * Returns the relation path from the object of the parent widget to the object of this widget.
     * If both widgets are based on the
     * same object or no valid path can be found, an empty path will be returned.
     *
     * @return RelationPath
     */
    public function getObjectRelationPathFromParent();

    /**
     *
     * @param string $string            
     */
    public function setObjectRelationPathFromParent($string);

    /**
     * Returns the relation path from the object of this widget to the object of its parent widget.
     * If both widgets are based on the
     * same object or no valid path can be found, an empty path will be returned.
     *
     * @return RelationPath
     */
    public function getObjectRelationPathToParent();

    /**
     *
     * @param string $string            
     */
    public function setObjectRelationPathToParent($string);

    /**
     * Returns TRUE if the meta object of this widget was not set explicitly but inherited from it's parent and FALSE otherwise.
     *
     * @return boolean
     */
    public function isObjectInheritedFromParent();

    /**
     * Returns the parent widget
     *
     * @return WidgetInterface
     */
    public function getParent();

    /**
     * Sets the parent widget
     *
     * @param WidgetInterface $widget            
     */
    public function setParent(WidgetInterface $widget);

    /**
     * Returns the UI manager
     *
     * @return \exface\Core\ui
     */
    public function getUi();

    /**
     *
     * @return string
     */
    public function getHint();

    /**
     *
     * @param string $value            
     */
    public function setHint($value);

    /**
     *
     * @return boolean
     */
    public function isHidden();

    /**
     *
     * @param boolean $value            
     */
    public function setHidden($value);

    /**
     * Returns the current visibility option (one of the EXF_WIDGET_VISIBILITY_xxx constants)
     *
     * @return integer
     */
    public function getVisibility();

    /**
     * Sets visibility of the widget. 
     * 
     * Accepted values are either one of the EXF_WIDGET_VISIBILITY_xxx or the
     * the "xxx" part of the constant name as string: e.g. "normal", "promoted".
     *
     * @param string $value            
     * @throws WidgetPropertyInvalidValueError
     */
    public function setVisibility($value);

    /**
     * Returns the data sheet used to prefill the widget or null if the widget is not prefilled
     *
     * @return DataSheetInterface
     */
    public function getPrefillData();

    /**
     *
     * @param DataSheetInterface $data_sheet            
     */
    public function setPrefillData(DataSheetInterface $data_sheet);

    /**
     * Checks if the widget implements the given interface (e.g.
     * "iHaveChildren"), etc.
     *
     * @param string $interface_name            
     */
    public function implementsInterface($interface_name);

    /**
     * Returns TRUE if the widget is of the given widget type or extends from it and FALSE otherwise
     * (e.g.
     * a DataTable would return TRUE for DataTable and Data)
     *
     * @param string $widget_type            
     * @return boolean
     *
     * @see is_exactly()
     */
    public function is($widget_type);

    /**
     * Returns TRUE if the widget is of the given type and FALSE otherwise.
     * In contrast to is(), it will return FALSE even
     * if the widget extends from the given type.
     *
     * @param string $widget_type            
     * @return boolean
     *
     * @see is()
     */
    public function isExactly($widget_type);

    /**
     * Returns all actions callable from this widget or it's children as an array.
     * Optional filters can be used to
     * return only actions with a specified id (would be a single one in most cases) or qualified action alias (e.g. "exface.EditObjectDialog")
     *
     * @param string $qualified_action_alias            
     * @param string $action_type            
     * @return ActionInterface[]
     */
    public function getActions($qualified_action_alias = null, $action_id = null);

    /**
     * Returns aliases of attributes used to aggregate data
     * TODO Not sure, if this should be a method of the abstract widget or a specific widget type.
     * Can any widget have aggregators?
     *
     * @return array
     */
    public function getAggregations();

    /**
     * Explicitly tells the widget to use the given data connection to fetch data (instead of the one specified on the base
     * object's data source)
     *
     * @param string $value            
     */
    public function setDataConnectionAlias($value);

    /**
     * Creates a link to this widget and returns the corresponding model object
     *
     * @return WidgetLink
     */
    public function createWidgetLink();

    /**
     *
     * @return UiPageInterface
     */
    public function getPage();

    /**
     *
     * @return string
     */
    public function getPageId();
    
    /**
     * 
     * @return string
     */
    public function getPageAlias();

    /**
     * Returns the orignal UXON description of this widget specified by the user, that is without any automatic enhancements
     *
     * @return \exface\Core\CommonLogic\UxonObject|\exface\Core\CommonLogic\UxonObject
     */
    public function exportUxonObjectOriginal();
}
?>