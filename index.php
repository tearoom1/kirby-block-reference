<?php

/**
 * @param \Kirby\Cms\Page $page
 * @return array
 */
function getBlocksFromPage(\Kirby\Cms\Page $page): array
{
    $allBlocks = [];
    $fieldsToCheck = array_keys(array_filter($page->blueprint()->fields(),
        fn($item) => in_array($item['type'], ['blocks', 'layout'])));
    foreach ($fieldsToCheck as $fieldName) {
        $toArray = $page->{$fieldName}()->toBlocks()->toArray();
        foreach ($toArray as &$block) {
            $block['parent'] = $fieldName;
        }
        $allBlocks = array_merge($allBlocks, $toArray);
    }
    // remove all blocks with type "reference"
    return array_values(array_filter($allBlocks, function ($block) {
        return $block['type'] !== 'reference';
    }));
}

Kirby::plugin('tearoom1/kirby-block-reference', [
    'blueprints' => [
        'blocks/reference' => __DIR__ . '/blueprints/blocks/reference.yml'
    ],
    'snippets' => [
        'blocks/reference' => __DIR__ . '/snippets/blocks/reference.php'
    ],
    'fields' => [
        'blockReference' => [
            'extends' => 'select'
        ]
    ],
    'api' => [
        'routes' => [
            [
                'pattern' => 'getAllPages',
                'action' => function () {
                    return array_keys(site()->index()->toArray());
                }
            ],
            [
                'pattern' => 'blocks',
                'action' => function () {
                    $pageId = get('page');
                    $page = site()->index(true)->findBy('id', $pageId);
                    if (!$page) {
                        return [];
                    }
                    return getBlocksFromPage($page);
                }
            ]
        ]
    ],
]);
