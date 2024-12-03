<?php
session_start();

// Ensure the admin is logged in
require_once '../Controllers/seller_controller.php'; // Include the controller to handle seller operations
$sellerController = new SellerController();

// Fetch all sellers with pending verification status
$pendingSellers = $sellerController->getPendingSellers();

// Handle approve/reject directly in this file
if (isset($_GET['action'], $_GET['id'], $_GET['status'])) {
    $action = $_GET['action'];
    $userId = $_GET['id'];
    $status = $_GET['status'];

    if ($action == 'approve') {
        // Approve the seller
        $sellerController->updateVerificationStatus($userId, 'Approved');
        header("Location: admin.php"); // Redirect after the action is completed
        exit();
    } elseif ($action == 'reject') {
        // Reject the seller
        $sellerController->updateVerificationStatus($userId, 'Rejected');
        header("Location: admin.php"); // Redirect after the action is completed
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        /* General Layout */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            display: flex;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            background-color: #6a0572;
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar h2 {
            color: #ffdc73;
            margin-bottom: 30px;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar ul li {
            margin: 15px 0;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            font-size: 1.2rem;
            transition: color 0.3s;
        }

        .sidebar ul li a:hover {
            color: #ffdc73;
        }

        /* Main Content Area */
        .content {
            margin-left: 250px;
            padding: 20px;
            width: 100%;
        }

        .content h1 {
            color: #6a0572;
        }

        .content h2 {
            margin-top: 20px;
            color: #6a0572;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
        }

        button {
            padding: 8px 12px;
            cursor: pointer;
            border: none;
            color: white;
            background-color: #007bff;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 800px;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 25px;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        iframe {
            width: 100%;
            height: 500px;
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="services.php">Product Management</a></li>
            <li><a href="categories.php">Category Management</a></li>
            <li><a href="logout.php">Log out</a></li>
        </ul>
    </aside>

    <main class="content">
        <div id="content-area">
            <h1>Welcome to the Admin Dashboard</h1>
            <p>Select an option from the sidebar to manage the system.</p>

            <!-- Seller Verification Management -->
            <h2>Pending Seller Verifications</h2>
            <?php if (empty($pendingSellers)): ?>
                <p>No sellers are pending verification at the moment.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Seller Name</th>
                            <th>Verification Status</th>
                            <th>Document Submitted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Ensure only one entry per seller is shown
                        $displayedSellers = [];
                        foreach ($pendingSellers as $seller) {
                            // Check if this seller has already been displayed
                            if (!in_array($seller['user_name'], $displayedSellers)) {
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($seller['user_name']) . '</td>';
                                echo '<td>' . htmlspecialchars($seller['verification_status']) . '</td>';
                                echo '<td><a href="javascript:void(0);" class="view-document" data-document="' . htmlspecialchars($seller['document_path']) . '">View Document</a></td>';
                                echo '<td>';
                                if ($seller['verification_status'] === 'Pending') {
                                    echo '<a href="admin.php?action=approve&id=' . $seller['user_id'] . '&status=approved"><button>Approve</button></a>';
                                    echo '<a href="admin.php?action=reject&id=' . $seller['user_id'] . '&status=rejected"><button>Reject</button></a>';
                                } else {
                                    echo '<span>Already ' . htmlspecialchars($seller['verification_status']) . '</span>';
                                }
                                echo '</td>';
                                echo '</tr>';

                                // Add the seller to the displayed list to avoid duplicates
                                $displayedSellers[] = $seller['user_name'];
                            }
                        }
                        ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>

    <!-- Modal to display the document -->
    <div id="documentModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Document Preview</h2>
            <iframe id="documentIframe" src="" frameborder="0"></iframe>
        </div>
    </div>

    <script>
        // Get the modal and the document iframe
        var modal = document.getElementById("documentModal");
        var iframe = document.getElementById("documentIframe");

        // Get all View Document links
        var viewDocumentLinks = document.querySelectorAll(".view-document");

        // Open the modal and load the document in iframe
        viewDocumentLinks.forEach(function(link) {
            link.addEventListener("click", function() {
                var documentPath = this.getAttribute("data-document");
                iframe.src = documentPath;  // Set the iframe's source to the document's path
                modal.style.display = "block";  // Display the modal
            });
        });

        // Close the modal when the user clicks on the close button
        var closeModal = document.querySelector(".close");
        closeModal.onclick = function() {
            modal.style.display = "none";
            iframe.src = "";  // Clear iframe source when closing modal
        };

        // Close the modal if the user clicks outside of it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
                iframe.src = "";  // Clear iframe source when closing modal
            }
        };
    </script>

</body>
</html>
