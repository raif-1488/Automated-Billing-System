<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../vendor/autoload.php';

$currentPage = basename($_SERVER['PHP_SELF']);

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;

use Ramsey\Uuid\Uuid; //for consumer_id
use RandomLib\Factory as RandomLibFactory;//for password generation
use SecurityLib\Strength;

$phoneUtil = PhoneNumberUtil::getInstance();
$arrRegions = $phoneUtil->getSupportedRegions();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $consumer_id = Uuid::uuid4()->toString();
    $name = htmlspecialchars(trim($_POST['name']));
    $email   = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $contact = trim($_POST['contact']);
    //$region = $_POST['country_code'];
    $address = htmlspecialchars(trim($_POST['address']));
    
    echo "<pre>";
        print_r([
            'name' => $name,
            'email' => $email,
            'contact' => $contact,
            'address' => $address
        ]);
    echo "</pre>";


    // Validation purpose
    $parseNumber = $phoneUtil->parse($contact,null);
    if($phoneUtil->isValidNumber($parseNumber)){
        $contact = $phoneUtil->format($parseNumber, PhoneNumberFormat::INTERNATIONAL); //E164
        echo "Number is valid";
    }
     else{
        $errors[] = "Invalid Number.";
     }
     
    
    if (empty($name)) $errors[] = "Name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email.";
    

    if (empty($errors)) {
        try {

            $factory = new RandomLibFactory;
            $generator = $factory->getGenerator(new Strength(Strength::MEDIUM));
            $randomPassword = $generator->generateString(8); 
            $hashedPassword = password_hash($randomPassword, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare("INSERT INTO consumers ( name, email, contact, address) VALUES ( ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $contact, $address]);
            
            $success = "Consumer added successfully!<br><strong>Email:</strong> {$email}<br><strong>Password:</strong> {$randomPassword}";
            sendCredentials($email, $name, $randomPassword); 

        } catch (PDOException $e) {
            $errors[] = "Database error: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Consumer</title>
    
    
    <script>
        const inputValidation = (e) => {
        // get value form event
        const value = e.target.value

        // validate value
        const validatedValue = value.replace(/[^0-9]/g, '');

        return validatedValue;
     }
    </script>
    
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0; padding: 0;
        }

        .main-content {
            margin-left: 220px;
            padding: 20px;
        }

        .form-container {
            max-width: 600px;
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 8px;
        }

        .form-row {
            display: flex;
            flex-direction: row;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input,
        .form-group textarea {
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-group textarea {
            resize: vertical;
        }

        .btn-submit {
            background-color: #28a745;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-submit:hover {
            background-color: #218838;
        }

        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .contact-inline {
            display: flex;
            gap: 4px;
            align-items: center;
        }

        .contact-inline select,
        .contact-inline input {
            flex: 1;
        }
    </style>

     
</head>
<body>

<?php include 'includes/sidebar.php'; ?>
<?php include 'includes/navbar.php'; ?>

<div class="main-content">
    <h2>Add New Consumer</h2>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" class="form-container" autocomplete="off">
        <div class="form-row">
            <div class="form-group">
                <label for="name">Name*</label>
                <input type="text" id="name" name="name" required value="<?= htmlspecialchars($name ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="email">Email*</label>
                <input type="email" id="email" name="email" required value="<?= htmlspecialchars($email ?? '') ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="contact">Contact*</label>
                    <input  type="tel" id="phone" name="contact"  required>
                  
            </div>

            <div class="form-group">
                <label for="address">Address*</label>
                <textarea id="address" name="address" rows="3" ><?= htmlspecialchars($address ?? '') ?></textarea>
            </div>
        </div>

        <button type="submit" class="btn-submit">Add Consumer</button>
    </form>
</div>

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/css/intlTelInput.css">
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/intlTelInput.min.js"></script>
    
   
    <!-- <script>
        const input = document.querySelector("#phone");
        window.intlTelInput(input, {
            loadUtils: () => import("https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/utils.js"),
        });
    </script> -->

    <script>
        const input = document.querySelector("#phone");

        const iti = window.intlTelInput(input, {
            initialCountry: "auto",
            geoIpLookup: function(callback) {
            fetch("https://ipinfo.io/json?token=<your_token>")
                .then(resp => resp.json())
                .then(resp => {
                const countryCode = (resp && resp.country) ? resp.country : "us";
                callback(countryCode);
                });
            },
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js", 
           
        
        });


        document.querySelector("form").addEventListener("submit", function () {
           input.value = iti.getNumber();  // Get full number in +91... format
    });
     
    </script>




</body>

    
</html>

