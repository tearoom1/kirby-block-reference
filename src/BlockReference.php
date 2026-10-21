<?php

namespace TearoomOne\BlockReference;

use Kirby\Cms\Block;
use Kirby\Cms\Page;

/**
 * Finds referenceable blocks and renders reference blocks
 */
class BlockReference
{
    /**
     * Blocks of all blocks and layout fields of a page, except reference blocks.
     * Each block carries the name of its field as `parent`.
     */
    public static function blocksFromPage(Page $page): array
    {
        $allBlocks = [];

        foreach (self::blockFieldNames($page) as $fieldName) {
            $toArray = $page->{$fieldName}()->toBlocks()->toArray();
            foreach ($toArray as &$block) {
                $block['parent'] = $fieldName;
            }
            $allBlocks = array_merge($allBlocks, $toArray);
        }

        return array_values(array_filter($allBlocks, fn ($block) => $block['type'] !== 'reference'));
    }

    /**
     * Whether the current user may use the reference picker
     */
    public static function canAccess(): bool
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

    public static function canReadPage(Page $page): bool
    {
        return $page->permissions()->can('read') === true;
    }

    /**
     * IDs of all pages (drafts included) the current user may pick from
     */
    public static function pageIds(): array
    {
        if (!self::canAccess()) {
            return [];
        }

        $pages = [];
        foreach (site()->index(true) as $page) {
            if (self::canReadPage($page)) {
                $pages[] = $page->id();
            }
        }

        return $pages;
    }

    /**
     * Blocks of a page for the picker, empty without access
     */
    public static function blocksForPicker(mixed $pageId): array
    {
        if (!self::canAccess() || !is_string($pageId) || $pageId === '') {
            return [];
        }

        $page = site()->findPageOrDraft($pageId);
        if (!$page || !self::canReadPage($page)) {
            return [];
        }

        return self::blocksFromPage($page);
    }

    /**
     * HTML of the block a reference block points to. Blocks of drafts are only
     * shown to logged-in users, unless the renderDrafts option is enabled.
     */
    public static function render(Block $reference): string
    {
        $pageId = $reference->targetPage()->value();
        $blockId = $reference->targetBlock()->value();
        if (!$pageId || !$blockId) {
            return '';
        }

        $page = site()->findPageOrDraft($pageId);
        if (!$page) {
            return '';
        }

        if ($page->isDraft() && !kirby()->user() && option('tearoom1.kirby-block-reference.renderDrafts', false) !== true) {
            return '';
        }

        foreach (self::blockFieldNames($page) as $fieldName) {
            $blocks = $page->{$fieldName}()->toBlocks()->filterBy('id', $blockId);
            if ($blocks->count() > 0) {
                return (string)$blocks;
            }
        }

        return '';
    }

    /**
     * Names of the blocks and layout fields of a page
     */
    protected static function blockFieldNames(Page $page): array
    {
        return array_keys(array_filter(
            $page->blueprint()->fields(),
            fn ($item) => in_array($item['type'], ['blocks', 'layout'])
        ));
    }
}
