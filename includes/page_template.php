<?php
// This file serves as a template for all pages
// It includes the header, sidebar, and sets up the main content area

// Check if page_title is set, otherwise use default
if (!isset($page_title)) {
    $page_title = 'Library Management System';
}

// Include header
include_once 'header.php';

// Include sidebar
include_once 'sidebar.php';
?>

<!-- Main Content Container -->
<div class="main-content">
    <div class="p-4">
        <!-- Page content will be inserted here -->