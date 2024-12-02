<?php
session_start();
require_once '../Controllers/category_controller.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cat_id = $_POST['cat_id'];
    $cat_name = $_POST['cat_name'];

    if (!empty($cat_id) && !empty($cat_name)) {
        $categoryController = new CategoryController();
        if ($categoryController->updateCategory($cat_id, $cat_name)) {
            $_SESSION['message'] = "Category updated successfully!";
        } else {
            $_SESSION['message'] = "Failed to update category.";
        }
    } else {
        $_SESSION['message'] = "Category name is required.";
    }
}

header("Location: ../views/categories.php");
exit();
?>
