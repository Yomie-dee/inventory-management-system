<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'InventoryManager.php';

$message = "";
$messageType = "";
$searchResults = null;

try {
    $inventory = new InventoryManager();

    // Handle Adding New Stock
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_item'])) {
        $itemName = trim($_POST['item_name'] ?? '');
        $quantity = trim($_POST['quantity'] ?? '');
        $supplier = trim($_POST['supplier'] ?? '');
        $category = trim($_POST['category'] ?? '');

        if (empty($itemName) || empty($quantity) || empty($supplier) || empty($category)) {
            throw new Exception("All fields are required.");
        }
        if (!filter_var($quantity, FILTER_VALIDATE_INT) || (int)$quantity < 1) {
            throw new Exception("Quantity must be a positive whole number.");
        }

        $inventory->addItem($itemName, (int)$quantity, $supplier, $category);
        $message = "Item successfully recorded in MySQL and backed up to CSV.";
        $messageType = "success";
    }

    // Handle Search
    if (isset($_GET['search_supplier'])&& trim($_GET['search_supplier']) !== '') {
        $searchResults = $inventory->searchBySupplier(trim($_GET['search_supplier']));
    } else {
        $searchResults=$inventory->getAllItems();
    }
    
} catch (Exception $e) {
    $message = $e->getMessage();
    $messageType = "danger";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouse Inventory Management System</title>
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #27ae60;
            --danger: #c0392b;
            --light: #ecf0f1;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        .card {
            background: #fff;
            padding: 20px 25px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        h2 {
            margin-top: 0;
            color: var(--primary);
            border-bottom: 2px solid var(--light);
            padding-bottom: 10px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            background: var(--primary);
            color: #fff;
            padding: 10px 18px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }
        .btn-success { background: var(--accent); }
        .alert {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-danger { background: #f8d7da; color: #721c24; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 10px; text-align: left; }
        th { background: var(--primary); color: #fff; }
        tr:nth-child(even) { background: #f9f9f9; }
    </style>
</head>
<body>
<div class="container">

    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType; ?>"><?= htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <!-- Add Stock Form -->
    <div class="card">
        <h2>Record New Stock Item</h2>
        <form method="POST" action="index.php">
            <div class="form-group">
                <label for="item_name">Item Name</label>
                <input type="text" id="item_name" name="item_name" required>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" id="quantity" name="quantity" min="1" required>
            </div>
            <div class="form-group">
                <label for="supplier">Supplier</label>
                <input type="text" id="supplier" name="supplier" required>
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" required>
                    <option value="">-- Select Category --</option>
                    <option value="Electronics">Electronics</option>
                    <option value="Raw Materials">Raw Materials</option>
                    <option value="Packaging">Packaging</option>
                    <option value="Safety Gear">Safety Gear</option>
                    <option value="Tools">Tools</option>
                </select>
            </div>
            <button type="submit" name="add_item" class="btn">Add Item to Inventory</button>
        </form>
    </div>

    <!-- Search & Export Options -->
    <div class="card">
        <h2>Lookup & Data Export</h2>
        <form method="GET" action="index.php" style="margin-bottom: 15px;">
            <div class="form-group">
                <label for="search_supplier">Search Items by Supplier</label>
                <input type="text" id="search_supplier" name="search_supplier" placeholder="Enter supplier name..." required>
            </div>
            <button type="submit" class="btn">Search</button>
            <a href="index.php" class="btn" style="background:#7f8c8d;">Reset</a>
            <a href="export.php" class="btn btn-success" style="float: right;">Export Full Inventory (CSV)</a>
        </form>

        <?php if ($searchResults !== null): ?>
            <h3>Search Results</h3>
            <?php if (count($searchResults) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Supplier</th>
                            <th>Category</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($searchResults as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['id']); ?></td>
                                <td><?= htmlspecialchars($item['item_name']); ?></td>
                                <td><?= htmlspecialchars($item['quantity']); ?></td>
                                <td><?= htmlspecialchars($item['supplier']); ?></td>
                                <td><?= htmlspecialchars($item['category']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No inventory records found for that supplier.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

</div>
</body>
</html>