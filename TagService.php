<?php

class TagService {
    private array $tags = []; // user_id => [note_title => [tag1, tag2, ...]]

    public function addTag(string $userId, string $noteTitle, string $tag): string {
        if (!isset($this->tags[$userId][$noteTitle])) {
            $this->tags[$userId][$noteTitle] = [];
        }

        if (in_array($tag, $this->tags[$userId][$noteTitle])) {
            return "Etichetă deja adăugată.";
        }

        $this->tags[$userId][$noteTitle][] = $tag;
        return "Etichetă adăugată cu succes.";
    }

    public function removeTag(string $userId, string $noteTitle, string $tag): string {
        if (!isset($this->tags[$userId][$noteTitle])) {
            return "Notiță fără etichete.";
        }

        $index = array_search($tag, $this->tags[$userId][$noteTitle]);
        if ($index === false) {
            return "Etichetă inexistentă.";
        }

        unset($this->tags[$userId][$noteTitle][$index]);
        $this->tags[$userId][$noteTitle] = array_values($this->tags[$userId][$noteTitle]);
        return "Etichetă eliminată.";
    }

    public function filterNotesByTag(string $userId, string $tag): array {
        $result = [];

        if (!isset($this->tags[$userId])) {
            return $result;
        }

        foreach ($this->tags[$userId] as $title => $tags) {
            if (in_array($tag, $tags)) {
                $result[] = $title;
            }
        }

        return $result;
    }
}
