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
        $itemTotal = 0;
        $packageData = ['id' => [], 'nob' => [], 'pib' => []];
        while (isset($_POST["item-select-" . $itemNo])) {
            $itemId = $_POST["item-select-" . $itemNo];
            $packageData['id'][] = $_POST["item-select-" . $itemNo];
            $packageData['nob'][] = $_POST["nob-" . $itemNo];
            $packageData['pib'][] = $_POST["pib-" . $itemNo];
            $result = $conn->query("SELECT * FROM `item` WHERE `id`= '$itemId'");
            $items = $result->fetch_all(MYSQLI_ASSOC);
            $itemTotal += $items[0]['rate'] * ($_POST["nob-" . $itemNo] * $_POST["pib-" . $itemNo]) * $items[0]['weight-per-pcs'];
            $itemNo++;
        }
        $jsonP = json_encode($packageData);
        // print_r($jsonP);
        $pName = $_POST["pName"];
        $pRemark = $_POST["pRemark"];

        $stmt = $conn->prepare("INSERT INTO package (`name`, `package`, `total`, `remark`) VALUES ('$pName', ?, '$itemTotal', '$pRemark')");
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
                            <h3 class="text-xl font-bold leading-none text-gray-900">Create New Package</h3>
                        </div>
                        <div class="flex items-center justify-center p-12">
                            <div class="mx-auto w-full">
                                <form action="" method="POST" id="boxForm">
                                    <div class="flex flex-wrap">
                                        <div class="w-full px-3">
                                            <div class="mb-5">
                                                <label for="pName" class="mb-3 block text-base font-medium text-[#07074D]">
                                                    Package Name
                                                </label>
                                                <input type="text" name="pName" id="pName" placeholder="Package Name" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" required />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap" id="addItemBox">
                                        <div class="w-full px-3 sm:w-1/3">
                                            <div class="mb-5">
                                                <label for="item-select-1" class="mb-3 block text-base font-medium text-[#07074D]">
                                                    Select Item 1
                                                </label>
                                                <select required name="item-select-1" class="w-full appearance-none rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" id="item-select-1">
                                                    
                                                    <?php
                                                    $result = $conn->query("SELECT * FROM `item` WHERE `removed`= 0");
                                                    $items = $result->fetch_all(MYSQLI_ASSOC);
                                                    foreach ($items as $item) :
                                                    ?>
                                                        <option value="<?= $item['id'] ?>"><?= $item['name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="w-full px-3 sm:w-1/3">
                                            <div class="mb-5">
                                                <label for="nob-1" class="mb-3 block text-base font-medium text-[#07074D]">
                                                    Total No of Box
                                                </label>
                                                <input required value="1" type="number" name="nob-1" id="nob-1" placeholder="Enter Number of Boxes" min="0" class="w-full appearance-none rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                            </div>
                                        </div>
                                        <div class="w-full px-3 sm:w-1/3">
                                            <div class="mb-5">
                                                <label for="pib-1" class="mb-3 block text-base font-medium text-[#07074D]">
                                                    PCS in one Box
                                                </label>
                                                <input required type="number" step="any" name="pib-1" id="pib-1" placeholder="Enter PCS in one Box" min="0" class="w-full appearance-none rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-5">
                                        <div class="w-48 mb-2 cursor-pointer bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-200 rounded-md py-3 px-8 text-center text-base font-semibold text-white outline-none" id="removeItem" style="display: none;">Remove Item</div>
                                        <div class="w-48 mb-2 cursor-pointer bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 rounded-md py-3 px-8 text-center text-base font-semibold text-white outline-none" id="addItem">Add More Item</div>
                                    </div>
                                    <div class="-mx-3 flex flex-wrap">
                                        <div class="w-full px-3">
                                            <div class="mb-5">
                                                <label for="pRemark" class="mb-3 block text-base font-medium text-[#07074D]">
                                                    Remark
                                                </label>
                                                <input type="text" name="pRemark" id="pRemark" placeholder="Remark (if any)" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                            </div>
                                        </div>
                                    </div>
                                    <button name="create-package" class="w-full bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 rounded-md py-3 px-8 text-center text-base font-semibold text-white outline-none">
                                        Create Package
                                    </button>
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
            <div class="w-full px-3 sm:w-1/3">
                                            <div class="mb-5">
                                                <label for="item-select-${itemNo}" class="mb-3 block text-base font-medium text-[#07074D]">
                                                    Select Item ${itemNo}
                                                </label>
                                                <select required name="item-select-${itemNo}" class="w-full appearance-none rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" id="item-select-${itemNo}">
                                                    
                                                    <?php foreach ($items as $item) : ?>
                                                        <option value="<?= $item['id'] ?>"><?= $item['name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="w-full px-3 sm:w-1/3">
                                            <div class="mb-5">
                                                <label for="nob-${itemNo}" class="mb-3 block text-base font-medium text-[#07074D]">
                                                    Total No of Box
                                                </label>
                                                <input required value="1" type="number" name="nob-${itemNo}" id="nob-${itemNo}" placeholder="Enter Number of Boxes" min="0" class="w-full appearance-none rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                            </div>
                                        </div>
                                        <div class="w-full px-3 sm:w-1/3">
                                            <div class="mb-5">
                                                <label for="pib-${itemNo}" class="mb-3 block text-base font-medium text-[#07074D]">
                                                    PCS in one Box
                                                </label>
                                                <input required type="number" step="any" name="pib-${itemNo}" id="pib-${itemNo}" placeholder="Enter PCS in one Box" min="0" class="w-full appearance-none rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                            </div>
                                        </div>
            `);
        });
    });
</script>
<?php require('components/_footer.php'); ?>