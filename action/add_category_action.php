<?php
session_start();
require_once '../Controllers/category_controller.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cat_name = $_POST['cat_name'];

    if (!empty($cat_name)) {
        $categoryController = new CategoryController();
        if ($categoryController->addCategory($cat_name)) {
            $_SESSION['message'] = "Category added successfully!";
        } else {
            $_SESSION['message'] = "Failed to add category.";
        }
    } else {
        $_SESSION['message'] = "Category name is required.";
    }
}

header("Location: ../views/categories.php");
exit();
?>
