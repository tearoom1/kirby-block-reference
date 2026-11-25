<?php

namespace TearoomOne\BlockReference\Tests;

use Kirby\Form\Field;
use Kirby\Form\Field\SelectField;
use TearoomOne\BlockReference\BlockReferenceField;

class BlockReferenceFieldTest extends TestCase
{
    public function testFieldTypeIsRegisteredAsClass(): void
    {
        $this->app();

        $class = Field::resolve('blockReference');

        $this->assertSame(BlockReferenceField::class, $class);
        $this->assertTrue(is_subclass_of($class, SelectField::class));
    }

    public function testBlockIdIsAcceptedWithoutServerSideOptions(): void
    {
        $this->app();

        // the options only exist in the Panel, a plain select would reject the value
        $field = new BlockReferenceField(name: 'targetBlock');
        $field->fill('some-block-id');

        $this->assertSame([], $field->errors());
        $this->assertSame('some-block-id', $field->toStoredValue());
    }

    public function testRequiredIsStillChecked(): void
    {
        $this->app();

        $field = new BlockReferenceField(name: 'targetBlock', required: true);

        $this->assertArrayHasKey('required', $field->errors());
    }
}
