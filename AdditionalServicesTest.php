<?php

use PHPUnit\Framework\TestCase;
require_once 'TagService.php';
require_once 'AuthService.php';

class AdditionalServicesTest extends TestCase {

    public function testAddTagSuccess() {
        $tagService = new TagService();
        $result = $tagService->addTag("u1", "Note1", "important");
        $this->assertEquals("Eticheta adaugata cu succes.", $result);
    }

    public function testAddDuplicateTag() {
        $tagService = new TagService();
        $tagService->addTag("u1", "Note1", "urgent");
        $result = $tagService->addTag("u1", "Note1", "urgent");
        $this->assertEquals("Eticheta deja adaugata.", $result);
    }

    public function testRemoveTagSuccess() {
        $tagService = new TagService();
        $tagService->addTag("u1", "Note1", "study");
        $result = $tagService->removeTag("u1", "Note1", "study");
        $this->assertEquals("Eticheta eliminata.", $result);
    }

    public function testFilterNotesByTag() {
        $tagService = new TagService();
        $tagService->addTag("u1", "Note1", "todo");
        $tagService->addTag("u1", "Note2", "done");
        $tagService->addTag("u1", "Note3", "todo");

        $notes = $tagService->filterNotesByTag("u1", "todo");
        $this->assertEquals(["Note1", "Note3"], $notes);
    }

    public function testRegisterAndAuthenticate() {
        $authService = new AuthService();
        $result = $authService->register("alice", "1234");
        $this->assertEquals("Inregistrare cu succes.", $result);
        $this->assertTrue($authService->authenticate("alice", "1234"));
    }

    public function testAuthenticationFail() {
        $authService = new AuthService();
        $authService->register("bob", "secret");
        $this->assertFalse($authService->authenticate("bob", "wrongpass"));
    }
}
