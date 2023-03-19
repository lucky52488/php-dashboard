<?php
session_start();
require('components/_siteUrl.php');

if (!isset($_SESSION['loggedIn']) or !$_SESSION['loggedIn']) {
    header("location: " . url() . "login.php");
    exit();
}
if($_SESSION['userRole']>2){
    header("location: " . url());
    exit();
}

require("components/_dataConnect.php");
$title = "Add New Item";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["add-item-btn"])) {
        $name = $_POST["item-name"];
        $uom = $_POST["uom"];
        $weightPerPcs = $uom == 'PCS' ? 1 : $_POST["weight"];
        $rate = $_POST["price"];
        $pcs = $_POST["pcs"];
        $cat = $_POST["item-cat"];
        $source = $_POST["source"];
        if ($name) {
            $sql = "INSERT INTO `item` (`name`, `uom`, `weight-per-pcs`, `rate`, `pcs`, `source`, `cat`) VALUES ('$name', '$uom', '$weightPerPcs', '$rate', '$pcs', '$source', '$cat')";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                $_SESSION['successMsg'] = "New Item Added Successfully";
                header("location: " . urlNow());
                exit();
            }
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
                <div class="grid grid-cols-1 2xl:grid-cols-1 xl:gap-4 my-4">
                    <div class="bg-white shadow rounded-lg mb-4 p-4 sm:p-6 h-full">
                        <div class="flex items-center justify-center mt-4">
                            <h3 class="text-xl font-bold leading-none text-gray-900">ADD NEW ITEM</h3>
                        </div>
                        <div class="flex items-center justify-center p-12">
                            <div class="mx-auto w-full max-w-[550px]">
                                <form action="" method="POST">
                                    <div class="-mx-3 flex flex-wrap">
                                        <div class="w-full px-3 sm:w-1/2">
                                            <div class="mb-5">
                                                <label for="item-name" class="mb-3 block text-base font-medium text-[#07074D]">
                                                    Item Name
                                                </label>
                                                <input type="text" name="item-name" id="item-name" placeholder="Enter New Item Name" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" required />
                                            </div>
                                        </div>
                                        <div class="w-full px-3 sm:w-1/2">
                                            <div class="mb-5">
                                                <label for="uom" class="mb-3 block text-base font-medium text-[#07074D]">
                                                    Unit of measure
                                                </label>
                                                <select name="uom" class="w-full appearance-none rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" id="uom">

                                                    <option>KG</option>
                                                    <option>PCS</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-5">
                                        <label for="weight" class="mb-3 block text-base font-medium text-[#07074D]">
                                            Weight Per PCS
                                        </label>
                                        <input type="number" step="any" name="weight" id="weight" placeholder="Enter Weight per Pcs" min="0" class="w-full appearance-none rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                    </div>
                                    <div class="mb-5">
                                        <label for="price" class="mb-3 block text-base font-medium text-[#07074D]">
                                            Price
                                        </label>
                                        <input type="number" step="any" name="price" id="price" placeholder="Rate (Price in INR)" min="0" class="w-full appearance-none rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                    </div>
                                    <div class="mb-5">
                                        <label for="pcs" class="mb-3 block text-base font-medium text-[#07074D]">
                                            PCS (Current Quantity)
                                        </label>
                                        <input type="number" step="any" name="pcs" id="pcs" placeholder="Enter current available Pcs" min="0" class="w-full appearance-none rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                    </div>
                                    <div class="-mx-3 flex flex-wrap">
                                        <div class="w-full px-3">
                                            <div class="mb-5">
                                                <label for="item-cat" class="mb-3 block text-base font-medium text-[#07074D]">
                                                    Item Category
                                                </label>
                                                <select name="item-cat" class="w-full appearance-none rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" id="item-cat">
                                                    <option value="0">Select</option>
                                                    <?php
                                                    $result = $conn->query("SELECT * FROM `item-category` WHERE `removed`=0");
                                                    $itemCats = $result->fetch_all(MYSQLI_ASSOC);
                                                    foreach ($itemCats as $cat) :
                                                    ?>
                                                        <option><?= $cat['cat'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="w-full px-3 sm:w-1/2">
                                            <div class="mb-5">
                                                <label class="mb-3 block text-base font-medium text-[#07074D]">
                                                    Item Source
                                                </label>
                                                <div class="flex items-center space-x-6">
                                                    <div class="flex items-center">
                                                        <input type="radio" name="source" id="radioButton1" class="h-5 w-5" required checked="checked" value="INHOUSE" />
                                                        <label for="radioButton1" class="pl-3 text-base font-medium text-[#07074D]">
                                                            INHOUSE
                                                        </label>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <input type="radio" name="source" id="radioButton2" class="h-5 w-5" value="OUTSIDE" />
                                                        <label for="radioButton2" class="pl-3 text-base font-medium text-[#07074D]">
                                                            OUTSIDE
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button name="add-item-btn" class="bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 rounded-md py-3 px-8 text-center text-base font-semibold text-white outline-none">
                                        Add Item
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

<?php require('components/_footer.php'); ?>