<?php
session_start();
require_once 'config.php';

// Obtener todos los libros
try {
    $pdo = getConnection();
    $stmt = $pdo->query("SELECT * FROM books ORDER BY created_at DESC");
    $books = $stmt->fetchAll();
} catch (Exception $e) {
    $error = "Error al cargar los libros: " . $e->getMessage();
}

$flash = getFlashMessage();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Biblioteca</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-800">Inventario de Biblioteca</h1>
                    <a href="create.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        Agregar Libro
                    </a>
                </div>

                <!-- Mensaje Flash -->
                <?php if ($flash): ?>
                <div class="mb-4 p-4 rounded-lg <?php echo $flash['type'] === 'success' ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300'; ?>">
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
                <?php endif; ?>

                <!-- Error -->
                <?php if (isset($error)): ?>
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg border border-red-300">
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>

                <!-- Tabla de libros -->
                <?php if (empty($books)): ?>
                <div class="text-center py-8">
                    <p class="text-gray-500 text-lg">No hay libros registrados</p>
                    <a href="create.php" class="text-blue-500 hover:text-blue-600 underline mt-2 inline-block">
                        Agregar el primer libro
                    </a>
                </div>
                <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">ID</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Título</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Autor</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">ISBN</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Año</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Cantidad</th>
                                <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($books as $book): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900"><?php echo $book['id']; ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900 font-medium"><?php echo htmlspecialchars($book['title']); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-600"><?php echo htmlspecialchars($book['author']); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-600"><?php echo htmlspecialchars($book['isbn']); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-600"><?php echo $book['year']; ?></td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    <span class="<?php echo $book['quantity'] > 0 ? 'text-green-600' : 'text-red-600'; ?> font-medium">
                                        <?php echo $book['quantity']; ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-center">
                                    <div class="flex justify-center space-x-2">
                                        <a href="edit.php?id=<?php echo $book['id']; ?>" 
                                           class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-xs transition duration-200">
                                            Editar
                                        </a>
                                        <a href="delete.php?id=<?php echo $book['id']; ?>" 
                                           class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs transition duration-200"
                                           onclick="return confirm('¿Estás seguro de que quieres eliminar este libro?')">
                                            Eliminar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>