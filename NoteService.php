<?php

class NoteService {
    private array $notes = [];
    private string $file = "notes.json";

    public function __construct() {
        if (file_exists($this->file)) {
            $this->notes = json_decode(file_get_contents($this->file), true) ?? [];
        }
    }

    private function save(): void {
        file_put_contents($this->file, json_encode($this->notes, JSON_PRETTY_PRINT));
    }

    public function createNote(string $userId, string $title, string $content): string {
        if (!isset($this->notes[$userId])) {
            $this->notes[$userId] = [];
        }

        foreach ($this->notes[$userId] as $note) {
            if ($note['title'] === $title) {
                return "Titlu deja folosit.";
            }
        }

        if (trim($title) === "" || trim($content) === "") {
            return "Titlu și conținut obligatorii.";
        }

        $this->notes[$userId][] = ['title' => $title, 'content' => $content];
        $this->save();
        return "Notiță creată cu succes.";
    }

    public function deleteNote(string $userId, string $title): string {
        if (!isset($this->notes[$userId])) {
            return "Utilizatorul nu are notițe.";
        }

        foreach ($this->notes[$userId] as $index => $note) {
            if ($note['title'] === $title) {
                array_splice($this->notes[$userId], $index, 1);
                $this->save();
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
                $this->save();
                return "Notiță actualizată.";
            }
        }

        return "Notiță inexistentă.";
    }

    public function getNotes(string $userId): array {
        return $this->notes[$userId] ?? [];
    }
}
