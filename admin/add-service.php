<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$errors = [];
$success = '';

$gst_rate = 0.18;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_name   = trim($_POST['service_name']);
    $description    = trim($_POST['description']);
    $price          = floatval($_POST['price']);
    $billing_period = intval($_POST['billing_period']);
    $billing_unit   = $_POST['billing_unit'];

    // Validation
    if ($service_name === '') {
        $errors[] = "Service name is required.";
    }

    if ($price <= 0) {
        $errors[] = "Price must be greater than zero.";
    }

    if (!in_array($billing_unit, ['month', 'year'])) {
        $errors[] = "Invalid billing unit selected.";
    }

    $gst_price = $price + $price * $gst_rate;

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO services 
            (service_name, description, price,gst_price, billing_period, billing_unit) 
            VALUES (:service_name, :description, :price, :gst_price, :billing_period, :billing_unit)");

        $stmt->execute([
            'service_name'   => $service_name,
            'description'    => $description,
            'price'          => $price,
            'gst_price'      => $gst_price,
            'billing_period' => $billing_period,
            'billing_unit'   => $billing_unit,
        ]);
        
        header("Location: services.php");
        exit();
        // $success = "Service added successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add New Service</title>
    <style>
        body {
            font-family: Arial;
            padding: 20px;
        }
        form {
            max-width: 600px;
            margin: auto;
            background: #f8f8f8;
            padding: 25px;
            border-radius: 10px;
        }
        label {
            display: block;
            margin-top: 15px;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
        }
        .btn {
            margin-top: 20px;
            background-color: rgb(55, 162, 98);
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: rgb(52, 118, 88);
        }
        .error {
            color: red;
            margin-top: 10px;
        }
        .success {
            color: green;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<h2>Add New Service</h2>

<?php if (!empty($errors)): ?>
    <div class="error">
        <ul>
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="success"><?= $success ?></div>
<?php endif; ?>

<form method="POST">
    <label for="service_name">Service Name *</label>
    <input type="text" name="service_name" id="service_name" required>

    <label for="description">Description</label>
    <textarea name="description" id="description" rows="4"></textarea>

    <label for="price">Price (₹) *</label>
    <input type="number" name="price" id="price" step="0.01" min="0" required>

    <label for="billing_period">Billing Period *</label>
    <input type="number" name="billing_period" id="billing_period" min="1" value="1" required>

    <label for="billing_unit">Billing Unit *</label>
    <select name="billing_unit" id="billing_unit" required>
        <option value="month">Month</option>
        <option value="year">Year</option>
    </select>

    <button type="submit" class="btn">Add Service</button>
</form>

</body>
</html>
