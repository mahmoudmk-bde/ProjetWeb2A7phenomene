<?php

require_once __DIR__ . '/../db_config.php';

class AdminOrderController {
    private $db;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = config::getConnexion();
    }

    public function index() {
        $orders = [];
        try {
            $stmt = $this->db->prepare("SELECT id, name, email, phone, address, city, shipping, total, created_at FROM orders ORDER BY created_at DESC");
            $stmt->execute();
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {}
        return $orders;
    }

    public function show() {
        if (!isset($_GET['id'])) {
            header('Location: ?controller=AdminOrder&action=index');
            return;
        }
        $id = (int)$_GET['id'];
        $order = null;
        $items = [];
        try {
            $stmtO = $this->db->prepare("SELECT id, name, email, phone, address, city, shipping, total, created_at FROM orders WHERE id = :id");
            $stmtO->bindParam(':id', $id);
            $stmtO->execute();
            $order = $stmtO->fetch(PDO::FETCH_ASSOC);
            if ($order) {
                $sqlItems = "SELECT item_id, name, price, qty FROM order_items WHERE order_id = :oid";
                try {
                    $stmtI = $this->db->prepare($sqlItems);
                    $stmtI->bindParam(':oid', $id);
                    $stmtI->execute();
                    $items = $stmtI->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $eI) {
                    $stmtIF = $this->db->prepare("SELECT item_id, name, price, qty FROM oder_items WHERE order_id = :oid");
                    $stmtIF->bindParam(':oid', $id);
                    $stmtIF->execute();
                    $items = $stmtIF->fetchAll(PDO::FETCH_ASSOC);
                }
            }
        } catch (PDOException $e) {}
        include __DIR__ . '/../view/backoffice/orders/order-detail.php';
    }
}
?>
