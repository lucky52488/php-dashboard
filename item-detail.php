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
$title = "Add New Item Category";

require("components/_dataConnect.php");
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_GET["edit-item"]) && isset($_POST["add-item-btn"])) {
        $updateItemId = $_GET["edit-item"];
        $name = $_POST["item-name"];
        $uom = $_POST["uom"];
        $weightPerPcs = $uom == 'PCS' ? 1 : $_POST["weight"];
        $rate = $_POST["price"];
        $pcs = $_POST["pcs"];
        $cat = $_POST["item-cat"];
        $source = $_POST["source"];
        if ($name) {
            $sql = "UPDATE `item` SET `name`='$name',`uom`='$uom',`weight-per-pcs`='$weightPerPcs',`rate`='$rate',`pcs`='$pcs',`source`='$source',`cat`='$cat' WHERE `id`='$updateItemId'";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                $_SESSION['successMsg'] = "Item Updated Successfully";
                header("location: " . url() . 'item-detail.php');
                exit();
            }
        }
    }

    if (isset($_POST["del-btn"])) {
        $deleteItem = $_POST["delete"];
        if ($deleteItem) {
            $sql = "UPDATE `item` SET `removed`=1 WHERE `id`='$deleteItem'";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                $_SESSION['errorMsg'] = "Item Removed Successfully";
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
                <?php if (isset($_GET['edit-item']) && $_GET['edit-item']) :
                    $editId = $_GET['edit-item'];
                    $result = $conn->query("SELECT * FROM `item` WHERE `id`='$editId'");
                    $items = $result->fetch_all(MYSQLI_ASSOC);
                    $item = $items[0];
                ?>
                    <div class="grid grid-cols-1 2xl:grid-cols-1 xl:gap-4 my-4">
                        <div class="bg-white shadow rounded-lg mb-4 p-4 sm:p-6 h-full">
                            <a href="<?= url() . 'item-detail.php' ?>" class="text-cyan-600 hover:text-cyan-700">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-box-arrow-left" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0v2z" />
                                    <path fill-rule="evenodd" d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3z" />
                                </svg>
                            </a>
                            <div class="flex items-center justify-center mb-4">
                                <h3 class="text-xl font-bold leading-none text-gray-900">Update Item Detail</h3>
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
                                                    <input value="<?= $item['name'] ?>" type="text" name="item-name" id="item-name" placeholder="Enter New Item Name" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" required />
                                                </div>
                                            </div>
                                            <div class="w-full px-3 sm:w-1/2">
                                                <div class="mb-5">
                                                    <label for="uom" class="mb-3 block text-base font-medium text-[#07074D]">
                                                        Unit of measure
                                                    </label>
                                                    <select name="uom" class="w-full appearance-none rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" id="uom">
                                                        <option <?= $item['uom'] == 'KG' ? 'selected' : '' ?>>KG</option>
                                                        <option <?= $item['uom'] == 'PCS' ? 'selected' : '' ?>>PCS</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-5">
                                            <label for="weight" class="mb-3 block text-base font-medium text-[#07074D]">
                                                Weight Per PCS
                                            </label>
                                            <input value="<?= $item['weight-per-pcs'] ?>" type="number" step="any" name="weight" id="weight" placeholder="Enter Weight per Pcs" min="0" class="w-full appearance-none rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                        </div>
                                        <div class="mb-5">
                                            <label for="price" class="mb-3 block text-base font-medium text-[#07074D]">
                                                Price
                                            </label>
                                            <input value="<?= $item['rate'] ?>" type="number" step="any" name="price" id="price" placeholder="Rate (Price in INR)" min="0" class="w-full appearance-none rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                        </div>
                                        <div class="mb-5">
                                            <label for="pcs" class="mb-3 block text-base font-medium text-[#07074D]">
                                                PCS (Current Quantity)
                                            </label>
                                            <input value="<?= $item['pcs'] ?>" type="number" step="any" name="pcs" id="pcs" placeholder="Enter current available Pcs" min="0" class="w-full appearance-none rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
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
                                                            <option <?= $item['cat'] == $cat['cat'] ? 'selected' : '' ?>><?= $cat['cat'] ?></option>
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
                                                            <input type="radio" name="source" id="radioButton1" class="h-5 w-5" required <?= $item['source'] == 'INHOUSE' ? 'checked' : '' ?> value="INHOUSE" />
                                                            <label for="radioButton1" class="pl-3 text-base font-medium text-[#07074D]">
                                                                INHOUSE
                                                            </label>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <input type="radio" name="source" id="radioButton2" class="h-5 w-5" <?= $item['source'] == 'OUTSIDE' ? 'checked' : '' ?> value="OUTSIDE" />
                                                            <label for="radioButton2" class="pl-3 text-base font-medium text-[#07074D]">
                                                                OUTSIDE
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button name="add-item-btn" class="bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 rounded-md py-3 px-8 text-center text-base font-semibold text-white outline-none">
                                            Update Item
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="grid grid-cols-1 2xl:grid-cols-1 xl:gap-4 my-4">
                        <div class="bg-white shadow rounded-lg mb-4 p-4 sm:p-6 h-full">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-xl font-bold leading-none text-gray-900">All Items Detail</h3>
                            </div>
                            <div class="flow-root">
                                <ul role="list" class="divide-y divide-gray-200">
                                    <?php
                                    $result = $conn->query("SELECT * FROM `item` WHERE `removed` = 0");
                                    $items = $result->fetch_all(MYSQLI_ASSOC);
                                    foreach ($items as $key => $item) :
                                    ?>
                                        <li class="py-3 sm:py-4">
                                            <div class="flex items-center space-x-4">
                                                <div class="flex-shrink-0">
                                                    <img class="h-12 w-12 rounded-full" src="assets/100924860.jpg" alt="sweet">
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">
                                                        <?= $item['name'] ?>
                                                    </p>
                                                    <p class="text-sm text-gray-500 truncate">
                                                        <?= $item['cat'] ?>
                                                    </p>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">
                                                        <?= $item['source'] ?> | <?= $item['pcs'] ?> PCS Available
                                                    </p>
                                                    <p class="text-sm text-gray-500 truncate">
                                                        <?= $item['date'] ?>
                                                    </p>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">
                                                        Rs: <?= $item['rate'].'/'.$item['uom'] ?>
                                                    </p>
                                                    <p class="text-sm text-gray-500 truncate">
                                                        <?= $item['uom']=='PCS'?'':'(Weight/PCS='. $item['weight-per-pcs'].')' ?>
                                                    </p>
                                                </div>
                                                <div class="inline-flex items-center text-base font-semibold text-gray-900">
                                                    <form action="" method="get">
                                                        <input type="hidden" name="edit-item" value="<?= $item['id'] ?>">
                                                        <button class="text-gray-500 hover:text-gray-900" type="submit">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear-fill" viewBox="0 0 16 16">
                                                                <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                                <div class="inline-flex items-center text-base font-semibold text-gray-900">
                                                    <form action="" method="post" id="del-btn">
                                                        <input type="hidden" name="delete" value="<?= $item['id'] ?>">
                                                        <button name="del-btn" class="text-rose-900 hover:text-rose-400" type="submit" onclick="return confirm('Are You Sure?')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z" />
                                                                <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php require('components/_footer.php'); ?>