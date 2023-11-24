<?php
use srag\Plugins\UdfEditor\Exception\UDFNotFoundException;
class xudfContentElement extends ActiveRecord
{

    const DB_TABLE_NAME = 'xudf_element';
    /**
     * @return string
     */
    public function getConnectorContainerName(): string
    {
        return self::DB_TABLE_NAME;
    }
    /**
     *
     */
    public function create(): void
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
    public static function find($primary_key, array $add_constructor_args = array()): xudfContentElement
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
    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * @param int $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }


    /**
     * @return int
     */
    public function getObjId(): int
    {
        return $this->obj_id;
    }


    /**
     * @param int $obj_id
     */
    public function setObjId($obj_id): void
    {
        $this->obj_id = $obj_id;
    }


    /**
     * @return int
     */
    public function getSort(): int
    {
        return $this->sort;
    }


    /**
     * @param int $sort
     */
    public function setSort($sort): void
    {
        $this->sort = $sort;
    }


    /**
     * @return bool
     */
    public function isSeparator(): bool
    {
        return $this->is_separator;
    }


    /**
     * @param bool $is_separator
     */
    public function setIsSeparator($is_separator): void
    {
        $this->is_separator = $is_separator;
    }


    /**
     * @return int
     */
    public function getUdfFieldId(): int
    {
        return $this->udf_field;
    }


    /**
     * @param int $udf_field
     */
    public function setUdfFieldId($udf_field): void
    {
        $this->udf_field = $udf_field;
    }


    /**
     * @return array
     * @throws UDFNotFoundException
     */
    public function getUdfFieldDefinition(): array
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
    public function getTitle(): string
    {
        if (!$this->isSeparator()) {
            return $this->getUdfFieldDefinition()['field_name'];
        }

        return $this->title;
    }


    /**
     * @param String $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }


    /**
     * @return String
     */
    public function getDescription(): string
    {
        return $this->description;
    }


    /**
     * @param String $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }


    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return (bool) $this->is_required;
    }


    /**
     * @param bool $is_required
     */
    public function setIsRequired($is_required): void
    {
        $this->is_required = $is_required;
    }
}