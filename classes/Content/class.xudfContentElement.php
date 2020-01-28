<?php

use srag\Plugins\UdfEditor\Exception\UDFNotFoundException;

/**
 * Class xudfContentElement
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class xudfContentElement extends ActiveRecord
{

    const DB_TABLE_NAME = 'xudf_element';


    /**
     * @return string
     */
    public function getConnectorContainerName()
    {
        return self::DB_TABLE_NAME;
    }


    /**
     *
     */
    public function create()
    {
        $element = self::orderBy('sort')->first();
        $sort = $element ? ($element->getSort() + 10) : 10;
        $this->setSort($sort);
        parent::create();
    }


    /**
     * @param       $primary_key
     * @param array $add_constructor_args
     *
     * @return self
     */
    public static function find($primary_key, array $add_constructor_args = array())
    {
        return parent::find($primary_key, $add_constructor_args);
    }


    /**
     * @var int
     *
     * @con_has_field    true
     * @con_sequence     true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     * @con_is_primary   true
     */
    protected $id;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $obj_id;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     */
    protected $sort;
    /**
     * @var bool
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     */
    protected $is_separator = false;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     */
    protected $udf_field;
    /**
     * @var String
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_length       256
     */
    protected $title;
    /**
     * @var String
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_length       256
     */
    protected $description;
    /**
     * @var bool
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     */
    protected $is_required = false;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * @return int
     */
    public function getObjId()
    {
        return $this->obj_id;
    }


    /**
     * @param int $obj_id
     */
    public function setObjId($obj_id)
    {
        $this->obj_id = $obj_id;
    }


    /**
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }


    /**
     * @param int $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }


    /**
     * @return bool
     */
    public function isSeparator()
    {
        return $this->is_separator;
    }


    /**
     * @param bool $is_separator
     */
    public function setIsSeparator($is_separator)
    {
        $this->is_separator = $is_separator;
    }


    /**
     * @return int
     */
    public function getUdfFieldId()
    {
        return $this->udf_field;
    }


    /**
     * @param int $udf_field
     */
    public function setUdfFieldId($udf_field)
    {
        $this->udf_field = $udf_field;
    }


    /**
     * @return array
     * @throws UDFNotFoundException
     */
    public function getUdfFieldDefinition()
    {
        $definition = ilUserDefinedFields::_getInstance()->getDefinition($this->getUdfFieldId());
        if (!is_array($definition) || empty($definition)) {
            throw new UDFNotFoundException('udf with id ' . $this->getUdfFieldId() . ' could not be found and was probably deleted');
        }

        return $definition;
    }


    /**
     * @return String
     * @throws UDFNotFoundException
     */
    public function getTitle()
    {
        if (!$this->isSeparator()) {
            return $this->getUdfFieldDefinition()['field_name'];
        }

        return $this->title;
    }


    /**
     * @param String $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }


    /**
     * @return String
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * @param String $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }


    /**
     * @return bool
     */
    public function isRequired()
    {
        return (bool) $this->is_required;
    }


    /**
     * @param bool $is_required
     */
    public function setIsRequired($is_required)
    {
        $this->is_required = $is_required;
    }
}