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
$title = "Packages";

require("components/_dataConnect.php");
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["del-btn"])) {
        $deleteItem = $_POST["delete"];
        if ($deleteItem) {
            $sql = "DELETE FROM `package` WHERE `id`='$deleteItem'";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                $_SESSION['errorMsg'] = "Package Removed Successfully";
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
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-3xl font-bold leading-none text-gray-900">Packages</h3>
                        </div>
                        <div class="flow-root">
                            <div class="w-full grid grid-cols-1 xl:grid-cols-2 2xl:grid-cols-3 gap-4">
                                <?php
                                $packageQuery = $conn->query("SELECT * FROM `package`");
                                $packages = $packageQuery->fetch_all(MYSQLI_ASSOC);
                                foreach ($packages as $key => $package) :
                                    $packageJ = json_decode($package['package'], true); ?>
                                    <div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8 ">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-1 min-w-0">
                                                <h3 class="mb-3 text-2xl font-bold text-cyan-600 truncate">
                                                    <?= $package['name'] ?>
                                                </h3>
                                                <p class="text-lg font-medium text-gray-900 truncate">
                                                    Items List
                                                </p>
                                                <?php
                                                // print_r($packageJ);
                                                // print_r($data['id']);
                                                // print_r($data['name']);
                                                // print_r($packageJ['nob'][$key]);
                                                $total=0;
                                                foreach ($packageJ['id'] as $key => $itemId) {
                                                    $itemQuery = $conn->query("SELECT * FROM `item` WHERE `id`='$itemId'");
                                                    $itemData = $itemQuery->fetch_all(MYSQLI_ASSOC);
                                                    $data = $itemData[0];  
                                                    $price = ($packageJ['nob'][$key]*$packageJ['pib'][$key])*$data['weight-per-pcs']*$data['rate'];
                                                    $total = $total+$price;
                                                    ?>

                                                    <p class="my-2 text-sm text-gray-500">
                                                        <?= $key + 1 ?>. <?= $data['name'] ?> | No. of Box: <?= $packageJ['nob'][$key] ?> | PCS in 1 Box: <?= $packageJ['pib'][$key] ?> | Price: Rs. <?= $price ?>/-
                                                    </p>

                                                <?php } ?>
                                                <div>
                                                    Total Amount = Rs. <?= $total ?>/-
                                                </div>
                                            </div>
                                            <form action="" method="post" id="del-btn">
                                                <input type="hidden" name="delete" value="<?= $package['id'] ?>">
                                                <button name="del-btn" class="text-rose-900 hover:text-rose-400" type="submit" onclick="return confirm('Are You Sure?')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z" />
                                                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require('components/_footer.php'); ?>