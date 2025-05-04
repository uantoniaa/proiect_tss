<?php
require_once 'NoteService.php';
require_once 'FilterService.php';
require_once 'TagService.php';
require_once 'AuthService.php';

session_start();
if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['userId'];
$noteService = new NoteService();
$filterService = new FilterService();
$tagService = new TagService();

$message = null;
$filteredNotes = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    if (isset($_POST['create'])) {
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $message = $noteService->createNote($userId, $title, $content);

        // Adaugă etichete dacă există
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
    
        // Actualizează etichetele noi adăugate
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
    elseif (isset($_POST['logout'])) {
        session_destroy();
        header("Location: login.php");
        exit;
    }
    
}
$notes = $noteService->getNotes($userId);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Note App</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="topbar">
    <img src="images/logo.png" alt="Note App Logo" style="height: 110px; margin-left: 125px; padding-bottom: 30px;">

    <form method="POST" class="logout-form">
            <button type="submit" name="logout">Logout (<?= htmlspecialchars($userId) ?>)</button>
        </form>
    </div>

    <div class="container">
    <div class="sidebar">
        <!-- Buton pentru deschiderea modalului -->
        <button type="button" onclick="openModal()" class="add-note-btn">Adaugă notiță</button>

        <!-- Form: Filtrare -->
        <form method="POST" style="margin-bottom: 20px; padding: 15px 20px;">
            <h2>Filtrare notițe</h2>
            Cuvânt cheie: <input type="text" name="keyword"><br>
            <button name="filter" type="submit">Filtrează</button>
            <button name="filter" type="submit" onclick="this.form.keyword.value='';">Afișează tot</button>
        </form>

    </div>

    <div class="notes">
        <!-- Modal pentru adăugare notiță -->
        <div id="noteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <form method="POST" style="box-shadow: none; margin-left: 15px;">
                <h2 style="padding-left: 0px;">Adaugă notiță</h2>
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
        
            echo "</div>"; // end note-title
            echo "<div class='note-content'>" . nl2br(htmlspecialchars($note['content'])) . "</div>";
        
            echo "<div class='note-actions'>";
            echo "<button class='edit-btn' onclick='openEditModal(" . json_encode($note['title']) . ", " . json_encode($note['content']) . ")'>Editează</button>";
            echo "<button class='delete-btn' onclick='confirmDelete(" . json_encode($note['title']) . ")'>Șterge</button>";
            echo "</div>";
            echo "</div>"; 
        }
        
        ?>
    </div>
</div>
<!-- Modal editare -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeEditModal()">&times;</span>
    <form method="POST" style="box-shadow: none; margin-left: 15px;">
        <h2>Editează notiță</h2>
        <input type="hidden" name="old_title" id="edit-old-title">
        Titlu: <input type="text" name="new_title" id="edit-title" required><br>
        Conținut: <textarea name="new_content" id="edit-content" required></textarea><br>
        Etichete noi (virgulă): <input type="text" name="new_tags"><br>
        <button name="update" type="submit">Actualizează</button>
    </form>
  </div>
</div>
<!-- Modal pentru mesaje de notificare -->
<div id="messageModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeMessageModal()">&times;</span>
    <p id="messageContent"></p>
  </div>
</div>
<!-- Formular ascuns pentru ștergere -->
<form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" name="delete_title" id="deleteTitleInput">
    <input type="hidden" name="delete" value="1">
</form>

<!-- Modal confirmare ștergere -->
<div id="confirmDeleteModal" class="modal">
  <div class="modal-content">
    <p>Ești sigur că vrei să ștergi această notiță?</p>
    <button onclick="submitDelete()">Da, șterge</button>
    <button onclick="closeDeleteModal()">Anulează</button>
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
function showMessage(message) {
    const modal = document.getElementById("messageModal");
    const content = document.getElementById("messageContent");
    content.textContent = message;
    modal.style.display = "block";
}

function closeMessageModal() {
    document.getElementById("messageModal").style.display = "none";
}

// Închide dacă se face clic pe fundal
window.addEventListener("click", function(event) {
    const modal = document.getElementById("messageModal");
    if (event.target === modal) {
        modal.style.display = "none";
    }
});
let noteToDelete = null;

function confirmDelete(title) {
    noteToDelete = title;
    document.getElementById("confirmDeleteModal").style.display = "block";
}

function closeDeleteModal() {
    document.getElementById("confirmDeleteModal").style.display = "none";
    noteToDelete = null;
}

function submitDelete() {
    document.getElementById("deleteTitleInput").value = noteToDelete;
    document.getElementById("deleteForm").submit();
}

</script>
<?php if ($message): ?>
<script>
    showMessage(<?= json_encode($message) ?>);
</script>
<?php endif; ?>


</body>
</html>
