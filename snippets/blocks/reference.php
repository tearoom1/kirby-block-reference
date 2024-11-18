<?php
/**
 * @var \Kirby\Cms\Page $page
 * @var \Kirby\Cms\Site $site
 * @var \Kirby\Cms\Block $block
 */
// retrieve block by id
?>
<?= $site->find($block->targetPage())->layout()->toLayouts()->toBlocks()->filterBy('id', $block->targetBlock()); ?>
