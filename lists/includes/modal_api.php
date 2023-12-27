<?php
    require_once '../../assets/includes/init.php';
    // validate user
    if(isset($_GET['modal'])){
        switch ($_GET['modal']) {
            case 'new_customers':
                require_once 'new_customer.php';
                break;
            case 'new_item':
                require_once 'new_item.php';
                break;
            default:
                # code...
                break;
        }
    }