<?php
require_once __DIR__ . '/../src/TagService.php';
require_once __DIR__ . '/../src/AuthService.php';
require_once __DIR__ . '/../src/NoteService.php';
require_once __DIR__ . '/../src/FilterService.php';
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\TagService::class)]
#[CoversClass(\AuthService::class)]
#[CoversClass(\NoteService::class)]
#[CoversClass(\FilterService::class)]


class AdditionalServicesTest extends TestCase
{
    protected function setUp(): void
    {
        @file_put_contents('users.json', json_encode([]));
        @file_put_contents('notes.json', json_encode([]));
        @file_put_contents('tags.json', json_encode([]));
    }

    // TAGSERVICE

    public function testAddTagSuccess()
    {
        $tagService = new TagService();
        $noteService = new NoteService();
        $noteService->createNote("u1", "Note1", "test");
        $result = $tagService->addTag("u1", "Note1", "important");
        $this->assertSame("Etichetă adăugată cu succes.", $result);
    }

    public function testAddDuplicateTag()
    {
        $tagService = new TagService();
        $noteService = new NoteService();
        $noteService->createNote("u1", "Note1", "test");
        $tagService->addTag("u1", "Note1", "urgent");
        $result = $tagService->addTag("u1", "Note1", "urgent");
        $this->assertSame("Etichetă deja adăugată.", $result);
    }

    public function testRemoveTagSuccess()
    {
        $tagService = new TagService();
        $noteService = new NoteService();
        $noteService->createNote("u1", "Note1", "test");
        $tagService->addTag("u1", "Note1", "study");
        $result = $tagService->removeTag("u1", "Note1", "study");
        $this->assertSame("Etichetă eliminată.", $result);
    }

    public function testRemoveTagNoTagsExist()
    {
        $tagService = new TagService();
        $result = $tagService->removeTag("nouser", "Notita", "tag");
        $this->assertSame("Notiță fără etichete.", $result);
    }

    public function testRemoveTagNonExistent()
    {
        $tagService = new TagService();
        $noteService = new NoteService();
        $noteService->createNote("u1", "Note1", "test");
        $tagService->addTag("u1", "Note1", "tag1");
        $result = $tagService->removeTag("u1", "Note1", "tag2");
        $this->assertSame("Etichetă inexistentă.", $result);
    }

    public function testFilterNotesByExistingTag()
    {
        $tagService = new TagService();
        $noteService = new NoteService();
        $noteService->createNote("u1", "Note1", "task");
        $noteService->createNote("u1", "Note2", "task");
        $tagService->addTag("u1", "Note1", "todo");
        $tagService->addTag("u1", "Note2", "done");
        $result = $tagService->filterNotesByTag("u1", "todo");
        $this->assertSame(["Note1"], $result);
    }

    public function testFilterNotesByMissingTag()
    {
        $tagService = new TagService();
        $noteService = new NoteService();
        $noteService->createNote("u1", "Note1", "task");
        $result = $tagService->filterNotesByTag("u1", "nonexistent");
        $this->assertSame([], $result);
    }

    public function testFilterNotesByTagNoUser()
    {
        $tagService = new TagService();
        $result = $tagService->filterNotesByTag("nouser", "tag");
        $this->assertSame([], $result);
    }

    public function testGetTagsEmpty()
    {
        $tagService = new TagService();
        $this->assertSame([], $tagService->getTags("u1", "unknown"));
    }

    // AUTHSERVICE

    public function testRegisterAndAuthenticate()
    {
        $authService = new AuthService();
        $result = $authService->register("alice", "1234");
        $this->assertSame("Înregistrare cu succes.", $result);
        $this->assertTrue($authService->authenticate("alice", "1234"));
    }

    public function testRegisterExistingUser()
    {
        $authService = new AuthService();
        $authService->register("bob", "pass");
        $result = $authService->register("bob", "pass");
        $this->assertSame("Utilizator deja existent.", $result);
    }

    public function testAuthenticationFails()
    {
        $authService = new AuthService();
        $authService->register("bob", "secret");
        $this->assertFalse($authService->authenticate("bob", "wrongpass"));
    }

    public function testAuthenticationNoUser()
    {
        $authService = new AuthService();
        $this->assertFalse($authService->authenticate("ghost", "1234"));
    }

    // FILTERSERVICE

    public function testFilterKeywordNoMatch()
    {
        $filter = new FilterService();
        $notes = [['content' => 'nothing useful']];
        $result = $filter->filterNotes($notes, 'missing');
        $this->assertEmpty($result);
    }

    public function testFilterWithDateButNoteHasNoDate()
    {
        $filter = new FilterService();
        $notes = [['content' => 'testing']];
        $result = $filter->filterNotes($notes, 'testing', '2024-01-01');
        $this->assertEmpty($result);
    }
}
