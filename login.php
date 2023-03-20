<?php
session_start();
require('components/_siteUrl.php');
$title = "Login to Paradise";
if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']) {
    header("location: " . url());
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require("components/_dataConnect.php");
    $loginU = $_POST["uName"];
    $loginP = $_POST["password"];
    $verifyUser = $conn->query("SELECT * FROM users WHERE `username`='$loginU'");
    $verifyUserCount = mysqli_num_rows($verifyUser);
    if ($verifyUserCount == 1) {
        $userData = mysqli_fetch_assoc($verifyUser);
        if ($loginP == $userData['password']) {
            $_SESSION['loggedIn'] = true;
            $_SESSION['userId'] = $userData['id'];
            $_SESSION['username'] = $userData['username'];
            $_SESSION['userEmail'] = $userData['email'];
            $_SESSION['userMobile'] = $userData['mobile'];
            $_SESSION['userRole'] = $userData['role'];
            $_SESSION['userPic'] = $userData['pic'];
            header("location: " . url());
            exit();
        } else {
            $_SESSION['errorMsg'] = "Sorry, Email and password do not match";
            header("location: " . url());
            exit();
        }
    } else {
        $_SESSION['errorMsg'] = "Sorry, Invalid email try again";
        header("location: " . url());
        exit();
    }
}

require('components/_header.php');
?>

<div class="relative min-h-screen flex ">
    <div class="flex flex-col sm:flex-row items-center md:items-start sm:justify-center md:justify-start flex-auto min-w-0 bg-white">
        <div class="sm:w-1/2 xl:w-3/5 h-full hidden md:flex flex-auto items-center justify-center p-10 overflow-hidden bg-purple-900 text-white bg-no-repeat bg-cover relative" style="background-image: url(https://images.unsplash.com/photo-1579451861283-a2239070aaa9?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80);">
            <div class="absolute bg-gradient-to-b from-indigo-600 to-blue-500 opacity-75 inset-0 z-0"></div>
            
            <!---remove custom style-->
            <ul class="circles">
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
            </ul>
        </div>
        <div class="md:flex md:items-center md:justify-center w-full sm:w-auto md:h-full w-2/5 xl:w-2/5 p-8  md:p-10 lg:p-14 sm:rounded-lg md:rounded-none bg-white">
            <div class="max-w-md w-full space-y-8">
                <?php if (isset($_SESSION['errorMsg']) && $_SESSION['errorMsg']) {
                    require('components/_errorMsg.php');
                    $_SESSION['errorMsg'] = false;
                } ?>
                <div class="text-center">
                    <h2 class="mt-6 text-3xl font-bold text-gray-900">
                        Welcome
                    </h2>
                    <p class="mt-2 text-sm text-gray-500">Please sign in to your account</p>
                </div>
                <div class="flex items-center justify-center space-x-2">
                    <span class="h-px w-16 bg-gray-200"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-person-fill-lock" viewBox="0 0 16 16">
                        <path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm-9 8c0 1 1 1 1 1h5v-1a1.9 1.9 0 0 1 .01-.2 4.49 4.49 0 0 1 1.534-3.693C9.077 9.038 8.564 9 8 9c-5 0-6 3-6 4Zm7 0a1 1 0 0 1 1-1v-1a2 2 0 1 1 4 0v1a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1v-2Zm3-3a1 1 0 0 0-1 1v1h2v-1a1 1 0 0 0-1-1Z" />
                    </svg>
                    <span class="h-px w-16 bg-gray-200"></span>
                </div>
                <form class="mt-8 space-y-6" action="#" method="POST">
                    <input type="hidden" name="remember" value="true">
                    <div class="relative">
                        <div class="absolute right-3 mt-4">
                        </div>
                        <label for="uName" class="ml-3 text-sm font-bold text-gray-700 tracking-wide">Username</label>
                        <input class=" w-full text-base px-4 py-2 border-b border-gray-300 focus:outline-none rounded-2xl focus:border-indigo-500" type="text" name="uName" placeholder="Enter Username" required>
                    </div>
                    <div class="mt-8 content-center">
                        <label class="ml-3 text-sm font-bold text-gray-700 tracking-wide">
                            Password
                        </label>
                        <input class="w-full content-center text-base px-4 py-2 border-b rounded-2xl border-gray-300 focus:outline-none focus:border-indigo-500" type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="show-password" name="remember_me" type="checkbox" onclick="togglePasswordVisibility()" class="h-4 w-4 bg-blue-500 focus:ring-blue-400 border-gray-300 rounded">
                            <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                                Show password
                            </label>
                        </div>
                    </div>
                    <div>
                        <button type="submit" name="submit" class="w-full flex justify-center bg-gradient-to-r from-indigo-500 to-blue-600  hover:bg-gradient-to-l hover:from-blue-500 hover:to-indigo-600 text-gray-100 p-4  rounded-full tracking-wide font-semibold  shadow-lg cursor-pointer transition ease-in duration-500">
                            Sign in
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require('components/_footer.php'); ?>