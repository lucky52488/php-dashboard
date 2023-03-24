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
                header("location: " . urlNow());
                exit();
            }
        }
    }
    if (isset($_POST["del-btn"])) {
        $deleteCat = $_POST["delete"];
        if ($deleteCat) {
            $sql = "UPDATE `item-category` SET `removed` = 1 WHERE `id`='$deleteCat'";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                $_SESSION['errorMsg'] = "Category Deleted Successfully";
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
                        <div class="flex items-center justify-center p-12">
                            <div class="mx-auto w-full max-w-[640px]">
                                <form action="" method="POST">
                                    <div class="-mx-3 flex flex-wrap">
                                        <div class="px-3 w-full">
                                            <div class="mb-5">
                                                <label for="cat-name" class="mb-3 block text-base font-medium text-[#07074D]">
                                                    Add Item Category
                                                </label>
                                                <div class="flex gap-2">
                                                    <input required type="text" name="cat-name" id="cat-name" placeholder="Enter New Item Category" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                    <button name="add-cat-button" class="text-cyan-600 hover:text-cyan-700">
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
                                    $result = $conn->query("SELECT * FROM `item-category` WHERE `removed` = 0");
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
                                                    <button name="del-btn" class="text-rose-900 hover:text-rose-400" type="submit" onclick="return confirm('Are You Sure?')">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z" />
                                                            <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z" />
                                                        </svg>
                                                    </button>
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