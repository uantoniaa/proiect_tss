<?php
require_once 'NoteService.php';
require_once 'FilterService.php';
require_once 'TagService.php';

session_start();
$userId = "demo_user";

$noteService = $_SESSION['noteService'] ?? new NoteService();
$filterService = $_SESSION['filterService'] ?? new FilterService();
$tagService = $_SESSION['tagService'] ?? new TagService();

$message = null;
$filteredNotes = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['create'])) {
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $message = $noteService->createNote($userId, $title, $content);
    } elseif (isset($_POST['delete'])) {
        $title = $_POST['delete_title'] ?? '';
        $message = $noteService->deleteNote($userId, $title);
    } elseif (isset($_POST['filter'])) {
        $keyword = $_POST['keyword'] ?? '';
        $filteredNotes = $filterService->filterNotes($noteService->getNotes($userId), $keyword);
    } elseif (isset($_POST['tag'])) {
        $title = $_POST['tag_title'] ?? '';
        $tag = $_POST['tag_value'] ?? '';
        $message = $tagService->addTag($userId, $title, $tag);
    }
}

$_SESSION['noteService'] = $noteService;
$_SESSION['filterService'] = $filterService;
$_SESSION['tagService'] = $tagService;

$notes = $noteService->getNotes($userId);
?>

<!DOCTYPE html>
<html>
<head><title>NoteApp</title></head>
<body>
    <h1>NoteApp</h1>

    <?php if ($message) echo "<p><strong>$message</strong></p>"; ?>

    <!-- Creare notiță -->
    <form method="POST">
        <h2>Adaugă notiță</h2>
        Titlu: <input type="text" name="title" required><br>
        Conținut: <textarea name="content" required></textarea><br>
        <button name="create" type="submit">Salvează</button>
    </form>

    <!-- Ștergere notiță -->
    <form method="POST">
        <h2>Șterge notiță</h2>
        Titlu: <input type="text" name="delete_title" required><br>
        <button name="delete" type="submit">Șterge</button>
    </form>

    <!-- Filtrare notițe -->
    <form method="POST">
        <h2>Filtrare notițe</h2>
        Cuvânt cheie: <input type="text" name="keyword"><br>
        <button name="filter" type="submit">Filtrează</button>
    </form>

    <!-- Adaugare etichetă -->
    <form method="POST">
        <h2>Adaugă etichetă la notiță</h2>
        Titlu notiță: <input type="text" name="tag_title" required><br>
        Etichetă: <input type="text" name="tag_value" required><br>
        <button name="tag" type="submit">Adaugă etichetă</button>
    </form>

    <h2>Notițele tale</h2>
    <ul>
        <?php
        $toShow = $filteredNotes ?? $notes;
        foreach ($toShow as $note) {
            echo "<li><strong>{$note['title']}</strong>: {$note['content']}</li>";
        }
        ?>
    </ul>
</body>
</html>
