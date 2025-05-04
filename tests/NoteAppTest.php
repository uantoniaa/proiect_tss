<?php

use PHPUnit\Framework\TestCase;
require_once 'NoteService.php';
require_once 'FilterService.php';

class NoteAppTest extends TestCase {

    public function testCreateNoteSuccess() {
        $service = new NoteService();
        $result = $service->createNote("u1", "Test", "Content");
        $this->assertEquals("Notita creata cu succes.", $result);
    }

    public function testCreateNoteDuplicateTitle() {
        $service = new NoteService();
        $service->createNote("u1", "Test", "Content");
        $result = $service->createNote("u1", "Test", "Other");
        $this->assertEquals("Titlu deja folosit.", $result);
    }

    public function testCreateNoteEmptyFields() {
        $service = new NoteService();
        $result = $service->createNote("u1", "", "");
        $this->assertEquals("Titlu si continut obligatorii.", $result);
    }

    public function testUpdateNoteSuccess() {
        $service = new NoteService();
        $service->createNote("u2", "Old", "Old Content");
        $result = $service->updateNote("u2", "Old", "New", "New Content");
        $this->assertEquals("Notita actualizata.", $result);
    }

    public function testUpdateNoteNotFound() {
        $service = new NoteService();
        $result = $service->updateNote("u2", "NonExistent", "New", "Content");
        $this->assertEquals("Notita inexistenta.", $result);
    }

    public function testDeleteNoteSuccess() {
        $service = new NoteService();
        $service->createNote("u3", "Test", "Content");
        $result = $service->deleteNote("u3", "Test");
        $this->assertEquals("Notita stearsa.", $result);
    }

    public function testDeleteNoteNotFound() {
        $service = new NoteService();
        $result = $service->deleteNote("u3", "Nope");
        $this->assertEquals("Notita inexistenta.", $result);
    }

    public function testFilterByKeyword() {
        $filter = new FilterService();
        $notes = [
            ['content' => 'Learn testing', 'date' => '2024-01-01'],
            ['content' => 'Write docs', 'date' => '2024-01-02'],
            ['content' => 'Testing code', 'date' => '2024-01-01']
        ];
        $result = $filter->filterNotes($notes, 'testing');
        $this->assertCount(2, $result);
    }

    public function testFilterByKeywordAndDate() {
        $filter = new FilterService();
        $notes = [
            ['content' => 'Learn testing', 'date' => '2024-01-01'],
            ['content' => 'Testing code', 'date' => '2024-01-01'],
            ['content' => 'Testing fail', 'date' => '2024-02-01']
        ];
        $result = $filter->filterNotes($notes, 'testing', '2024-01-01');
        $this->assertCount(2, $result);
    }
}
