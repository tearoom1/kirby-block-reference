<?php
/**
 * @var \Kirby\Cms\Page $page
 * @var \Kirby\Cms\Site $site
 * @var \Kirby\Cms\Block $block
 */
// retrieve block by id
?>
<?php
$targetPage = $site->find($block->targetPage());
$fieldsToCheck = array_keys(array_filter($targetPage->blueprint()->fields(),
    fn($item) => in_array($item['type'], ['blocks', 'layout'])));
foreach ($fieldsToCheck as $fieldName) {
    echo $targetPage->{$fieldName}()->toBlocks()->filterBy('id', $block->targetBlock());
    break;
}
?>

