<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Validate the payment amount
if (!isset($_POST['total_amount']) || empty($_POST['total_amount'])) {
    die("Invalid payment request.");
}

$totalAmount = floatval($_POST['total_amount']); // Convert to float for accuracy

// Retrieve or default to a selected currency
$currency = isset($_POST['currency']) ? strtoupper(trim($_POST['currency'])) : 'USD'; // Default to USD

// List of supported currencies (as per your Paystack configuration)
$supportedCurrencies = ['USD', 'GHS', 'NGN', 'KES', 'ZAR'];
if (!in_array($currency, $supportedCurrencies)) {
    die("Unsupported currency: $currency");
}

// Define exchange rates (or integrate with an API for real-time rates)
$exchangeRates = [
    'USD' => 12.5, // Example: 1 USD = 12.5 GHS
    'NGN' => 0.03, // Example: 1 NGN = 0.03 GHS
    'KES' => 0.09, // Example: 1 KES = 0.09 GHS
    'ZAR' => 0.65, // Example: 1 ZAR = 0.65 GHS
    'GHS' => 1.0   // GHS remains the base currency
];

// Convert the total amount to GHS
if (isset($exchangeRates[$currency])) {
    $totalAmountGHS = $totalAmount * $exchangeRates[$currency];
} else {
    die("Currency conversion error.");
}

// Generate a unique transaction reference
$reference = uniqid("txn_");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <script src="https://js.paystack.co/v1/inline.js"></script>
</head>
<body>
<h1>Processing Payment...</h1>
<script>
    let handler = PaystackPop.setup({
        key: 'pk_test_fd2673b977195c947f073a83c59cb8a57e173e87', // Replace with your Paystack public key
        email: "<?php echo htmlspecialchars($_SESSION['user_email']); ?>", // Sanitize user email
        amount: <?php echo round($totalAmountGHS * 100); ?>, // Convert to pesewas (GHS smallest unit)
        currency: "GHS", // All payments are processed in GHS
        ref: "<?php echo $reference; ?>",
        callback: function(response) {
            // Handle successful payment
            alert("Payment successful. Transaction reference: " + response.reference);
            window.location.href = "../views/cart.php?reference=" + response.reference;
        },
        onClose: function() {
            alert("Payment process canceled.");
        }
    });
    handler.openIframe();
</script>
</body>
</html>
