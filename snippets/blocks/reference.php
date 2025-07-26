<?php
/**
 * @var \Kirby\Cms\Page $page
 * @var \Kirby\Cms\Site $site
 * @var \Kirby\Cms\Block $block
 */
// retrieve block by id
?>
<?php
$targetPage = site()->index(true)->find($block->targetPage());
$targetBlock = $block->targetBlock();

$fieldsToCheck = array_keys(array_filter($targetPage->blueprint()->fields(),
    fn($item) => in_array($item['type'], ['blocks', 'layout'])));
foreach ($fieldsToCheck as $fieldName) {
    $blocks = $targetPage->{$fieldName}()->toBlocks()->filterBy('id', $block->targetBlock());
    if ($blocks->count() > 0) {
        echo $blocks;
        break;
    }
}
?>

