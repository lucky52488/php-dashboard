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
$title = "Add New Item";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["add-item-btn"])) {
        $name = $_POST["item-name"];
        $uom = $_POST["uom"];
        // $weightPerPcs = $uom == 'PCS' ? 1 : $_POST["weight"];
        $weightPerPcs = 1;
        $rate = $_POST["price"];
        // $pcs = $_POST["pcs"];
        $cat = $_POST["item-cat"];
        $source = $_POST["source"];
        $targetDir = "assets/item_pic/";
        if (!empty($_FILES["pic"]["name"])) {
            $fileName = rand() . basename($_FILES["pic"]["name"]);
            $targetFilePath = $targetDir . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            $allowTypes = array('jpg', 'png', 'jpeg', 'webp');
            if ($_FILES["pic"]["size"] < 500000) {
                if (in_array($fileType, $allowTypes)) {
                    if (move_uploaded_file($_FILES["pic"]["tmp_name"], $targetFilePath)) {
                        // $oldPic = $conn->query("SELECT `pic` FROM `users` WHERE `id`='$uId'");
                        // $deletePic = mysqli_fetch_assoc($oldPic);
                        // if (file_exists("assets/item_pic/" . $deletePic['pic'])) {
                        //     unlink("assets/item_pic/" . $deletePic['pic']);
                        // }
                        $sql = "INSERT INTO `item` (`name`, `uom`, `weight-per-pcs`, `rate`, `source`, `cat`, `img`) VALUES ('$name', '$uom', '$weightPerPcs', '$rate', '$source', '$cat', '$fileName')";
                        // $sql = "INSERT INTO `item` (`name`, `uom`, `weight-per-pcs`, `rate`, `pcs`, `source`, `cat`, `img`) VALUES ('$name', '$uom', '$weightPerPcs', '$rate', '$pcs', '$source', '$cat', '$fileName')";
                        $result = mysqli_query($conn, $sql);
                        if ($result) {
                            $_SESSION['successMsg'] = "Item added successfully.";
                            header("location: " . urlNow());
                            exit();
                        } else {
                            $_SESSION['errorMsg'] = "Picture Upload failed, please try again.";
                            header("location: " . urlNow());
                            exit();
                        }
                    } else {
                        $_SESSION['errorMsg'] = "Sorry, Unable to save picture, please try again.";
                        header("location: " . urlNow());
                        exit();
                    }
                } else {
                    $_SESSION['errorMsg'] = 'Sorry, only JPG, JPEG, PNG, WEBP Pictures are allowed to upload.';
                    header("location: " . urlNow());
                    exit();
                }
            } else {
                $_SESSION['errorMsg'] = 'Sorry, Picture size is too large max size is 200kb';
                header("location: " . urlNow());
                exit();
            }
        } elseif ($name) {
            $sql = "INSERT INTO `item` (`name`, `uom`, `weight-per-pcs`, `rate`, `pcs`, `source`, `cat`) VALUES ('$name', '$uom', '$weightPerPcs', '$rate', '$pcs', '$source', '$cat')";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                $_SESSION['successMsg'] = "New Item Added Successfully";
                header("location: " . urlNow());
                exit();
            } else {
                $_SESSION['errorMsg'] = 'Something went wrong, try again';
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
                <?php if (isset($_SESSION['errorMsg']) && $_SESSION['errorMsg']) {
                    require('components/_errorMsg.php');
                    $_SESSION['errorMsg'] = false;
                } ?>
                <div class="grid grid-cols-1 2xl:grid-cols-1 xl:gap-4 my-4">
                    <div class="bg-white shadow rounded-lg mb-4 p-4 sm:p-6 h-full">
                        <div class="flex items-center justify-center mt-4">
                            <h3 class="text-2xl font-bold leading-none text-cyan-600">ADD NEW ITEM</h3>
                        </div>
                        <div class="flex items-center justify-center p-12">
                            <div class="mx-auto w-full">
                                <form action="" method="POST" enctype="multipart/form-data">
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
                                        <div class="w-full px-3">
                                            <div class="w-full mb-5">
                                                <label for="pic" class="mb-3 block text-base font-medium text-[#07074D]">
                                                    Item Image
                                                </label>
                                                <input type="file" name="pic" id="pic" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />

                                            </div>
                                        </div>
                                        <!-- <div class="w-full px-3 sm:w-1/2">
                                            <div class="mb-5">
                                                <label for="weight" class="mb-3 block text-base font-medium text-[#07074D]">
                                                    Weight Per PCS
                                                </label>
                                                <input type="number" step="any" name="weight" id="weight" placeholder="Enter Weight per Pcs" min="0" class="w-full appearance-none rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                            </div>
                                        </div> -->

                                        <!-- <div class="w-full px-3 sm:w-1/2">
                                            <div class="mb-5">
                                                <label for="pcs" class="mb-3 block text-base font-medium text-[#07074D]">
                                                    PCS (Current Quantity)
                                                </label>
                                                <input type="number" step="any" name="pcs" id="pcs" placeholder="Enter current available Pcs" min="0" class="w-full appearance-none rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                            </div>
                                        </div> -->
                                    </div>
                                    <div class="-mx-3 flex flex-wrap">
                                        <div class="w-full px-3 sm:w-1/3">
                                            <div class="mb-5">
                                                <label for="price" class="mb-3 block text-base font-medium text-[#07074D]">
                                                    Rate
                                                </label>
                                                <input type="number" step="any" name="price" id="price" placeholder="Rate (Price in INR)" min="0" class="w-full appearance-none rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                            </div>
                                        </div>
                                        <div class="w-full px-3 sm:w-1/3">
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
                                        <div class="flex w-full px-3 sm:w-1/3">
                                            <div class="flex my-auto w-full">
                                                <div class="flex flex-wrap w-2/3 justify-between">
                                                    <label class="mb-3 w-full block text-base font-medium text-[#07074D]">
                                                        Item Source
                                                    </label>
                                                    <div class="flex items-center my-auto">
                                                        <input type="radio" name="source" id="radioButton1" class="h-5 w-5" required checked="checked" value="INHOUSE" />
                                                        <label for="radioButton1" class="pl-3 text-base font-medium text-[#07074D]">
                                                            INHOUSE
                                                        </label>
                                                    </div>
                                                    <div class="flex items-center my-auto">
                                                        <input type="radio" name="source" id="radioButton2" class="h-5 w-5" value="OUTSIDE" />
                                                        <label for="radioButton2" class="pl-3 text-base font-medium text-[#07074D]">
                                                            OUTSIDE
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="w-1/3 mt-auto text-end">
                                                    <button title="Done" name="add-item-btn" class="text-cyan-600 hover:text-cyan-700">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                                                        </svg>
                                                    </button>
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

<?php require('components/_footer.php'); ?>