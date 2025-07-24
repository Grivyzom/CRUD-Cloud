<?php
session_start();
require_once 'config.php';

// Obtener ID del libro
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    setFlashMessage('ID de libro inválido', 'error');
    header('Location: index.php');
    exit;
}

try {
    $pdo = getConnection();
    
    // Verificar que el libro existe
    $stmt = $pdo->prepare("SELECT title FROM books WHERE id = ?");
    $stmt->execute([$id]);
    $book = $stmt->fetch();
    
    if (!$book) {
        setFlashMessage('Libro no encontrado', 'error');
        header('Location: index.php');
        exit;
    }
    
    // Eliminar el libro
    $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
    $stmt->execute([$id]);
    
    setFlashMessage('Libro "' . $book['title'] . '" eliminado exitosamente');
    
} catch (Exception $e) {
    setFlashMessage('Error al eliminar el libro: ' . $e->getMessage(), 'error');
}

header('Location: index.php');
exit;
?>