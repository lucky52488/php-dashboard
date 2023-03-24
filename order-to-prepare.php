<?php
session_start();
require('components/_siteUrl.php');
if (!isset($_SESSION['loggedIn']) or !$_SESSION['loggedIn']) {
    header("location: " . url() . "login.php");
    exit();
}
if ($_SESSION['userRole'] > 2 && $_SESSION['userRole'] != 3) {
    header("location: " . url());
    exit();
}
$title = "Customers Order Detail";

require("components/_dataConnect.php");
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_GET["order"]) && isset($_POST["confirm"])) {
        $orderId = $_GET["order"];
        $sql = "UPDATE `orders` SET `status`=3 WHERE `id`='$orderId'";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $_SESSION['successMsg'] = "Order Proceeded Successfully";
            header("location: " . url() . 'order-to-prepare.php');
            exit();
        }
    } elseif (isset($_GET["order"]) && isset($_POST["cancel"])) {
        $orderId = $_GET["order"];
        $sql = "UPDATE `orders` SET `status`= 5 WHERE `id`='$orderId'";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $_SESSION['errorMsg'] = "Order Canceled";
            header("location: " . url() . 'order-to-prepare.php');
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

                    <div class="w-full bg-white shadow-lg border-2 border-cyan-600 rounded-lg mb-4 p-4 sm:p-6 h-full">

                        <a href="<?= url() . 'order-to-prepare.php' ?>" class="text-cyan-600 hover:text-cyan-700">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-box-arrow-left" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0v2z" />
                                <path fill-rule="evenodd" d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3z" />
                            </svg>
                        </a>

                        <div class="flex items-center justify-center mb-4">
                            <h3 class="text-xl font-bold leading-none  text-blue-900">
                                Order No. <?= $orderId ?> | <?php
                                                            if ($order['status'] == 1) {
                                                                echo '<span class="text-orange-600 font-semibold">Pending</span>';
                                                            } elseif ($order['status'] == 2) {
                                                                echo '<span class="text-yellow-500 font-semibold">Preparing</span>';
                                                            } elseif ($order['status'] == 3) {
                                                                echo '<span class="text-blue-600 font-semibold">Ready To Ship</span>';
                                                            } elseif ($order['status'] == 4) {
                                                                echo '<span class="text-green-600 font-semibold">Completed</span>';
                                                            } else {
                                                                echo '<span class="text-red-600 font-semibold">Canceled</span>';
                                                            }
                                                            ?>
                            </h3>
                        </div>
                        <div class="flex flex-wrap w-full">
                            <div class="sm:w-1/2 w-full mt-5 sm:mt-0">
                                <div class="w-full">
                                    <span class="text-xl font-bold text-blue-900"><?= $order['name'] ?></span>
                                </div>
                                <div class="w-full my-1 font-semibold">
                                    <?= $order['mobile'] ?>
                                </div>
                                <div class="w-full my-1 font-semibold">
                                    <?= $order['email'] ?>
                                </div>
                                <div class="w-full my-1 font-semibold">
                                    <?= $order['address'] ?>
                                </div>
                                <div class="w-full">
                                    <span class="font-semibold">GST No: </span><?= $order['gst'] ? $order['gst'] : 'N/A' ?>
                                </div>
                                <div class="w-full my-1">
                                    <?= $order['alternate-no'] ? '<span class="font-semibold">Alternate Mobile: </span>' . $order['alternate-no'] : '' ?>
                                </div>
                            </div>
                            <div class="sm:w-1/2 w-full  mt-5 sm:mt-0 sm:text-end">
                                <div class="w-full my-1">
                                    <div class="font-semibold">Order Date & Time</div><?= $order['date'] ?>
                                </div>
                                <div class="w-full my-1">
                                    <div class="font-semibold">Delivery Date & Time</div><?= $order['dod'] ?>
                                </div>
                                <div class="w-full my-1">
                                    <div class="font-semibold">Reminder Date</div><?= $order['reminder'] ?>
                                </div>
                            </div>
                            <div class="w-full border-2 rounded border-cyan-300 my-10">
                                <?php
                                $itemJ = json_decode($order['items'], true);
                                $packageJ = json_decode($order['packages'], true);
                                ?>
                                <ul class="my-2 px-5 py-2">
                                    <li class="text-xl flex font-semibold text-blue-900">
                                        <span class="w-1/3">ITEMS / PACKAGES</span>
                                        <span class="w-1/3 text-center">Quantity</span>
                                        <span class="pr-2 w-1/3 text-end">Price</span>
                                    </li>
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
                                        <li class="w-full my-1 flex">
                                            <div class="w-1/3"><span class="font-bold">I(<?= $key + 1 ?>). </span><?= $data['name'] ?> | Rate: Rs. <?= $price ?>/<?= $data['uom'] ?></div>
                                            <div class="w-1/3 text-center"><?= $itemJ['qty'][$key] ?> <?= $data['uom'] ?></div>
                                            <div class="w-1/3 text-end">
                                                <span class="text-end"><?= $itemJ['qty'][$key] * $price ?>/-</span>
                                            </div>
                                        </li>

                                    <?php } ?>
                                </ul>
                                <ul class="my-2 px-5 py-2">
                                    <?php
                                    // print_r($packageJ);
                                    // print_r($data['id']);
                                    // print_r($data['name']);
                                    // print_r($packageJ['nob'][$key]);
                                    foreach ($packageJ['id'] as $key => $itemId) {
                                        $itemQuery = $conn->query("SELECT * FROM `package` WHERE `id`='$itemId'");
                                        $itemData = $itemQuery->fetch_all(MYSQLI_ASSOC);
                                        $data = $itemData[0];
                                        $price = $data['total']; ?>
                                        <li class="w-full my-1 flex justify-between">
                                            <div class="w-1/3"><span class="font-bold">P(<?= $key + 1 ?>). </span><?= $data['name'] ?> | Price: Rs. <?= $price ?>/-</div>
                                            <div class="w-1/3 text-center"><?= $packageJ['qty'][$key] ?> PKG</div>
                                            <div class="w-1/3 text-end">
                                                <span class=""><?= $packageJ['qty'][$key] * $price ?>/-</span>
                                            </div>
                                        </li>
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
                                        <button name="confirm" type="submit" class="bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 rounded-md py-3 px-8 text-center text-base font-semibold text-white outline-none">Ready To Ship</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="bg-white shadow rounded-lg mb-4 p-4 sm:p-6 h-full">
                        <div class="flex justify-between w-full">
                            <h3 class="text-2xl font-bold leading-none text-cyan-600 mb-4">Orders Preparing</h3>
                            <!-- <div class="pt-2 relative text-gray-600 my-2">
                                <input class="border-2 border-gray-300 bg-white h-10 px-5 pr-16 rounded-lg text-sm focus:outline-none" type="search" name="search" placeholder="Search">
                                <button type="submit" class="absolute right-0 top-0 mt-5 mr-4">
                                    <svg class="text-gray-600 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 56.966 56.966" style="enable-background:new 0 0 56.966 56.966;" xml:space="preserve" width="512px" height="512px">
                                        <path d="M55.146,51.887L41.588,37.786c3.486-4.144,5.396-9.358,5.396-14.786c0-12.682-10.318-23-23-23s-23,10.318-23,23  s10.318,23,23,23c4.761,0,9.298-1.436,13.177-4.162l13.661,14.208c0.571,0.593,1.339,0.92,2.162,0.92  c0.779,0,1.518-0.297,2.079-0.837C56.255,54.982,56.293,53.08,55.146,51.887z M23.984,6c9.374,0,17,7.626,17,17s-7.626,17-17,17  s-17-7.626-17-17S14.61,6,23.984,6z" />
                                    </svg>
                                </button>
                            </div> -->
                        </div>
                        <?php
                        $result = $conn->query("SELECT * FROM `orders` WHERE `status`=2 ORDER BY `id` DESC");
                        $orders = $result->fetch_all(MYSQLI_ASSOC);
                        foreach ($orders as $key => $order) :
                        ?>
                            <a href="?order=<?= $order['id'] ?>">
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
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>
<?php require('components/_footer.php'); ?>