<?php
session_start();
require('components/_siteUrl.php');
if (!isset($_SESSION['loggedIn']) or !$_SESSION['loggedIn']) {
    header("location: " . url() . "login.php");
    exit();
}
if ($_SESSION['userRole'] > 2 && $_SESSION['userRole'] != 4) {
    header("location: " . url());
    exit();
}
$title = "Customers Order Detail";

require("components/_dataConnect.php");
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_GET["order"]) && isset($_POST["confirm"])) {
        $orderId = $_GET["order"];
        $sql = "UPDATE `orders` SET `status`=4 WHERE `id`='$orderId'";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $_SESSION['successMsg'] = "Order Proceeded Successfully";
            header("location: " . url() . 'ready-to-ship.php');
            exit();
        }
    } elseif (isset($_GET["order"]) && isset($_POST["cancel"])) {
        $orderId = $_GET["order"];
        $sql = "UPDATE `orders` SET `status`= 5 WHERE `id`='$orderId'";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $_SESSION['errorMsg'] = "Order Canceled";
            header("location: " . url() . 'ready-to-ship.php');
            exit();
        }
    }
}

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
                <?php if (isset($_GET['order']) && $_GET['order']) :
                    $orderId = $_GET['order'];
                    $result = $conn->query("SELECT * FROM `orders` WHERE `id`='$orderId'");
                    $orders = $result->fetch_all(MYSQLI_ASSOC);
                    $order = $orders[0]; ?>

                    <div class="w-full bg-white shadow rounded-lg mb-4 p-4 sm:p-6 h-full">

                        <a href="<?= url() . 'ready-to-ship.php' ?>" class="text-cyan-600 hover:text-cyan-700">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-box-arrow-left" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0v2z" />
                                <path fill-rule="evenodd" d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3z" />
                            </svg>
                        </a>

                        <div class="flex items-center justify-center mb-4">
                            <h3 class="text-xl font-bold leading-none text-gray-900">Order No. <?= $orderId ?></h3>
                        </div>
                        <div class="flex flex-wrap w-full">
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
                                <div class="w-full">
                                    GST No: <?= $order['gst'] ? $order['gst'] : 'N/A' ?>
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
                                    <?= $order['alternate-no'] ? 'Alternate Mobile: ' . $order['alternate-no'] : '' ?>
                                </div>
                            </div>
                            <div class="w-full border-2 my-10">
                                <?php
                                $itemJ = json_decode($order['items'], true);
                                $packageJ = json_decode($order['packages'], true);
                                ?>
                                <ul class="my-3 p-5">
                                    <span class="text-xl">Items List</span>
                                    <?php
                                    // print_r($packageJ);
                                    // print_r($data['id']);
                                    // print_r($data['name']);
                                    // print_r($packageJ['nob'][$key]);
                                    foreach ($itemJ['id'] as $key => $itemId) {
                                        $itemQuery = $conn->query("SELECT * FROM `item` WHERE `id`='$itemId'");
                                        $itemData = $itemQuery->fetch_all(MYSQLI_ASSOC);
                                        $data = $itemData[0];
                                        $price = $data['rate'];
                                    ?>
                                        <li class="w-full flex justify-between">
                                            <div class="w-1/3"><?= $key + 1 ?>. <?= $data['name'] ?> | Rate: Rs. <?= $price ?>/<?= $data['uom'] ?></div>
                                            <div class="w-1/3 text-center">Quantity: <?= $itemJ['qty'][$key] ?> <?= $data['uom'] ?></div>
                                            <div class="w-1/3 text-end">Price: <?= $itemJ['qty'][$key] * $price ?>/-</div>
                                        </li>
                                        <hr>
                                    <?php } ?>
                                </ul>
                                <ul class="my-3 p-5">
                                    <span class="text-xl">Packages List</span>

                                    <?php
                                    // print_r($packageJ);
                                    // print_r($data['id']);
                                    // print_r($data['name']);
                                    // print_r($packageJ['nob'][$key]);
                                    foreach ($packageJ['id'] as $key => $itemId) {
                                        $itemQuery = $conn->query("SELECT * FROM `package` WHERE `id`='$itemId'");
                                        $itemData = $itemQuery->fetch_all(MYSQLI_ASSOC);
                                        $data = $itemData[0];
                                        $price = $data['total'];
                                    ?>
                                        <li class="w-full flex justify-between">
                                            <div class="w-1/3"><?= $key + 1 ?>. <?= $data['name'] ?> | Price: Rs. <?= $price ?>/-</div>
                                            <div class="w-1/3 text-center">Quantity: <?= $packageJ['qty'][$key] ?></div>
                                            <div class="w-1/3 text-end">Price: <?= $packageJ['qty'][$key] * $price ?>/-</div>
                                        </li>
                                        <hr>
                                    <?php } ?>
                                </ul>
                            </div>
                            <div class="w-full">
                                <div class="flex w-full flex-wrap justify-between">
                                    <span>Total Amount</span>
                                    <span>Rs. <?= $order['total'] ?>/-</span>
                                </div>
                                <hr>
                                <div class="flex w-full flex-wrap justify-between">
                                    <span>Discount</span>
                                    <span class="text-red-600">Rs. <?= $order['discount'] ?>/-</span>
                                </div>
                                <hr>
                                <div class="flex w-full flex-wrap justify-between">
                                    <span>Net Amount</span>
                                    <span>Rs. <?= $order['net'] ?>/-</span>
                                </div>
                                <hr>
                                <div class="flex w-full flex-wrap justify-between">
                                    <span>Advance Received</span>
                                    <span class="text-red-600">Rs. <?= $order['advance'] ?>/-</span>
                                </div>
                                <hr>
                                <div class="flex w-full flex-wrap justify-between">
                                    <span>Balance</span>
                                    <span>Rs. <?= $order['balance'] ?>/-</span>
                                </div>
                                <div class="flex w-full flex-wrap justify-center gap-5">
                                    <form action="" method="post">
                                        <?php if ($_SESSION['userRole'] < 3) : ?>
                                            <button name="cancel" type="submit" class="bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-200 rounded-md py-3 px-8 text-center text-base font-semibold text-white outline-none">Cancel</button>
                                        <?php endif ?>
                                        <button name="confirm" type="submit" class="bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 rounded-md py-3 px-8 text-center text-base font-semibold text-white outline-none">Order Completed</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="bg-white shadow rounded-lg mb-4 p-4 sm:p-6 h-full">
                        <h3 class="text-2xl font-bold leading-none text-gray-900 mb-4">Orders Ready to Ship</h3>
                        <?php
                        $result = $conn->query("SELECT * FROM `orders` WHERE `status`=3 ORDER BY `id` DESC");
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
                                    <div class="w-full text-end">
                                        <a class="bg-red" href="?order=<?= $order['id'] ?>">Details</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>
<?php require('components/_footer.php'); ?>