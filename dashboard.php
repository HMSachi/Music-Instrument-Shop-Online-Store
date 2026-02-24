<?php
/**
 * Dashboard Router
 * Redirects users to appropriate dashboard based on role
 */

require_once 'config/config.php';

requireLogin();

// Redirect based on user role
if (isAdmin()) {
    redirect('admin/dashboard.php');
} elseif (isStaff()) {
    redirect('staff/dashboard.php');
} else {
    redirect('customer/dashboard.php');
}
?>
