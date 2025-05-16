<?php 
require_once __DIR__ . '/../src/NoteService.php';
require_once __DIR__ . '/../src/FilterService.php';

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\NoteService::class)]
#[CoversClass(\FilterService::class)]

class NoteAppTest extends TestCase
{
    protected function setUp(): void
    {
        @file_put_contents('notes.json', json_encode([]));
    }

    public function testCreateNoteSuccess()
    {
        $service = new NoteService();
        $result = $service->createNote("u1", "Test", "Content");
        $this->assertSame("Notiță creată cu succes.", $result);
    }

    public function testCreateNoteDuplicateTitle()
    {
        $service = new NoteService();
        $service->createNote("u1", "Test", "Content");
        $result = $service->createNote("u1", "Test", "Other");
        $this->assertSame("Titlu deja folosit.", $result);
    }

    public function testCreateNoteWithWhitespaceOnly()
    {
        $service = new NoteService();
        $result = $service->createNote("u1", "   ", "   ");
        $this->assertSame("Titlu și conținut obligatorii.", $result);
    }

    public function testUpdateNoteSuccess()
    {
        $service = new NoteService();
        $service->createNote("u2", "Old", "Old Content");
        $result = $service->updateNote("u2", "Old", "New", "New Content");
        $this->assertSame("Notiță actualizată.", $result);
    }

    public function testUpdateNoteNotFound()
    {
        $service = new NoteService();
        $result = $service->updateNote("u2", "NonExistent", "New", "Content");
        $this->assertSame('Utilizatorul nu are notițe.', $result);
    }

    public function testDeleteNoteSuccess()
    {
        $service = new NoteService();
        $service->createNote("u3", "Test", "Content");
        $result = $service->deleteNote("u3", "Test");
        $this->assertSame("Notiță ștearsă.", $result);
    }

    public function testDeleteNoteNotFound()
    {
        $service = new NoteService();
        $result = $service->deleteNote("u3", "Nope");
        $this->assertSame("Utilizatorul nu are notițe.", $result);
    }

    public function testDeleteNoteNoUserNotes()
    {
        $service = new NoteService();
        $result = $service->deleteNote("nouser", "Any");
        $this->assertSame("Utilizatorul nu are notițe.", $result);
    }

    public function testUpdateNoteNoUserNotes()
    {
        $service = new NoteService();
        $result = $service->updateNote("nouser", "Old", "New", "New Content");
        $this->assertSame("Utilizatorul nu are notițe.", $result);
    }

    public function testGetNotesReturnsArray()
    {
        $service = new NoteService();
        $service->createNote("u1", "Test", "Content");
        $notes = $service->getNotes("u1");
        $this->assertIsArray($notes);
        $this->assertCount(1, $notes);
    }

    public function testFilterByKeyword()
    {
        $filter = new FilterService();
        $notes = [
            ['content' => 'Learn testing', 'date' => '2024-01-01'],
            ['content' => 'Write docs', 'date' => '2024-01-02'],
            ['content' => 'Testing code', 'date' => '2024-01-01']
        ];
        $result = $filter->filterNotes($notes, 'testing');
        $this->assertCount(2, $result);
    }

    public function testFilterByKeywordAndDate()
    {
        $filter = new FilterService();
        $notes = [
            ['content' => 'Learn testing', 'date' => '2024-01-01'],
            ['content' => 'Testing code', 'date' => '2024-01-01'],
            ['content' => 'Testing fail', 'date' => '2024-02-01']
        ];
        $result = $filter->filterNotes($notes, 'testing', '2024-01-01');
        $this->assertCount(2, $result);
    }
    public function testDeleteNoteWrongTitle()
    {
        $service = new NoteService();
        $service->createNote("u4", "RealTitle", "Some content");


        $result = $service->deleteNote("u4", "WrongTitle");

        $this->assertSame("Notiță inexistentă.", $result);
    }
    public function testUpdateNoteWrongTitle()
{
    $service = new NoteService();
    $service->createNote("u5", "OriginalTitle", "Some content");

    $result = $service->updateNote("u5", "NonExistentTitle", "NewTitle", "Updated");

    $this->assertSame("Notiță inexistentă.", $result);
}

}
