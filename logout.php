<?php
session_start();
require('components/_siteUrl.php');
if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']):
    // $hLocation = isset($_SESSION['currentPageUrl']) ? $_SESSION['currentPageUrl'] : "/";
    session_unset();
    session_destroy();
    header("location: ".url());
    exit();
else:
    header("location: ".url());
    exit();
endif;
?>