<?php
session_start();

$_SESSION['auth'] = 1;
// if(!isset($_SESSION['auth'])){
//     header('Location: ../login');
//     exit;
// }   
// require_once '../assets/includes/init.php';
// $user->google_client->setAccessToken($_SESSION['token']);
// if($user->google_client->isAccessTokenExpired()){
//     header('Location: ../logout');
//     exit;
//   }
// $google_oauth = new Google_Service_Oauth2($user->google_client);
// $user_info = $google_oauth->userinfo->get();

?>
<!DOCTYPE html>
<html lang="en" class="sidebar-close light-style layout-menu-fixed  layout-compact" data-eg-theme="light">

<head>
    <meta charset="utf-8">
    <title><?=$glob->title?></title>
    <link rel="stylesheet" href="../assets/css/root.css">
    <link rel="stylesheet" href="../assets/vendor/MDB5-6.3.0/css/mdb.min.css">
    <!-- <link rel="stylesheet" href="../assets/vendor/bootstrap-5.3.0-alpha3/css/bootstrap.min.css"> -->
    <link rel="stylesheet" href="../assets/vendor/tui.grid-4.21.19/tui-date-picker.min.css">
    <link rel="stylesheet" href="../assets/vendor/tui.grid-4.21.19/tui-pagination.min.css">
    <link rel="stylesheet" href="../assets/vendor/tui.grid-4.21.19/tui-grid.min.css">
    <link rel="stylesheet" href="../assets/vendor/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="../assets/vendor/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="../assets/vendor/toastr/toastr.min.css">
    <!-- <link rel="stylesheet" href="../assets/css/chosen.min.css"> -->
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/core.css">
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.0/css/boxicons.min.css">

</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">

        <!-- Layout container -->
        <div class="layout-container">

            <!-- Menu -->
            <?php include("sidebar.php"); ?>
            <!-- / Menu -->

            <!-- Layout page -->
            <div class="layout-page" id="layout-page" style="height:100vh; overflow-y: hidden;">

                <!-- Navbar -->
                <!--***************TopNav Gose Here*********** -->
                <?php include("topbar.php"); ?>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper" style="height:100%; overflow-y: hidden;">

                    <!-- Content -->
                    <!-- <div class='container-xxl flex-grow-1 container-p-y'></div> -->