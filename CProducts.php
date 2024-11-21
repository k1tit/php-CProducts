<?php
class CProducts {
    private $database;
    private $host;
    private $user;
    private $password;
    private $mysqli;

    public function __construct($host, $user, $password, $database) {
        $this->database = new mysqli($host, $user, $password, $database);
        if ($this->database->connect_error) {
            die("Connection failed: " . $this->database->connect_error);
        }
    }

    public function getProducts($limit) {
        $stmt = $this->database->prepare("SELECT * FROM Products WHERE IS_HIDDEN = 0 ORDER BY DATE_CREATE DESC LIMIT ?");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function hideProduct($id) {
        $stmt = $this->database->prepare("UPDATE Products SET IS_HIDDEN = 1 WHERE ID = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function updateQuantity($id, $quantity) {
        if ($quantity < 0) {
            return false;
        }
    
        $stmt = $this->database->prepare("UPDATE Products SET PRODUCT_QUANTITY = ? WHERE ID = ?");
        $stmt->bind_param("ii", $quantity, $id);
    
        return $stmt->execute();
    }
     
}
?>