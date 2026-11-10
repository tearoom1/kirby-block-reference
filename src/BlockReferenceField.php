<?php

namespace TearoomOne\BlockReference;

use Kirby\Form\Field\SelectField;

/**
 * Select field whose options (the blocks of the selected target page)
 * are loaded by the Panel component, so the server has no option list to validate against
 */
class BlockReferenceField extends SelectField
{
    protected function validations(): array
    {
        return [];
    }
}
