<?php
// Настройки базы данных
$host = 'localhost';
$user = 'root';
$password = '12345678';
$database = 'my_database_name';

// Подключаемся к базе данных
$mysqli = new mysqli($host, $user, $password, $database);

// Проверяем подключение
if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

// Исправленный запрос: выбираем все товары, но пропускаем скрытые
$query = "SELECT * FROM Products"; // Выбираем все товары
$result = $mysqli->query($query);

$items = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Пропускаем товары, если поле IS_HIDDEN равно 0 (скрыто)
 // Пропускаем скрытые товары
if ($row['IS_HIDDEN'] == 1)
{
    $items[] = $row;
}
else 
{
    continue;
}        
    }
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <style>
        /* Стили страницы */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        table {
            border-collapse: collapse;
            width: 90%;
            max-width: 1200px;
            background-color: #ffffff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px 16px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f7f7f7;
            font-weight: bold;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .decrease {
            background-color: #f44336;
        }
        .decrease:hover {
            background-color: #e53935;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Product Price</th>
                <th>Product Article</th>
                <th>Quantity</th>
                <th>Date Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="product-table">
            <?php if (empty($items)): ?>
                <tr>
                    <td colspan="8">No products available.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <tr data-id="<?= htmlspecialchars($item['ID']) ?>">
                        <td><?= htmlspecialchars($item['ID']) ?></td>
                        <td><?= htmlspecialchars($item['PRODUCT_ID']) ?></td>
                        <td><?= htmlspecialchars($item['PRODUCT_NAME']) ?></td>
                        <td><?= htmlspecialchars($item['PRODUCT_PRICE']) ?></td>
                        <td><?= htmlspecialchars($item['PRODUCT_ARTICLE']) ?></td>
                        <td>
                            <button class="decrease">-</button>
                            <span><?= htmlspecialchars($item['PRODUCT_QUANTITY']) ?></span>
                            <button class="increase">+</button>
                        </td>
                        <td><?= htmlspecialchars($item['DATE_CREATE']) ?></td>
                        <td><button class="hide-product" data-id="<?= htmlspecialchars($item['ID']) ?>">Hide</button></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
        $(document).ready(function () {
           
            $(".hide-product").click(function () {
                const productId = $(this).data("id");  // Получаем ID товара
                const row = $(this).closest('tr');  // Находим строку таблицы

                $.ajax({
                    url: "products_actions.php",  // Путь к обработчику
                    type: "POST",
                    data: { action: "hideProduct", id: productId }, // Отправляем ID товара
                    success: function (response) {
                      //  const result = JSON.parse(response);
                        if (response.success) {
                            alert("Product successfully hidden!");
                            row.remove();
                           // location.reload(true);    
                        } else {
                            alert("Error: " + result.error);  // Показываем ошибку
                        }
                    },

                });
            });

            // Изменение количества товара
            $(document).on('click', '.increase, .decrease', function () {
                const row = $(this).closest('tr');
                const id = row.data('id');
                const span = $(this).siblings('span');
                let quantity = parseInt(span.text());

                if ($(this).hasClass('increase')) quantity++;
                if ($(this).hasClass('decrease') && quantity > 0) quantity--;

                // Отправляем обновление количества на сервер
                $.post('products_actions.php', { action: 'updateQuantity', id: id, quantity: quantity }, function (response) {
                    if (response.success) {
                        span.text(quantity); // Обновляем количество в таблице
                    } else {
                        alert('Failed to update quantity.');
                    }
                }, 'json');
            });
        });
    </script>
</body>
</html>
