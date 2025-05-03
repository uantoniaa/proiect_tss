<?php

class FilterService {
    public function filterNotes(array $notes, string $keyword, ?string $dateFilter = null): array {
        $filtered = [];

        foreach ($notes as $note) {
            if (stripos($note['content'], $keyword) !== false) {
                if ($dateFilter !== null) { // if fără else
                    if (isset($note['date']) && $note['date'] === $dateFilter) {
                        $filtered[] = $note;
                    }
                } else {
                    $filtered[] = $note; // if cu else
                }
            }
        }

        return $filtered;
    }
}
