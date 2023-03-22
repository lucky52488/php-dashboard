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

require("components/_dataConnect.php");
$title = "New Customer Order";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["create-order"])) {
        $itemNo = 1;
        $itemTotal = 0;
        $packageNo = 1;
        $packageTotal = 0;
        $itemData = ['id' => [], 'qty' => []];
        $packageData = ['id' => [], 'qty' => []];
        if (!isset($_POST["select-item-" . $itemNo]) && !isset($_POST["select-package-" . $packageNo])) {
            $_SESSION['errorMsg'] = "Please add a item or package to create order";
            header("location: " . urlNow());
            exit();
        }
        while (isset($_POST["select-item-" . $itemNo])) {
            if (!$_POST["select-item-" . $itemNo] || !$_POST["item-quantity-" . $itemNo]) {
                $_SESSION['errorMsg'] = "Error! Invalid item no.". $itemNo;
                header("location: " . urlNow());
                exit();
            }
            $itemId = $_POST["select-item-" . $itemNo];
            $itemData['id'][] = $itemId;
            $itemData['qty'][] = $_POST["item-quantity-" . $itemNo];
            $result = $conn->query("SELECT * FROM `item` WHERE `id`= '$itemId'");
            $items = $result->fetch_all(MYSQLI_ASSOC);
            $itemTotal += $items[0]['rate'] * $_POST["item-quantity-" . $itemNo];
            $itemNo++;
        }
        while (isset($_POST["select-package-" . $packageNo])) {
            if (!$_POST["select-package-" . $packageNo] || !$_POST["package-quantity-" . $packageNo]) {
                $_SESSION['errorMsg'] = "Error! Invalid package no.". $packageNo;
                header("location: " . urlNow());
                exit();
            }
            $packageId = $_POST["select-package-" . $packageNo];
            $packageData['id'][] = $_POST["select-package-" . $packageNo];
            $packageData['qty'][] = $_POST["package-quantity-" . $packageNo];
            $result = $conn->query("SELECT * FROM `package` WHERE `id`= '$packageId'");
            $packages = $result->fetch_all(MYSQLI_ASSOC);
            $packageTotal += $packages[0]['total'] * $_POST["package-quantity-" . $packageNo];
            $packageNo++;
        }
        $orderTotal = $itemTotal + $packageTotal;
        $jsonI = json_encode($itemData);
        $jsonP = json_encode($packageData);
        // print_r($jsonP);
        $cName = $_POST["customer-name"];
        $cMobile = $_POST["customer-mobile"];
        $aMobile = $_POST["alternate-no"];
        $cEmail = $_POST["customer-email"];
        $cAddress = $_POST["customer-address"];
        $gst = $_POST["gst"];
        $dDate = $_POST["delivery-date"];
        $rDate = $_POST["reminder-date"];
        $status = 1;
        // $stmt = $conn->prepare("INSERT INTO orders (`name`, `mobile`, `email`, `address`, `dod`, `reminder`, `status`, `items`, `packages`) VALUES ('$cName', '$cMobile', '$cEmail', '$cAddress', '$dDate', '$rDate', '$status', ?)");
        // $stmt->bind_param("s", $jsonI, $jsonP);
        $stmt = $conn->prepare("INSERT INTO orders (`name`, `mobile`, `email`, `address`, `dod`, `reminder`, `status`, `items`, `packages`, `total`, `net`, `balance`, `alternate-no`, `gst`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssissdddss", $cName, $cMobile, $cEmail, $cAddress, $dDate, $rDate, $status, $jsonI, $jsonP, $orderTotal, $orderTotal, $orderTotal, $aMobile, $gst);

        if ($stmt->execute()) {
            $_SESSION['successMsg'] = "Customer Invoice Created Successfully";
            header("location: " . url().'customers-detail.php');
            exit();
        } else {
            $_SESSION['errorMsg'] = "Something went wrong";
            header("location: " . urlNow());
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
                <div class="grid grid-cols-1 2xl:grid-cols-1 xl:gap-4 my-4">
                    <div class="bg-white shadow rounded-lg mb-4 p-4 sm:p-6 h-full">
                        <div class="flex items-center justify-center mt-4">
                            <h3 class="text-2xl font-bold leading-none text-gray-900">Create Customer Order</h3>
                        </div>
                        <div class="flex items-center justify-center p-12">
                            <div class="mx-auto w-full">
                                <form action="" method="POST" id="boxForm">
                                    <div class="flex items-center my-4">
                                        <h3 class="text-xl font-bold leading-none text-cyan-600">Customer Detail</h3>
                                    </div>
                                    <div class="flex flex-wrap w-full">
                                        <div class="sm:w-1/3 w-full p-2">
                                            <label for="customer-name" class="mb-3 text-base font-medium text-[#07074D]">Customer Name</label>
                                            <input required class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" type="text" placeholder="Enter Customer Name" name="customer-name" id="customer-name">
                                        </div>
                                        <div class="sm:w-1/3 w-full p-2">
                                            <label for="customer-mobile" class="mb-3 text-base font-medium text-[#07074D]">Mobile Number</label>
                                            <input required class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" type="text" placeholder="Enter Mobile No." name="customer-mobile" id="customer-mobile">
                                        </div>
                                        <div class="sm:w-1/3 w-full p-2">
                                            <label for="customer-email" class="mb-3 text-base font-medium text-[#07074D]">Email</label>
                                            <input class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" type="email" placeholder="xyz@domain.com" name="customer-email" id="customer-email">
                                        </div>
                                        <div class="w-full p-2">
                                            <label for="customer-address" class="mb-3 text-base font-medium text-[#07074D]">Delivery Address</label>
                                            <input class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" type="text" placeholder="Enter Delivery Address" name="customer-address" id="customer-address">
                                        </div>
                                        <div class="sm:w-1/2 w-full p-2">
                                            <label for="alternate-no" class="mb-3 text-base font-medium text-[#07074D]">Alternate Contact Number</label>
                                            <input class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" type="text" placeholder="Enter Alternate Number" name="alternate-no" id="alternate-no">
                                        </div>
                                        <div class="sm:w-1/2 w-full p-2">
                                            <label for="gst" class="mb-3 text-base font-medium text-[#07074D]">GST No.</label>
                                            <input class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" type="text" placeholder="Enter GST No (If Any)" name="gst" id="gst">
                                        </div>
                                        <div class="sm:w-2/3 w-full p-2">
                                            <label for="delivery-date" class="mb-3 text-base font-medium text-[#07074D]">Delivery Date & Time</label>
                                            <input class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" type="datetime-local" placeholder="Enter Mobile No." name="delivery-date" id="delivery-date">
                                        </div>
                                        <div class="sm:w-1/3 w-full p-2">
                                            <label for="reminder-date" class="mb-3 text-base font-medium text-[#07074D]">Reminder Date</label>
                                            <input class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" type="date" placeholder="Enter Mobile No." name="reminder-date" id="reminder-date">
                                        </div>
                                    </div>
                                    <hr class="my-3 border-2">
                                    <div class="flex items-center my-4 justify-between">
                                        <h3 class="text-xl font-bold leading-none text-cyan-600">Items</h3>
                                    </div>
                                    <div id="item-div">
                                        <!-- Item Add dynamically -->
                                    </div>
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <div id="remove-item" class="w-40 cursor-pointer bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-cyan-200 rounded-md py-3 px-8 text-center text-base font-semibold text-white outline-none" style="display: none;">Remove Last</div>
                                        <div id="add-item" class="w-40 cursor-pointer bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 rounded-md py-3 px-8 text-center text-base font-semibold text-white outline-none">Add Item</div>
                                    </div>
                                    <hr class="my-3 border-2">
                                    <div class="flex items-center my-4">
                                        <h3 class="text-xl font-bold leading-none text-cyan-600">Packages</h3>
                                    </div>
                                    <div id="package-div">

                                    </div>
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <div id="remove-package" class="w-40 cursor-pointer bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-cyan-200 rounded-md py-3 px-8 text-center text-base font-semibold text-white outline-none" style="display: none;">Remove Last</div>
                                        <div id="add-package" class="w-40 cursor-pointer bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 rounded-md py-3 px-8 text-center text-base font-semibold text-white outline-none">Add Package</div>
                                    </div>
                                    <!-- <hr class="my-3 border-2">
                                    <div class="items-center my-4">
                                        <div class="flex justify-between">
                                            <h3 class="text-xl font-bold leading-none text-cyan-600 my-1">Total</h3>
                                            <span class="text-lg">12345/-</span>
                                        </div>
                                        <hr>
                                        <div class="flex justify-between">
                                            <h3 class="text-xl font-bold leading-none text-cyan-600 my-1">Discount</h3>
                                            <input class="text-end" type="number" step="any" placeholder="Enter amount">
                                        </div>
                                        <hr>
                                        <div class="flex justify-between">
                                            <h3 class="text-xl font-bold leading-none text-cyan-600 my-1">Net Amount</h3>
                                            <span class="text-lg">12345/-</span>
                                        </div>
                                        <hr>
                                        <div class="flex justify-between">
                                            <h3 class="text-xl font-bold leading-none text-cyan-600 my-1">Advance Received</h3>
                                            <input class="text-end" type="number" step="any" placeholder="Enter amount">
                                        </div>
                                        <hr>
                                        <div class="flex justify-between">
                                            <h3 class="text-xl font-bold leading-none text-cyan-600 my-1">Net Amount</h3>
                                            <span class="text-lg">12345/-</span>
                                        </div>
                                    </div> -->
                                    <div class="flex justify-center my-12">
                                        <button name="create-order" class="bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 rounded-md py-3 px-8 text-center text-base font-semibold text-white outline-none">
                                            Create Customer Order
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>
<script>
    $(document).ready(function() {
        let itemNo = 0;
        let packageNo = 0;
        // $("#remove-item").hide()
        // $("#remove-package").hide()
        $("#remove-item").click(function() {
            $("#item-div").children("div").slice(-1).remove();
            itemNo--;
            if (itemNo == 0) {
                $("#remove-item").hide()
            }
        })
        $("#remove-package").click(function() {
            $("#package-div").children("div").slice(-1).remove();
            packageNo--;
            if (packageNo == 0) {
                $("#remove-package").hide()
            }
        })
        $("#add-item").click(function() {
            itemNo++;
            $("#remove-item").show()
            $("#item-div").append(`<div class="flex w-full justify-between">
                                            <div class="flex flex-wrap w-full">
                                                <div class="xl:w-3/4 sm:w-2/3 w-full p-2">
                                                    <label for="select-item-${itemNo}" class="mb-3 text-base font-medium text-[#07074D]">Select Item ${itemNo}</label>
                                                    <select required class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" type="text" placeholder="Enter Mobile No." name="select-item-${itemNo}" id="select-item-${itemNo}">
                                                        <option value="0">Select</option>
                                                        <?php
                                                        $result = $conn->query("SELECT * FROM `item` WHERE `removed`= 0");
                                                        $items = $result->fetch_all(MYSQLI_ASSOC);
                                                        foreach ($items as $item) :
                                                        ?>
                                                            <option value="<?= $item['id'] ?>"><?= $item['name'] ?> | Rate: <?= $item['rate'] . '/' . $item['uom'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="xl:w-1/4 sm:w-1/3 w-full p-2">
                                                    <label for="item-quantity-${itemNo}" class="mb-3 text-base font-medium text-[#07074D]">Quantity</label>
                                                    <input required class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" type="number" step="any" placeholder="Enter Item Quantity" name="item-quantity-${itemNo}" id="item-quantity-${itemNo}">
                                                </div>
                                            </div>
                                            </div>`);
        });
        $("#add-package").click(function() {
            packageNo++;
            $("#remove-package").show()
            $("#package-div").append(`<div class="flex w-full justify-between">
                                            <div class="flex flex-wrap w-full">
                                                <div class="xl:w-3/4 sm:w-2/3 w-full p-2">
                                                    <label for="select-package-${packageNo}" class="mb-3 text-base font-medium text-[#07074D]">Select Package ${packageNo}</label>
                                                    <select required class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" type="text" placeholder="Enter Mobile No." name="select-package-${packageNo}" id="select-package-${packageNo}">
                                                        <option value="0">Select</option>
                                                        <?php
                                                        $result = $conn->query("SELECT * FROM `package`");
                                                        $packages = $result->fetch_all(MYSQLI_ASSOC);
                                                        foreach ($packages as $package) :
                                                        ?>
                                                            <option value="<?= $package['id'] ?>"><?= $package['name'] ?> | Price: <?= $package['total'] ?>/-</option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="xl:w-1/4 sm:w-1/3 w-full p-2">
                                                    <label for="package-quantity-${packageNo}" class="mb-3 text-base font-medium text-[#07074D]">Quantity</label>
                                                    <input required class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" type="number" step="any" placeholder="Enter Item Quantity" name="package-quantity-${packageNo}" id="package-quantity-${packageNo}">
                                                </div>
                                            </div>
                                            </div>`);
        });
    });
</script>
<?php require('components/_footer.php'); ?>