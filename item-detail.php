<?php
session_start();
require('components/_siteUrl.php');
if (!isset($_SESSION['loggedIn']) or !$_SESSION['loggedIn']) {
    header("location: " . url() . "login.php");
    exit();
}
$title = "Add New Item Category";

require("components/_dataConnect.php");
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["add-cat-button"])) {
        $newCat = $_POST["cat-name"];
        if ($newCat) {
            $sql = "INSERT INTO `item-category` (`cat`) VALUES ('$newCat')";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                $_SESSION['successMsg'] = "New Category Added Successfully";
                header("location: " . url() . "add-new-item-cat.php");
                exit();
            }
        }
    }
    if (isset($_POST["del-btn"])) {
        $deleteCat = $_POST["delete"];
        if ($deleteCat) {
            $sql = "DELETE FROM `item-category` WHERE `id`='$deleteCat'";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                $_SESSION['ErrorMsg'] = "Category Deleted Successfully";
                header("location: " . url() . "add-new-item-cat.php");
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
                <?php if (isset($_SESSION['ErrorMsg']) && $_SESSION['ErrorMsg']) {
                    require('components/_errorMsg.php');
                    $_SESSION['ErrorMsg'] = false;
                } ?>
                <div class="grid grid-cols-1 2xl:grid-cols-1 xl:gap-4 my-4">
                    <div class="bg-white shadow rounded-lg mb-4 p-4 sm:p-6 h-full">
                        <div class="flex items-center justify-center p-12">
                            <div class="mx-auto w-full max-w-[550px]">
                                <form action="" method="POST">
                                    <div class="-mx-3 flex flex-wrap">
                                        <div class="w-full px-3">
                                            <div class="mb-5">
                                                <label for="cat-name" class="mb-3 block text-base font-medium text-[#07074D]">
                                                    Add Item Category
                                                </label>
                                                <input type="text" name="cat-name" id="cat-name" placeholder="Enter New Item Category" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                            </div>
                                        </div>
                                    </div>
                                    <button name="add-cat-button" class="bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 rounded-md py-3 px-8 text-center text-base font-semibold text-white outline-none">
                                        Add Category
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8 ">
                        <h3 class="text-xl leading-none font-bold text-gray-900 mb-10">Item Categories</h3>
                        <div class="block w-full overflow-x-auto">
                            <table class="items-center w-full bg-transparent border-collapse">
                                <thead>
                                    <tr>
                                        <th class="px-4 bg-gray-50 text-gray-700 align-middle py-3 text-xs font-semibold text-left uppercase border-l-0 border-r-0 whitespace-nowrap">#</th>
                                        <th class="px-4 bg-gray-50 text-gray-700 align-middle py-3 text-xs font-semibold text-left uppercase border-l-0 border-r-0 whitespace-nowrap">Category Name</th>
                                        <th class="px-4 bg-gray-50 text-gray-700 align-middle py-3 text-xs font-semibold text-left uppercase border-l-0 border-r-0 whitespace-nowrap">Added On</th>
                                        <th class="px-4 bg-gray-50 text-gray-700 align-middle py-3 text-xs font-semibold text-left uppercase border-l-0 border-r-0 whitespace-nowrap">Delete</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php
                                    $result = $conn->query("SELECT * FROM `item-category`");
                                    $itemCats = $result->fetch_all(MYSQLI_ASSOC);
                                    foreach ($itemCats as $key => $cat) :
                                    ?>
                                        <tr class="text-gray-500">
                                            <th class="border-t-0 px-4 align-middle text-sm font-normal whitespace-nowrap p-4 text-left"><?= $key + 1 ?></th>
                                            <td class="border-t-0 px-4 align-middle text-xs font-medium text-gray-900 whitespace-nowrap p-4"><?= $cat['cat'] ?></td>
                                            <td class="border-t-0 px-4 align-middle text-xs font-medium text-gray-900 whitespace-nowrap p-4"><?= $cat['date'] ?></td>
                                            <td class="border-t-0 px-4 align-middle text-xs font-medium text-gray-900 whitespace-nowrap p-4">
                                                <form action="" method="post">
                                                    <input type="hidden" name="delete" value="<?= $cat['id'] ?>">
                                                    <button name="del-btn" class="text-rose-900 hover:text-rose-400" type="submit"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z" />
                                                            <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z" />
                                                        </svg></button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>

<?php require('components/_footer.php'); ?>