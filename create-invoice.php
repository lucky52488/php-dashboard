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
$title = "Create New Box";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["create-package"])) {
        $itemNo = 1;
        $packageData = ['id' => [], 'nob' => [], 'pib' => []];
        while (isset($_POST["item-select-" . $itemNo])) {
            $packageData['id'][] = $_POST["item-select-" . $itemNo];
            $packageData['nob'][] = $_POST["nob-" . $itemNo];
            $packageData['pib'][] = $_POST["pib-" . $itemNo];
            $itemNo++;
        }
        $jsonP = json_encode($packageData);
        // print_r($jsonP);
        $pName = $_POST["pName"];
        $pRemark = $_POST["pRemark"];

        $stmt = $conn->prepare("INSERT INTO package (`name`, `package`, `remark`) VALUES ('$pName', ?, '$pRemark')");
        $stmt->bind_param("s", $jsonP);
        if ($stmt->execute()) {
            $_SESSION['successMsg'] = "New Package Created Successfully";
            header("location: " . urlNow());
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
                                            <input class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" type="text" placeholder="Enter Customer Name" name="customer-name" id="customer-name">
                                        </div>
                                        <div class="sm:w-1/3 w-full p-2">
                                            <label for="customer-name" class="mb-3 text-base font-medium text-[#07074D]">Mobile Number</label>
                                            <input class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" type="text" placeholder="Enter Mobile No." name="customer-name" id="customer-name">
                                        </div>
                                        <div class="sm:w-1/3 w-full p-2">
                                            <label for="customer-name" class="mb-3 text-base font-medium text-[#07074D]">Email</label>
                                            <input class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" type="email" placeholder="xyz@domain.com" name="customer-name" id="customer-name">
                                        </div>
                                        <div class="w-full p-2">
                                            <label for="customer-name" class="mb-3 text-base font-medium text-[#07074D]">Delivery Address</label>
                                            <input class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" type="text" placeholder="Enter Delivery Address" name="customer-name" id="customer-name">
                                        </div>
                                        <div class="sm:w-2/3 w-full p-2">
                                            <label for="customer-name" class="mb-3 text-base font-medium text-[#07074D]">Delivery Date & Time</label>
                                            <input class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" type="datetime-local" placeholder="Enter Mobile No." name="customer-name" id="customer-name">
                                        </div>
                                        <div class="sm:w-1/3 w-full p-2">
                                            <label for="customer-name" class="mb-3 text-base font-medium text-[#07074D]">Reminder Date</label>
                                            <input class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" type="date" placeholder="Enter Mobile No." name="customer-name" id="customer-name">
                                        </div>
                                    </div>
                                    <hr class="my-3">
                                    <div class="flex items-center my-4 justify-between">
                                        <h3 class="text-xl font-bold leading-none text-cyan-600">Items</h3>
                                        <div id="add-item" class="cursor-pointer bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 rounded-md py-3 px-8 text-center text-base font-semibold text-white outline-none">Add Item</div>
                                    </div>
                                    <div id="item-div">
                                        <div class="flex w-full justify-between">
                                            <div class="flex w-11/12">
                                                <div class="xl:w-2/4 w-1/2 p-2">
                                                    <label for="customer-name" class="mb-3 text-base font-medium text-[#07074D]">Select Item</label>
                                                    <input class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" type="text" placeholder="Enter Mobile No." name="customer-name" id="customer-name">
                                                </div>
                                                <div class="xl:w-1/4 w-1/2 p-2">
                                                    <label for="customer-name" class="mb-3 text-base font-medium text-[#07074D]">Quantity</label>
                                                    <input class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" type="text" placeholder="Enter Mobile No." name="customer-name" id="customer-name">
                                                </div>
                                                <div class="xl:w-1/4 w-1/2 p-2">
                                                    <label for="customer-name" class="mb-3 text-base font-medium text-[#07074D]">Unit</label>
                                                    <input class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" type="text" placeholder="Enter Mobile No." name="customer-name" id="customer-name">
                                                </div>
                                            </div>
                                            <div class="flex w-1/12 m-auto justify-center">
                                                <div name="del-btn" class="cursor-pointer text-rose-900 hover:text-rose-400" type="submit" onclick="return confirm('Are You Sure?')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z" />
                                                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex w-full justify-between">
                                            <div class="flex w-11/12">
                                                <div class="xl:w-2/4 w-1/2 p-2">
                                                    <label for="customer-name" class="mb-3 text-base font-medium text-[#07074D]">Select Item</label>
                                                    <input class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" type="text" placeholder="Enter Mobile No." name="customer-name" id="customer-name">
                                                </div>
                                                <div class="xl:w-1/4 w-1/2 p-2">
                                                    <label for="customer-name" class="mb-3 text-base font-medium text-[#07074D]">Quantity</label>
                                                    <input class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" type="text" placeholder="Enter Mobile No." name="customer-name" id="customer-name">
                                                </div>
                                                <div class="xl:w-1/4 w-1/2 p-2">
                                                    <label for="customer-name" class="mb-3 text-base font-medium text-[#07074D]">Unit</label>
                                                    <input class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" type="text" placeholder="Enter Mobile No." name="customer-name" id="customer-name">
                                                </div>
                                            </div>
                                            <div class="flex w-1/12 m-auto justify-center">
                                                <div name="del-btn" class="cursor-pointer text-rose-900 hover:text-rose-400" type="submit" onclick="return confirm('Are You Sure?')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z" />
                                                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="my-3">
                                    <div class="flex items-center my-4 justify-between">
                                        <h3 class="text-xl font-bold leading-none text-cyan-600">Packages</h3>
                                        <div id="add-package" class="cursor-pointer bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 rounded-md py-3 px-8 text-center text-base font-semibold text-white outline-none">Add Package</div>
                                    </div>
                                    <div id="package-div">
                                        <div class="flex w-full justify-between">
                                            <div class="flex w-11/12">
                                                <div class="xl:w-2/4 w-1/2 p-2">
                                                    <label for="customer-name" class="mb-3 text-base font-medium text-[#07074D]">Select Item</label>
                                                    <input class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" type="text" placeholder="Enter Mobile No." name="customer-name" id="customer-name">
                                                </div>
                                                <div class="xl:w-1/4 w-1/2 p-2">
                                                    <label for="customer-name" class="mb-3 text-base font-medium text-[#07074D]">Quantity</label>
                                                    <input class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" type="text" placeholder="Enter Mobile No." name="customer-name" id="customer-name">
                                                </div>
                                                <div class="xl:w-1/4 w-1/2 p-2">
                                                    <label for="customer-name" class="mb-3 text-base font-medium text-[#07074D]">Unit</label>
                                                    <input class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" type="text" placeholder="Enter Mobile No." name="customer-name" id="customer-name">
                                                </div>
                                            </div>
                                            <div class="flex w-1/12 m-auto justify-center">
                                                <div name="del-btn" class="cursor-pointer text-rose-900 hover:text-rose-400" type="submit" onclick="return confirm('Are You Sure?')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z" />
                                                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
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
        let itemNo = 1;
        $("#removeItem").hide()
        $("#removeItem").click(function() {
            $("#addItemBox").children("div").slice(-4).remove();
            itemNo--;
            if (itemNo == 1) {
                $("#removeItem").hide()
            }
        })
        $("#addItem").click(function() {
            itemNo++;
            $("#removeItem").show()
            $("#addItemBox").append(`
            <div class="border-t-2 my-2 w-full"></div>
            <div class="w-full px-3">
                                            <div class="mb-5">
                                                <label for="item-select-${itemNo}" class="mb-3 block text-base font-medium text-[#07074D]">
                                                    Select Item ${itemNo}
                                                </label>
                                                <select name="item-select-${itemNo}" class="w-full appearance-none rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" id="item-select-${itemNo}">
                                                    <option value="0">Select</option>
                                                    <?php foreach ($items as $item) : ?>
                                                        <option value="<?= $item['id'] ?>"><?= $item['name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="w-full px-3 sm:w-1/2">
                                            <div class="mb-5">
                                                <label for="nob-${itemNo}" class="mb-3 block text-base font-medium text-[#07074D]">
                                                    Total No of Box
                                                </label>
                                                <input type="number" name="nob-${itemNo}" id="nob-${itemNo}" placeholder="Enter Weight per Pcs" min="0" class="w-full appearance-none rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                            </div>
                                        </div>
                                        <div class="w-full px-3 sm:w-1/2">
                                            <div class="mb-5">
                                                <label for="pib-${itemNo}" class="mb-3 block text-base font-medium text-[#07074D]">
                                                    PCS in one Box
                                                </label>
                                                <input type="number" step="any" name="pib-${itemNo}" id="pib-${itemNo}" placeholder="Enter Weight per Pcs" min="0" class="w-full appearance-none rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                            </div>
                                        </div>
            `);
        });
    });
</script>
<?php require('components/_footer.php'); ?>