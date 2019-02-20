<?php
/**
 * Created by PhpStorm.
 * User: d.balayan
 * Date: 14.02.19
 * Time: 15:29
 */
declare(strict_types=1);

namespace Realconnex\RequestStructure;

/**
 * Class EntityField
 * contains requested entity structure
 *
 * @package Realconnex
 */
class EntityField
{
    /** @var string */
    public $name;
    /** @var EntityField[] */
    public $fields;

    /**
     * EntityField constructor.
     *
     * @param string $name
     */
    public function __construct(string $name) {
        /// force validate name
        if (empty($name)) throw new \InvalidArgumentException('$name shouldn\'t blank!');

        $this->name = $name;
        $this->fields = [];
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name) : void
    {
        $this->name = $name;
    }

    /**
     * @return \Realconnex\RequestStructure\EntityField[]
     */
    public function getFields() : array
    {
        return $this->fields;
    }

    /**
     * @param \Realconnex\RequestStructure\EntityField[] $fields
     *
     * @return \Realconnex\RequestStructure\EntityField
     */
    public function setFields(array $fields)
    {
        /// force validate fields
        array_walk($fields, function($field) {
            if(!$field instanceof EntityField) {
                $givenClassName = get_class($field);
                $expectedClassName = EntityField::class;

                throw new \InvalidArgumentException(
                    "Fields should be instances of {$expectedClassName}, but {$givenClassName} given!"
                );
            }
        });

        $this->fields = $fields;

        return $this;
    }


}