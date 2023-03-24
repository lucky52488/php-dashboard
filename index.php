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
                    <div class="flex justify-between w-full">
                        <h3 class="text-2xl font-bold leading-none text-cyan-600 mb-4">Latest Orders</h3>
                    </div>
                    <?php
                    $result = $conn->query("SELECT * FROM `orders` ORDER BY `id` DESC LIMIT 5");
                    $orders = $result->fetch_all(MYSQLI_ASSOC);
                    foreach ($orders as $key => $order) :
                    ?>
                        <a href="<?= url().'orders-detail.php?order='.$order['id'] ?>">
                            <div class="flex flex-wrap bg-slate-50 hover:bg-slate-200 duration-300 shadow rounded-lg mb-4 p-4 sm:p-6 h-full">
                                <div class="w-1/3">
                                    <span class="text-xl font-bold  text-blue-900"><?= $order['name'] ?></span>
                                </div>
                                <div class="w-1/3 text-center font-bold text-blue-900">
                                    <div>OrderNo: <?= $order['id'] ?></div>
                                </div>
                                <div class="w-1/3 text-end text-blue-900">
                                    <span class="font-bold">Status:</span>
                                    <?php
                                    if ($order['status'] == 1) {
                                        echo '<span class="text-orange-600 font-semibold">Order Pending</span>';
                                    } elseif ($order['status'] == 2) {
                                        echo '<span class="text-yellow-500 font-semibold">Order Preparing</span>';
                                    } elseif ($order['status'] == 3) {
                                        echo '<span class="text-blue-600 font-semibold">Ready To Ship</span>';
                                    } elseif ($order['status'] == 4) {
                                        echo '<span class="text-green-600 font-semibold">Order Completed</span>';
                                    } else {
                                        echo '<span class="text-red-600 font-semibold">Order Canceled</span>';
                                    }
                                    ?>
                                </div>
                                <div class="sm:w-1/2 w-full mt-5 sm:mt-0">
                                    <div class="w-full my-1 font-semibold">
                                        <?= $order['mobile'] ?>
                                    </div>
                                    <div class="w-full font-semibold my-1">
                                        <?= $order['email'] ?>
                                    </div>
                                    <div class="w-full font-semibold my-1">
                                        <?= $order['address'] ?>
                                    </div>
                                </div>
                                <div class="sm:w-1/2 w-full sm:text-end mt-5 sm:mt-0">
                                    <div class="w-full my-1">
                                        <div class="font-semibold">Order Date & Time</div><?= $order['date'] ?>
                                    </div>
                                    <div class="w-full my-1">
                                        <div class="font-semibold">Delivery Date & Time</div><?= $order['dod'] ?>
                                    </div>
                                    <div class="w-full my-1 text-end">
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require('components/_footer.php'); ?>