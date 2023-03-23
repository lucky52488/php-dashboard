<?php
session_start();
require('components/_siteUrl.php');
if (!isset($_SESSION['loggedIn']) or !$_SESSION['loggedIn']) {
    header("location: " . url() . "login.php");
    exit();
}
if ($_SESSION['userRole'] > 2) {
    header("location: " . url());
    exit();
}
$title = "Customers Order Detail";

require("components/_dataConnect.php");
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_GET["order"]) && (isset($_POST["update"]) || isset($_POST["confirm"]))) {
        $orderId = $_GET["order"];
        $total = $_POST["total"];
        // echo $total;
        $discount = $_POST["discount"];
        $net = $_POST["total"] - $_POST["discount"];
        $advance = $_POST["advance"];
        $balance = $net - $_POST["advance"];
        $sql = "UPDATE `orders` SET `discount`='$discount',`net`='$net',`advance`='$advance',`balance`='$balance' WHERE `id`='$orderId'";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $_SESSION['successMsg'] = "Order Updated Successfully";
            if (isset($_POST["confirm"])) {
                $sql = "UPDATE `orders` SET `status`=2 WHERE `id`='$orderId'";
                $result = mysqli_query($conn, $sql);
                header("location: " . url() . "create-order.php?order=" . $orderId);
                exit();
            }elseif (isset($_GET["order"]) && isset($_POST["cancel"])) {
                $orderId = $_GET["order"];
                $sql = "UPDATE `orders` SET `status`= 5 WHERE `id`='$orderId'";
                $result = mysqli_query($conn, $sql);
                if ($result) {
                    $_SESSION['errorMsg'] = "Order Canceled";
                    header("location: " . urlNow());
                    exit();
                }
            }
            header("location: " . urlNow());
            exit();
        }
    } else {
        $_SESSION['errorMsg'] = "Something Went Wrong";
        header("location: " . urlNow());
        exit();
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
                    $order = $orders[0];
                ?>

                    <div class="w-full bg-white shadow rounded-lg mb-4 p-4 sm:p-6 h-full">

                        <a href="<?= url() . 'customers-detail.php' ?>" class="text-cyan-600 hover:text-cyan-700">
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
                                <span class="text-xl font-bold"><?= $order['name'] ?></span>
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
                                <form action="" method="post">
                                    <div class="flex w-full flex-wrap justify-between">
                                        <span>Total Amount</span>
                                        <span>Rs. <?= $order['total'] ?>/-</span>
                                        <input type="hidden" name="total" value="<?= $order['total'] ?>">
                                    </div>
                                    <hr>
                                    <div class="flex w-full flex-wrap justify-between">
                                        <span>Discount</span>
                                        <input value="<?= $order['discount'] ?>" class="text-end text-red-600" type="number" step="any" name="discount" id="discount" placeholder="Enter Amount">
                                    </div>
                                    <hr>
                                    <div class="flex w-full flex-wrap justify-between">
                                        <span>Net Amount</span>
                                        <span>Rs. <?= $order['net'] ?>/-</span>
                                        <input type="hidden" name="net" value="<?= $order['net'] ?>">
                                    </div>
                                    <hr>
                                    <div class="flex w-full flex-wrap justify-between">
                                        <span>Advance Received</span>
                                        <input value="<?= $order['advance'] ?>" class="text-end text-red-600" type="number" step="any" name="advance" id="advance" placeholder="Enter Amount">
                                    </div>
                                    <hr>
                                    <div class="flex w-full flex-wrap justify-between">
                                        <span>Balance</span>
                                        <span>Rs. <?= $order['balance'] ?>/-</span>
                                        <input type="hidden" name="balance" value="<?= $order['balance'] ?>">
                                    </div>
                                    <?php if($order['status']!=5): ?>
                                    <div class="flex w-full flex-wrap justify-center gap-5">
                                        <?php if($order['status']==1): ?>
                                        <button name="update" type="submit" class="bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 rounded-md py-3 px-8 text-center text-base font-semibold text-white outline-none">Update</button>
                                        <button name="confirm" type="submit" class="bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 rounded-md py-3 px-8 text-center text-base font-semibold text-white outline-none">Proceed</button>
                                        <?php endif ?>
                                        <button name="cancel" type="submit" class="bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-200 rounded-md py-3 px-8 text-center text-base font-semibold text-white outline-none">Cancel</button>
                                    </div>
                                    <?php endif ?>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="bg-white shadow rounded-lg mb-4 p-4 sm:p-6 h-full">
                        <div class="flex justify-between w-full">
                            <h3 class="text-2xl font-bold leading-none text-cyan-600 mb-4">All Customers Order</h3>
                            <div class="pt-2 relative text-gray-600 my-2">
                                <input class="border-2 border-gray-300 bg-white h-10 px-5 pr-16 rounded-lg text-sm focus:outline-none" type="search" name="search" placeholder="Search">
                                <button type="submit" class="absolute right-0 top-0 mt-5 mr-4">
                                    <svg class="text-gray-600 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 56.966 56.966" style="enable-background:new 0 0 56.966 56.966;" xml:space="preserve" width="512px" height="512px">
                                        <path d="M55.146,51.887L41.588,37.786c3.486-4.144,5.396-9.358,5.396-14.786c0-12.682-10.318-23-23-23s-23,10.318-23,23  s10.318,23,23,23c4.761,0,9.298-1.436,13.177-4.162l13.661,14.208c0.571,0.593,1.339,0.92,2.162,0.92  c0.779,0,1.518-0.297,2.079-0.837C56.255,54.982,56.293,53.08,55.146,51.887z M23.984,6c9.374,0,17,7.626,17,17s-7.626,17-17,17  s-17-7.626-17-17S14.61,6,23.984,6z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <?php
                        $result = $conn->query("SELECT * FROM `orders` ORDER BY `id` DESC");
                        $orders = $result->fetch_all(MYSQLI_ASSOC);
                        foreach ($orders as $key => $order) :
                        ?>
                            <div class="flex flex-wrap bg-white shadow rounded-lg mb-4 p-4 sm:p-6 h-full">
                                <div class="w-1/2">
                                    <span class="text-xl font-bold"><?= $order['name'] ?></span>
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
                                        <span class="font-bold">Status:</span> <span class="text-red-600 font-semibold"><?= $order['status'] == 1 ? 'Order Pending' : ($order['status'] == 2 ? 'Order Preparing' : ($order['status'] == 3 ? 'Ready To Ship' : ($order['status'] == 4 ? 'Order Completed' : 'Order Canceled'))) ?></span>
                                    </div>
                                    <div class="mt-5 w-full text-end">
                                        <a class="bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 rounded-md py-2 px-3 text-center text-base font-semibold text-white outline-none" href="?order=<?= $order['id'] ?>">Details</a>
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