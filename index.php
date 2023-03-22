<?php
session_start();
require('components/_siteUrl.php');
$title = "Paradise Dashboard";
if (!isset($_SESSION['loggedIn']) or !$_SESSION['loggedIn']) {
    header("location: " . url() . "login.php");
    exit();
}
require("components/_dataConnect.php");
require('components/_header.php');
?>
<!-- This is an example component -->
<div>
    <?php require('components/_navbar.php') ?>

    <div class="flex overflow-hidden bg-white pt-16">

        <?php require('components/_sidebar.php') ?>

        <div id="main-content" class="h-full w-full bg-gray-50 relative overflow-y-auto lg:ml-64">
            <div class="pt-6 px-4">
                <?php if (isset($_SESSION['successMsg']) && $_SESSION['successMsg']) {
                    require('components/_successMsg.php');
                    $_SESSION['successMsg'] = false;
                } ?>
                <?php if (isset($_SESSION['errorMsg']) && $_SESSION['errorMsg']) {
                    require('components/_errorMsg.php');
                    $_SESSION['errorMsg'] = false;
                } ?>
                <div class="bg-white shadow rounded-lg mb-4 p-4 sm:p-6 h-full">
                    <h3 class="text-2xl font-bold leading-none text-gray-900 mb-4">Latest Customers</h3>
                    <?php
                    $result = $conn->query("SELECT * FROM `orders` ORDER BY `id` DESC LIMIT 5");
                    $orders = $result->fetch_all(MYSQLI_ASSOC);
                    foreach ($orders as $key => $order) :
                    ?>
                        <div class="flex flex-wrap bg-white shadow rounded-lg mb-4 p-4 sm:p-6 h-full">
                            <div class="w-1/2">
                                <span class="text-xl"><?= $order['name'] ?></span>
                            </div>
                            <div class="w-1/2 text-end">
                                OrderNo: <?= $order['id'] ?>
                            </div>
                            <div class="lg:w-1/2 w-full">
                                <div class="w-full">
                                    <?= $order['mobile'] ?>
                                </div>
                                <div class="w-full">
                                    <?= $order['email'] ?>
                                </div>
                                <div class="w-full">
                                    <?= $order['address'] ?>
                                </div>
                            </div>
                            <div class="lg:w-1/2 w-full lg:text-end">
                                <div class="w-full">
                                    Order Date: <?= $order['date'] ?>
                                </div>
                                <div class="w-full">
                                    Delivery Date & Time: <?= $order['dod'] ?>
                                </div>
                                <div class="w-full">
                                    Reminder Date: <?= $order['reminder'] ?>
                                </div>
                                <div class="w-full">
                                    Status: <span class="text-red-700"><?= $order['status'] == 1 ? 'Order Pending' : ($order['status'] == 2 ? 'Order Preparing' : ($order['status'] == 3 ? 'Ready To Ship' : ($order['status'] == 4 ? 'Order Completed' : 'Order Canceled'))) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require('components/_footer.php'); ?>