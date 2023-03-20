<?php
session_start();
require('components/_siteUrl.php');
$title = "Paradise Dashboard";
$uId = $_SESSION['userId'];

if (!isset($_SESSION['loggedIn']) or !$_SESSION['loggedIn']) {
    header("location: " . url() . "login.php");
    exit();
}
require("components/_dataConnect.php");
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["update-password"])) {
        $cPass = $_POST['cPass'];
        $nPass = $_POST['nPass'];
        $nPass2 = $_POST['nPass2'];
        $result = $conn->query("SELECT * FROM `users` WHERE `id`='$uId'");
        $cUser = $result->fetch_all(MYSQLI_ASSOC);
        if ($cUser && $cUser[0]['password'] == $cPass) {
            if ($nPass && $nPass == $nPass2) {
                $sql = "UPDATE `users` SET `password`='$nPass' WHERE `id`='$uId'";
                $result = mysqli_query($conn, $sql);
                if ($result) {
                    $_SESSION['successMsg'] = "Password Updated Successfully";
                    header("location: " . urlNow());
                    exit();
                } else {
                    $_SESSION['errorMsg'] = "Something Went wrong, try again";
                    header("location: " . urlNow());
                    exit();
                }
            } else {
                $_SESSION['errorMsg'] = "Password do not match, try again";
                header("location: " . urlNow());
                exit();
            }
        } else {
            $_SESSION['errorMsg'] = "Incorrect current password, try again";
            header("location: " . urlNow());
            exit();
        }
    }
    if (isset($_POST["update-profile"])) {
        $email = $_POST['email'];
        $mobile = $_POST['mobile'];
        $targetDir = "assets/profile_pic/";
        if (!empty($_FILES["pic"]["name"])) {
            $fileName = rand() . basename($_FILES["pic"]["name"]);
            $targetFilePath = $targetDir . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            $allowTypes = array('jpg', 'png', 'jpeg', 'webp');
            if ($_FILES["pic"]["size"] < 200000) {
                if (in_array($fileType, $allowTypes)) {
                    if (move_uploaded_file($_FILES["pic"]["tmp_name"], $targetFilePath)) {
                        $oldPic = $conn->query("SELECT `pic` FROM `users` WHERE `id`='$uId'");
                        $deletePic = mysqli_fetch_assoc($oldPic);
                        if (file_exists("assets/profile_pic/" . $deletePic['pic'])) {
                            unlink("assets/profile_pic/" . $deletePic['pic']);
                        }
                        $insert = $conn->query("UPDATE `users` SET `pic`='$fileName', `email`='$email', `mobile`='$mobile' WHERE `id`='$uId'");
                        if ($insert) {
                            $_SESSION['successMsg'] = "Updated successfully.";
                            $_SESSION['userMobile'] = $mobile;
                            $_SESSION['userEmail'] = $email;
                            $_SESSION['userPic'] = $fileName;
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
        } else {
            $insert = $conn->query("UPDATE `users` SET `email`='$email', `mobile`='$mobile' WHERE `id`='$uId'");
            $_SESSION['successMsg'] = "Updated successfully.";
            $_SESSION['userMobile'] = $mobile;
            $_SESSION['userEmail'] = $email;
            header("location: " . urlNow());
            exit();
        }
    }
    if (isset($_POST["add-user"])) {
        $uName = $_POST['uName'];
        $nPass = $_POST['nPass'];
        $nPass2 = $_POST['nPass2'];
        $uRole = $_POST['uRole'];
        $result = $conn->query("SELECT * FROM `users` WHERE `username`='$uName'");
        $sameNameCount = mysqli_num_rows($result);
        if ($sameNameCount == 0) {
            if ($uName && $nPass) {
                if ($nPass2 == $nPass) {
                    $sql = "INSERT INTO `users` (`username`, `password`, `role`) VALUES ('$uName', '$nPass', '$uRole')";
                    $result = mysqli_query($conn, $sql);
                    if ($result) {
                        $_SESSION['successMsg'] = "New User Added Successfully";
                        header("location: " . urlNow());
                        exit();
                    } else {
                        $_SESSION['errorMsg'] = "Something went wrong, try again";
                        header("location: " . urlNow());
                        exit();
                    }
                } else {
                    $_SESSION['errorMsg'] = "Password do not match, try again";
                    header("location: " . urlNow());
                    exit();
                }
            } else {
                $_SESSION['errorMsg'] = "Enter username and password, try again";
                header("location: " . urlNow());
                exit();
            }
        } else {
            $_SESSION['errorMsg'] = "Username already registered";
            header("location: " . urlNow());
            exit();
        }
    }
    if (isset($_POST["del-btn"])) {
        $deleteItem = $_POST["delete"];
        if ($deleteItem) {
            $sql = "DELETE FROM `users` WHERE `id`='$deleteItem'";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                $_SESSION['errorMsg'] = "User Removed Successfully";
                header("location: " . urlNow());
                exit();
            }
        }
    }
}

require('components/_header.php');
?>

<div>
    <?php require('components/_navbar.php') ?>

    <div class="flex overflow-hidden bg-white pt-16">

        <?php require('components/_sidebar.php') ?>

        <div id="main-content" class="h-full w-full bg-gray-50 relative overflow-y-auto lg:ml-64">
            <main>
                <div class="pt-6 px-4">
                    <?php if (isset($_SESSION['successMsg']) && $_SESSION['successMsg']) {
                        require('components/_successMsg.php');
                        $_SESSION['successMsg'] = false;
                    } ?>
                    <?php if (isset($_SESSION['errorMsg']) && $_SESSION['errorMsg']) {
                        require('components/_errorMsg.php');
                        $_SESSION['errorMsg'] = false;
                    } ?>
                    <div class="grid grid-cols-1 xl:gap-4 my-4">
                        <div class="bg-white shadow rounded-lg mb-4 p-4 sm:p-6 h-full">
                            <h3 class="text-2xl leading-none font-bold text-gray-900">Account Settings</h3>
                            <div class="flex flex-wrap items-center justify-center p-12">
                                <div class="mx-auto w-full p-2">
                                    <h3 class="text-xl mb-5 leading-none font-bold text-cyan-600">Update Profile</h3>
                                    <form action="" method="POST" enctype="multipart/form-data">
                                        <div class="-mx-3 flex flex-wrap">
                                            <div class="w-full px-3">
                                                <div class="mb-5">
                                                    <label for="email" class="mb-3 block text-base font-medium text-[#07074D]">
                                                        Email
                                                    </label>
                                                    <input value="<?= $_SESSION['userEmail'] ?>" type="email" name="email" id="email" placeholder="Update email address" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                </div>
                                            </div>
                                            <div class="w-full px-3 sm:w-1/2">
                                                <div class="mb-5">
                                                    <label for="mobile" class="mb-3 block text-base font-medium text-[#07074D]">
                                                        Mobile
                                                    </label>
                                                    <input value="<?= $_SESSION['userMobile'] ?>" type="text" name="mobile" id="mobile" placeholder="Update mobile number" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                </div>
                                            </div>
                                            <div class="w-full px-3 sm:w-1/2">
                                                <div class="mb-5">
                                                    <label for="pic" class="mb-3 block text-base font-medium text-[#07074D]">
                                                        Profile Picture
                                                    </label>
                                                    <input type="file" name="pic" id="pic" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                </div>
                                            </div>
                                        </div>
                                        <button name="update-profile" class="bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 rounded-md py-3 px-8 text-center text-base font-semibold text-white outline-none">
                                            Update Profile
                                        </button>
                                    </form>
                                </div>
                                <div class="mx-auto w-full p-2 mt-10">
                                    <h3 class="text-xl mb-5 leading-none font-bold text-cyan-600">Update Password</h3>
                                    <form action="" method="POST">
                                        <div class="-mx-3 flex flex-wrap">
                                            <div class="w-full px-3">
                                                <div class="mb-5">
                                                    <label for="cPass" class="mb-3 block text-base font-medium text-[#07074D]">
                                                        Current Password
                                                    </label>
                                                    <input type="password" name="cPass" id="cPass" placeholder="Enter New Item Name" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" required />
                                                </div>
                                            </div>
                                            <div class="w-full px-3 sm:w-1/2">
                                                <div class="mb-5">
                                                    <label for="nPass" class="mb-3 block text-base font-medium text-[#07074D]">
                                                        New Password
                                                    </label>
                                                    <input type="password" name="nPass" id="nPass" placeholder="Enter New Item Name" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" required />
                                                </div>
                                            </div>
                                            <div class="w-full px-3 sm:w-1/2">
                                                <div class="mb-5">
                                                    <label for="nPass2" class="mb-3 block text-base font-medium text-[#07074D]">
                                                        Confirm New Password
                                                    </label>
                                                    <input type="password" name="nPass2" id="nPass2" placeholder="Enter New Item Name" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" required />
                                                </div>
                                            </div>
                                        </div>
                                        <button name="update-password" class="bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 rounded-md py-3 px-8 text-center text-base font-semibold text-white outline-none">
                                            Update Password
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php if ($_SESSION['userRole'] == 1) : ?>
                            <div class="bg-white shadow rounded-lg mb-4 p-4 sm:p-6 h-full">
                                <h3 class="text-2xl leading-none font-bold text-gray-900">Admin Control Panel</h3>
                                <div class="flex flex-wrap items-center justify-center p-12">
                                    <div class="mx-auto w-full">
                                        <h3 class="text-xl mb-5 leading-none font-bold text-cyan-600">Add New User</h3>
                                        <form action="" method="POST">
                                            <div class="-mx-3 flex flex-wrap">
                                                <div class="w-full px-3 sm:w-1/2">
                                                    <div class="mb-5">
                                                        <label for="uName" class="mb-3 block text-base font-medium text-[#07074D]">
                                                            Username
                                                        </label>
                                                        <input type="text" name="uName" id="uName" placeholder="Enter New Item Name" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" required />
                                                    </div>
                                                </div>
                                                <div class="w-full px-3 sm:w-1/2">
                                                    <div class="mb-5">
                                                        <label for="uRole" class="mb-3 block text-base font-medium text-[#07074D]">
                                                            Role
                                                        </label>
                                                        <select name="uRole" class="w-full appearance-none rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" id="uRole">
                                                            <option value="4">Delivery</option>
                                                            <option value="3">Production</option>
                                                            <option value="2">Manager</option>
                                                            <option value="1">Admin</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="w-full px-3 sm:w-1/2">
                                                    <div class="mb-5">
                                                        <label for="nPass" class="mb-3 block text-base font-medium text-[#07074D]">
                                                            Set Password
                                                        </label>
                                                        <input type="password" name="nPass" id="nPass" placeholder="Enter New Item Name" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" required />
                                                    </div>
                                                </div>
                                                <div class="w-full px-3 sm:w-1/2">
                                                    <div class="mb-5">
                                                        <label for="nPass2" class="mb-3 block text-base font-medium text-[#07074D]">
                                                            Confirm Password
                                                        </label>
                                                        <input type="password" name="nPass2" id="nPass2" placeholder="Enter New Item Name" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <button name="add-user" class="bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 rounded-md py-3 px-8 text-center text-base font-semibold text-white outline-none">
                                                Add New User
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8 ">
                                    <h3 class="text-xl mb-5 leading-none font-bold text-cyan-600">Users</h3>
                                    <div class="block w-full overflow-x-auto">
                                        <table class="items-center w-full bg-transparent border-collapse">
                                            <thead>
                                                <tr>
                                                    <th class="px-4 bg-gray-50 text-gray-700 align-middle py-3 text-xs font-semibold text-left uppercase border-l-0 border-r-0 whitespace-nowrap">#</th>
                                                    <th class="px-4 bg-gray-50 text-gray-700 align-middle py-3 text-xs font-semibold text-left uppercase border-l-0 border-r-0 whitespace-nowrap">User Name</th>
                                                    <th class="px-4 bg-gray-50 text-gray-700 align-middle py-3 text-xs font-semibold text-left uppercase border-l-0 border-r-0 whitespace-nowrap">Role</th>
                                                    <th class="px-4 bg-gray-50 text-gray-700 align-middle py-3 text-xs font-semibold text-left uppercase border-l-0 border-r-0 whitespace-nowrap">Delete</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                                <?php
                                                $result = $conn->query("SELECT * FROM `users`");
                                                $users = $result->fetch_all(MYSQLI_ASSOC);
                                                foreach ($users as $key => $user) :
                                                ?>
                                                    <tr class="text-gray-500">
                                                        <th class="border-t-0 px-4 align-middle text-sm font-normal whitespace-nowrap p-4 text-left"><?= $key + 1 ?></th>
                                                        <td class="border-t-0 px-4 align-middle text-xs font-medium text-gray-900 whitespace-nowrap p-4"><?= $user['username'] ?></td>
                                                        <td class="border-t-0 px-4 align-middle text-xs font-medium text-gray-900 whitespace-nowrap p-4"><?= $user['role'] == 1 ? 'Admin' : ($user['role'] == 2 ? 'Manager' : ($user['role'] == 3 ? 'Production' : 'Delivery')) ?></td>
                                                        <?php if ($user['role'] != 1) : ?>
                                                            <td class="border-t-0 px-4 align-middle text-xs font-medium text-gray-900 whitespace-nowrap p-4">
                                                                <form action="" method="post">
                                                                    <input type="hidden" name="delete" value="<?= $user['id'] ?>">
                                                                    <button name="del-btn" class="text-rose-900 hover:text-rose-400" type="submit" onclick="return confirm('Are You Sure?')">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z" />
                                                                            <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z" />
                                                                        </svg>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        <?php endif ?>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<?php require('components/_footer.php'); ?>