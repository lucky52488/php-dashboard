<?php
session_start();
if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']):
    // $hLocation = isset($_SESSION['currentPageUrl']) ? $_SESSION['currentPageUrl'] : "/";
    session_unset();
    session_destroy();
    header("location: /php-dashboard/");
else:
    header("location: /php-dashboard/login.php");
endif;
?>