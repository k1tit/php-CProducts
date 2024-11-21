<?php
$host = 'localhost';
$user = 'root';
$password = '12345678';
$database = 'my_database_name';

header('Content-Type: application/json; charset=utf-8');

$response = [];


// Подключаемся к базе данных
$mysqli = new mysqli($host, $user, $password, $database);

if ($mysqli->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Database connection failed: ' . $mysqli->connect_error]));
}

$response = ['success' => false];

// Проверяем, существует ли параметр 'action' в POST-запросе
if (isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'hideProduct':
            // Проверяем, передан ли ID товара
            if (isset($_POST['id'])) {
                $id = (int)$_POST['id'];  // Преобразуем ID в целое число
                // SQL-запрос для обновления статуса IS_HIDDEN
                $query = "UPDATE Products SET IS_HIDDEN = 0 WHERE ID = ?";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("i", $id);  // Привязываем ID
                if ($stmt->execute()) {
                    $response['success'] = true;  // Возвращаем успех
                } else {
                    $response['error'] = 'Не удалось скрыть товар';  // Ошибка выполнения
                }
                $stmt->close();
            } else {
                $response['error'] = 'Product ID is missing';  // Если ID не передан
            }
            break;

        case 'updateQuantity':
            // Обработка запроса на обновление количества
            if (isset($_POST['id'], $_POST['quantity']) && is_numeric($_POST['id']) && is_numeric($_POST['quantity'])) {
                $id = (int)$_POST['id'];
                $quantity = (int)$_POST['quantity'];

                // Проверка, что количество не отрицательное
                if ($quantity < 0) {
                    $response['error'] = 'Quantity cannot be negative';
                } else {
                    $stmt = $mysqli->prepare("UPDATE Products SET PRODUCT_QUANTITY = ? WHERE ID = ?");
                    $stmt->bind_param('ii', $quantity, $id);

                    if ($stmt->execute() && $stmt->affected_rows > 0) {
                        $response['success'] = true;
                    } else {
                        $response['error'] = 'Failed to update quantity: ' . $stmt->error;
                    }

                    $stmt->close();
                }
            } else {
                $response['error'] = 'Product ID or quantity is missing or invalid';
            }
            break;

        default:
            $response['error'] = 'Unknown action: ' . $action; 
            break;
    }
} else {
    $response['error'] = 'Invalid request';
}

echo json_encode($response);
$mysqli->close();
?>
