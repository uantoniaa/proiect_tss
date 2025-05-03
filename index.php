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

        $tagsRaw = $_POST['tags'] ?? '';
        $tags = array_filter(array_map('trim', explode(',', $tagsRaw)));
        
        foreach ($tags as $tag) {
            $tagMessage = $tagService->addTag($userId, $title, $tag);
        }
    } elseif (isset($_POST['delete'])) {
        $title = $_POST['delete_title'] ?? '';
        $message = $noteService->deleteNote($userId, $title);
    } elseif (isset($_POST['filter'])) {
        $keyword = $_POST['keyword'] ?? '';
        $allNotes = $noteService->getNotes($userId);
        $filteredNotes = [];

        foreach ($allNotes as $note) {
            $tags = $tagService->getTags($userId, $note['title']);

            if (
                stripos($note['title'], $keyword) !== false ||
                stripos($note['content'], $keyword) !== false ||
                array_filter($tags, fn($tag) => stripos($tag, $keyword) !== false)
            ) {
                $filteredNotes[] = $note;
            }
        }

    } elseif (isset($_POST['tag'])) {
        $title = $_POST['tag_title'] ?? '';
        $tag = $_POST['tag_value'] ?? '';
        $message = $tagService->addTag($userId, $title, $tag);
    }
    elseif (isset($_POST['update'])) {
        $oldTitle = $_POST['old_title'];
        $newTitle = $_POST['new_title'];
        $newContent = $_POST['new_content'];
        $message = $noteService->updateNote($userId, $oldTitle, $newTitle, $newContent);
    
        $tagsRaw = $_POST['new_tags'] ?? '';
        $tags = array_filter(array_map('trim', explode(',', $tagsRaw)));
    
        foreach ($tags as $tag) {
            $tagService->addTag($userId, $newTitle, $tag);
        }
    
    } elseif (isset($_POST['remove_tag'])) {
        $title = $_POST['tag_title'];
        $tag = $_POST['tag_value'];
        $message = $tagService->removeTag($userId, $title, $tag);
    }
    
}

$_SESSION['noteService'] = $noteService;
$_SESSION['filterService'] = $filterService;
$_SESSION['tagService'] = $tagService;

$notes = $noteService->getNotes($userId);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Note App</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>NoteApp</h1>

    <?php if ($message) echo "<p><strong>$message</strong></p>"; ?>

    <div class="container">
    <div class="sidebar">

        <button type="button" onclick="openModal()" class="add-note-btn">Adaugă notiță</button>


        <form method="POST">
            <h2>Șterge notiță</h2>
            Selectează notița:
            <select name="delete_title" required>
                <?php foreach ($notes as $note): ?>
                    <option value="<?= htmlspecialchars($note['title']) ?>"><?= htmlspecialchars($note['title']) ?></option>
                <?php endforeach; ?>
            </select><br>
            <button name="delete" type="submit">Șterge</button>
        </form>

        <form method="POST">
            <h2>Filtrare notițe</h2>
            Cuvânt cheie: <input type="text" name="keyword"><br>
            <button name="filter" type="submit">Filtrează</button>
        </form>
    </div>

    <div class="notes">
  
        <div id="noteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <form method="POST">
                <h2>Adaugă notiță</h2>
                Titlu: <input type="text" name="title" required><br>
                Conținut: <textarea name="content" required></textarea><br>
                Etichete (separate prin virgulă): <input type="text" name="tags"><br>
                <button name="create" type="submit">Salvează</button>
            </form>
        </div>
</div>

        <h2>Notițele tale</h2>
        <?php
        $toShow = $filteredNotes ?? $notes;
        foreach ($toShow as $note) {
            $tags = $tagService->getTags($userId, $note['title']);
            echo "<div class='note'>";
            echo "<div class='note-title'>{$note['title']}";
            
            if (!empty($tags)) {
                echo "<div class='tags'>";
                foreach ($tags as $tag) {
                    echo "<form method='POST' style='display:inline; margin-right:5px;'>";
                    echo "<input type='hidden' name='tag_title' value='" . htmlspecialchars($note['title']) . "'>";
                    echo "<input type='hidden' name='tag_value' value='" . htmlspecialchars($tag) . "'>";
                    echo "<button type='submit' name='remove_tag' class='tag-remove'>#{$tag} ×</button>";
                    echo "</form>";
                }
                echo "</div>";
            }
        
            echo "</div>";
            echo "<div class='note-content'>" . nl2br(htmlspecialchars($note['content'])) . "</div>";
        
    
            echo "<button class='edit-btn' onclick='openEditModal(" . json_encode($note['title']) . ", " . json_encode($note['content']) . ")'>Editează</button>";
            
            echo "</div>";
        }
        
        ?>
    </div>
</div>
<div id="editModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeEditModal()">&times;</span>
    <form method="POST">
        <h2>Editează notiță</h2>
        <input type="hidden" name="old_title" id="edit-old-title">
        Titlu: <input type="text" name="new_title" id="edit-title" required><br>
        Conținut: <textarea name="new_content" id="edit-content" required></textarea><br>
        Etichete noi (virgulă): <input type="text" name="new_tags"><br>
        <button name="update" type="submit">Actualizează</button>
    </form>
  </div>
</div>

<script>
function openModal() {
    document.getElementById("noteModal").style.display = "block";
}

function closeModal() {
    document.getElementById("noteModal").style.display = "none";
}

function openEditModal(title, content) {
    document.getElementById("editModal").style.display = "block";
    document.getElementById("edit-old-title").value = title;
    document.getElementById("edit-title").value = title;
    document.getElementById("edit-content").value = content;
}

function closeEditModal() {
    document.getElementById("editModal").style.display = "none";
}

window.onclick = function(event) {
    const modal1 = document.getElementById("noteModal");
    const modal2 = document.getElementById("editModal");
    if (event.target === modal1) modal1.style.display = "none";
    if (event.target === modal2) modal2.style.display = "none";
}
</script>


</body>
</html>
