<?php

declare(strict_types=1);

use Realconnex\RequestStructure\EntityField;

/**
 * Class EntityFieldTest
 */
class EntityFieldTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Checks is entity field structure could be created
     * @dataProvider getValidEntityFieldStructures
     *
     * @dat
     *
     * @param $field
     */
    public function testEntityFieldStructureCreateSuccess($field)
    {
        $entityFieldStructure = $this->makeEntityField($field);
        $this->assertNotEmpty($entityFieldStructure, 'Cannot create entity field structure object!');
    }

    public function getValidEntityFieldStructures()
    {
        return [
            [['name' => 'myField', 'fields' => []]],
            [['name' => 'mf', 'fields' => [['name' => 'innerF', ]]]],
        ];
    }

    /**
     * Checks is entity field structure could be created
     * @dataProvider getInValidEntityFieldStructures
     *
     * @dat
     *
     * @param $field
     */
    public function testEntityFieldStructureCreateFail($field)
    {
        $this->expectException(InvalidArgumentException::class);
        new EntityField('123', [new stdClass()]);
    }

    public function getInValidEntityFieldStructures()
    {
        return [
            [['name' => 'myField', 'fields' => []]],
            [['name' => 'mf', 'fields' => [['name' => 'innerF', ]]]],
        ];
    }

    protected function makeEntityField(array $childField)
    {
        $fields = isset($childField['fields']) ? array_map([$this, 'makeEntityField'], $childField['fields']) : [];
        $name = $childField['name'] ?? '';

        return new EntityField($name, $fields);
    }
}
