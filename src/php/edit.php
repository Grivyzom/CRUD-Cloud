<?php
session_start();
require_once 'config.php';

$errors = [];
$book = null;

// Obtener ID del libro
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    setFlashMessage('ID de libro inválido', 'error');
    header('Location: index.php');
    exit;
}

// Obtener datos del libro
try {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$id]);
    $book = $stmt->fetch();
    
    if (!$book) {
        setFlashMessage('Libro no encontrado', 'error');
        header('Location: index.php');
        exit;
    }
} catch (Exception $e) {
    setFlashMessage('Error al cargar el libro', 'error');
    header('Location: index.php');
    exit;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $isbn = trim($_POST['isbn'] ?? '');
    $year = trim($_POST['year'] ?? '');
    $quantity = trim($_POST['quantity'] ?? '');

    // Validaciones
    if (empty($title)) $errors[] = 'El título es obligatorio';
    if (empty($author)) $errors[] = 'El autor es obligatorio';
    if (empty($isbn)) $errors[] = 'El ISBN es obligatorio';
    if (empty($year) || !is_numeric($year) || $year < 1000 || $year > date('Y')) {
        $errors[] = 'El año debe ser un número válido entre 1000 y ' . date('Y');
    }
    if (empty($quantity) || !is_numeric($quantity) || $quantity < 0) {
        $errors[] = 'La cantidad debe ser un número mayor o igual a 0';
    }

    // Verificar ISBN único (excepto el libro actual)
    if (!empty($isbn)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM books WHERE isbn = ? AND id != ?");
            $stmt->execute([$isbn, $id]);
            if ($stmt->fetch()) {
                $errors[] = 'Ya existe otro libro con este ISBN';
            }
        } catch (Exception $e) {
            $errors[] = 'Error al verificar el ISBN';
        }
    }

    // Si no hay errores, actualizar el libro
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, isbn = ?, year = ?, quantity = ? WHERE id = ?");
            $stmt->execute([$title, $author, $isbn, $year, $quantity, $id]);
            
            setFlashMessage('Libro actualizado exitosamente');
            header('Location: index.php');
            exit;
        } catch (Exception $e) {
            $errors[] = 'Error al actualizar el libro: ' . $e->getMessage();
        }
    } else {
        // Actualizar datos del libro con los valores del formulario
        $book['title'] = $title;
        $book['author'] = $author;
        $book['isbn'] = $isbn;
        $book['year'] = $year;
        $book['quantity'] = $quantity;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Libro - Biblioteca</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Editar Libro</h1>
                    <a href="index.php" class="text-gray-600 hover:text-gray-800 text-sm">← Volver</a>
                </div>

                <!-- Errores -->
                <?php if (!empty($errors)): ?>
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg border border-red-300">
                    <ul class="list-disc list-inside">
                        <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <form method="POST" class="space-y-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="<?php echo htmlspecialchars($book['title']); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                    </div>

                    <div>
                        <label for="author" class="block text-sm font-medium text-gray-700 mb-1">Autor *</label>
                        <input type="text" 
                               id="author" 
                               name="author" 
                               value="<?php echo htmlspecialchars($book['author']); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                    </div>

                    <div>
                        <label for="isbn" class="block text-sm font-medium text-gray-700 mb-1">ISBN *</label>
                        <input type="text" 
                               id="isbn" 
                               name="isbn" 
                               value="<?php echo htmlspecialchars($book['isbn']); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                    </div>

                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Año *</label>
                        <input type="number" 
                               id="year" 
                               name="year" 
                               value="<?php echo htmlspecialchars($book['year']); ?>"
                               min="1000" 
                               max="<?php echo date('Y'); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                    </div>

                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Cantidad *</label>
                        <input type="number" 
                               id="quantity" 
                               name="quantity" 
                               value="<?php echo htmlspecialchars($book['quantity']); ?>"
                               min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                    </div>

                    <div class="flex space-x-3 pt-4">
                        <button type="submit" 
                                class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition duration-200">
                            Guardar Cambios
                        </button>
                        <a href="index.php" 
                           class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg text-center transition duration-200">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>