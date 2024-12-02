<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Website Title</title>
    <link rel="stylesheet" href="../css/styles.css"> <!-- Link to your external CSS file -->
</head>

<body>
    <header>

    </header>

    <main>

        <section id="contact">
            <h2>Contact Us</h2>
            <form id="paymentForm">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email-address" required />
                </div>
                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="tel" id="amount" required />
                </div>
                <div class="form-group">
                    <label for="first-name">First Name</label>
                    <input type="text" id="first-name" />
                </div>
                <div class="form-group">
                    <label for="last-name">Last Name</label>
                    <input type="text" id="last-name" />
                </div>
                <div class="form-submit">
                    <button type="submit" onclick="payWithPaystack()">Pay</button>
                </div>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Your Website</p>
    </footer>

    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="pay.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script></body>

</html>