<?php
require_once __DIR__ . '/CProducts.php'; 

$host = 'localhost';
$user = 'root';
$password = '12345678';
$database = 'my_database_name';

header('Content-Type: application/json; charset=utf-8');

$products = new CProducts($host, $user, $password, $database);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $response = ['success' => false]; 
    $action = $_POST['action']; 

    switch ($action) {
        case 'hideProduct':
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                $productId = (int)$_POST['id'];
    
                $result = $products->hideProduct($productId);
                if ($result) {
                    $response['success'] = true;
                    $response['message'] = 'Product hidden successfully';
                } else {
                    $response['error'] = 'Failed to hide product. No rows affected.';
                }
            } else {
                $response['error'] = 'Product ID is missing or invalid';
            }
            break;    

        case 'updateQuantity':

            if (isset($_POST['id'], $_POST['quantity']) && is_numeric($_POST['id']) && is_numeric($_POST['quantity'])) {
                $id = (int)$_POST['id'];
                $quantity = (int)$_POST['quantity'];

                $result = $products->updateQuantity($id, $quantity);

                if ($result) {
                    $response['success'] = true;
                    $response['message'] = 'Quantity updated successfully';
                } else {
                    $response['error'] = 'Failed to update quantity';
                }
            } else {
                $response['error'] = 'Product ID or quantity is missing or invalid';
            }
            break;

        default:
            $response['error'] = 'Unknown action: ' . $action; 
            break;
    }

    echo json_encode($response);
} else {

    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>