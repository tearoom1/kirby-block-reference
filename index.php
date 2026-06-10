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

function blockReferenceCanAccess(): bool
{
    $user = kirby()->user();
    if (!$user) {
        return false;
    }

    if ($user->isAdmin()) {
        return true;
    }

    $allowed = option('tearoom1.kirby-block-reference.allowedRoles', []);
    if (!is_array($allowed) || empty($allowed)) {
        return false;
    }

    return in_array($user->role()->name(), $allowed, true);
}

function blockReferenceCanReadPage(\Kirby\Cms\Page $page): bool
{
    return $page->permissions()->can('read') === true;
}

Kirby::plugin('tearoom1/kirby-block-reference', [
    'options' => [
        'allowedRoles' => [],
    ],
    'pageMethods' => [
        'blockReferenceCanRead' => function (): bool {
            return blockReferenceCanAccess() && blockReferenceCanReadPage($this);
        },
    ],
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
                'auth' => true,
                'action' => function () {
                    if (!blockReferenceCanAccess()) {
                        return [];
                    }

                    $pages = [];
                    foreach (site()->index(true) as $page) {
                        if (blockReferenceCanReadPage($page)) {
                            $pages[] = $page->id();
                        }
                    }

                    return $pages;
                }
            ],
            [
                'pattern' => 'blocks',
                'auth' => true,
                'action' => function () {
                    if (!blockReferenceCanAccess()) {
                        return [];
                    }

                    $pageId = get('page');
                    if (!is_string($pageId) || $pageId === '') {
                        return [];
                    }

                    $page = site()->index(true)->findBy('id', $pageId);
                    if (!$page || !blockReferenceCanReadPage($page)) {
                        return [];
                    }

                    return getBlocksFromPage($page);
                }
            ]
        ]
    ],
]);
