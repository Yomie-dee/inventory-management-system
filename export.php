<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'InventoryManager.php';

try {
    $inventory = new InventoryManager();
    $items = $inventory->getAllItems();

    // 1. Force browser file download headers
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="inventory_export.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // 2. Open output stream
    $output = fopen('php://output', 'w');

    // 3. Write column headers
    fputcsv($output, ['ID', 'Item Name', 'Quantity', 'Supplier', 'Category', 'Date Added']);

    // 4. Write database records
    foreach ($items as $row) {
        fputcsv($output, [
            $row['id'],
            $row['item_name'],
            $row['quantity'],
            $row['supplier'],
            $row['category'],
            $row['created_at']]);
    }

    fclose($output);
    exit();

} catch (Exception $e) {
    die("Export Failed: " . $e->getMessage());
}
?>