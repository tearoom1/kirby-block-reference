<?php

namespace TearoomOne\BlockReference\Tests;

use TearoomOne\BlockReference\BlockReference;

/**
 * The namespaced API, draft handling and the new API routes
 */
class BlockReferenceClassTest extends TestCase
{
    public function testClassOffersTheSameBlocksAsTheLegacyFunction(): void
    {
        $kirby = $this->app();
        $page = $kirby->page('source');

        $this->assertSame(getBlocksFromPage($page), BlockReference::blocksFromPage($page));
    }

    public function testBlockOfASubpageIsRendered(): void
    {
        $kirby = $this->app();

        $this->assertStringContainsString('From a subpage', $this->render($kirby, 'ref-child'));
    }

    public function testDraftBlocksAreHiddenFromVisitors(): void
    {
        $kirby = $this->app();

        $this->assertSame('', $this->render($kirby, 'ref-draft'));
    }

    public function testDraftBlocksAreShownToLoggedInUsers(): void
    {
        $kirby = $this->app();
        $kirby->impersonate('editor@example.test');

        $this->assertStringContainsString('Not published yet', $this->render($kirby, 'ref-draft'));
    }

    public function testDraftBlocksCanBeShownToEveryone(): void
    {
        $kirby = $this->app(['tearoom1.kirby-block-reference.renderDrafts' => true]);

        $this->assertStringContainsString('Not published yet', $this->render($kirby, 'ref-draft'));
    }

    public function testNewApiRoutesMatchTheOldOnes(): void
    {
        $_GET['page'] = 'source';
        $kirby = $this->app();
        $kirby->impersonate('admin@example.test');

        $this->assertSame($this->api($kirby, 'getAllPages'), $this->api($kirby, 'block-reference/pages'));
        $this->assertSame($this->api($kirby, 'blocks'), $this->api($kirby, 'block-reference/blocks'));
    }

    public function testNewApiRoutesCheckAccess(): void
    {
        $_GET['page'] = 'source';
        $kirby = $this->app();
        $kirby->impersonate('editor@example.test');

        $this->assertSame([], $this->api($kirby, 'block-reference/pages'));
        $this->assertSame([], $this->api($kirby, 'block-reference/blocks'));
    }

    public function testBlocksOfASubpageAreOfferedByTheApi(): void
    {
        $_GET['page'] = 'source/child';
        $kirby = $this->app();
        $kirby->impersonate('admin@example.test');

        $this->assertSame(['child-heading'], array_column($this->api($kirby, 'block-reference/blocks'), 'id'));
    }

    protected function tearDown(): void
    {
        unset($_GET['page']);
        parent::tearDown();
    }
}
