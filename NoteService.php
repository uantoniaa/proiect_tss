<?php

class NoteService {
    private array $notes = [];

    public function createNote(string $userId, string $title, string $content): string {
        if (!isset($this->notes[$userId])) {
            $this->notes[$userId] = [];
        }

        // verificare titlu duplicat
        foreach ($this->notes[$userId] as $note) {
            if ($note['title'] === $title) {
                return "Titlu deja folosit."; 
            }
        }

        if (trim($title) === "" || trim($content) === "") { 
            return "Titlu și conținut obligatorii.";
        }

        $this->notes[$userId][] = [
            'title' => $title,
            'content' => $content
        ];

        return "Notiță creată cu succes.";
    }

    public function deleteNote(string $userId, string $title): string {
        if (!isset($this->notes[$userId])) {
            return "Utilizatorul nu are notițe.";
        }
    
        foreach ($this->notes[$userId] as $index => $note) {
            if ($note['title'] === $title) {
                array_splice($this->notes[$userId], $index, 1);
                return "Notiță ștearsă.";
            }
        }
    
        return "Notiță inexistentă.";
    }
    public function updateNote(string $userId, string $oldTitle, string $newTitle, string $newContent): string {
        if (!isset($this->notes[$userId])) return "Utilizatorul nu are notițe.";
    
        foreach ($this->notes[$userId] as &$note) {
            if ($note['title'] === $oldTitle) {
                $note['title'] = $newTitle;
                $note['content'] = $newContent;
                return "Notiță actualizată.";
            }
        }
    
        return "Notiță inexistentă.";
    }
    
    public function getNotes(string $userId): array {
        return $this->notes[$userId] ?? [];
    }
}
