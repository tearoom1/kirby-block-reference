<?php

namespace TearoomOne\BlockReference\Tests;

/**
 * Current behaviour of the plugin, so later changes keep existing sites working
 */
class BlockReferenceTest extends TestCase
{
    public function testBlocksOfBlocksAndLayoutFieldsAreOffered(): void
    {
        $kirby = $this->app();

        $blocks = getBlocksFromPage($kirby->page('source'));

        $this->assertSame(['heading-1', 'in-layout'], array_column($blocks, 'id'));
        $this->assertSame(['text', 'grid'], array_column($blocks, 'parent'));
    }

    public function testReferenceBlocksAreNotOffered(): void
    {
        $kirby = $this->app();

        $types = array_column(getBlocksFromPage($kirby->page('source')), 'type');

        $this->assertNotContains('reference', $types);
    }

    public function testReferencedBlockIsRendered(): void
    {
        $kirby = $this->app();

        $html = $this->render($kirby, 'ref-heading');

        $this->assertStringContainsString('Shared heading', $html);
        $this->assertStringContainsString('<h2', $html);
    }

    public function testBlockInsideALayoutIsRendered(): void
    {
        $kirby = $this->app();

        $this->assertStringContainsString('Inside a layout', $this->render($kirby, 'ref-layout'));
    }

    public function testMissingPageOrBlockRendersNothing(): void
    {
        $kirby = $this->app();

        $this->assertSame('', $this->render($kirby, 'ref-missing-page'));
        $this->assertSame('', $this->render($kirby, 'ref-missing-block'));
    }

    public function testAccessNeedsAnAdminOrAnAllowedRole(): void
    {
        $kirby = $this->app();
        $this->assertFalse(blockReferenceCanAccess(), 'Nobody logged in');

        $kirby->impersonate('admin@example.test');
        $this->assertTrue(blockReferenceCanAccess());

        $kirby->impersonate('editor@example.test');
        $this->assertFalse(blockReferenceCanAccess());

        $kirby = $this->app(['tearoom1.kirby-block-reference.allowedRoles' => ['editor']]);
        $kirby->impersonate('editor@example.test');
        $this->assertTrue(blockReferenceCanAccess());
    }

    public function testPageMethodCombinesAccessAndReadPermission(): void
    {
        $kirby = $this->app();
        $this->assertFalse($kirby->page('source')->blockReferenceCanRead());

        $kirby->impersonate('admin@example.test');
        $this->assertTrue($kirby->page('source')->blockReferenceCanRead());
    }

    public function testApiListsPagesIncludingDrafts(): void
    {
        $kirby = $this->app();
        $kirby->impersonate('admin@example.test');

        $pages = $this->api($kirby, 'getAllPages');

        $this->assertEqualsCanonicalizing(['source', 'source/child', 'target', 'draft'], $pages);
    }

    public function testApiReturnsTheBlocksOfAPage(): void
    {
        $_GET['page'] = 'source';
        $kirby = $this->app();
        $kirby->impersonate('admin@example.test');

        $blocks = $this->api($kirby, 'blocks');

        $this->assertSame(['heading-1', 'in-layout'], array_column($blocks, 'id'));
    }

    public function testApiReturnsNothingWithoutAccess(): void
    {
        $_GET['page'] = 'source';
        $kirby = $this->app();
        $kirby->impersonate('editor@example.test');

        $this->assertSame([], $this->api($kirby, 'getAllPages'));
        $this->assertSame([], $this->api($kirby, 'blocks'));
    }

    protected function tearDown(): void
    {
        unset($_GET['page']);
        parent::tearDown();
    }
}
