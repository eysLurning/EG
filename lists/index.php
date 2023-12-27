
<?php
require_once '../assets/includes/init.php';
include NEWHEADERTOW;
?>

<div class='container-xxl flex-grow-1 container-p-y d-flex ' style=" margin-bottom:5px; flex-flow: column;">

    <div class="d-flex justify-content-between" style="flex: 0 1 auto;">
        <div>Table Info</div>
        <div class="d-flex"style="gap: 8px;">
            <button id="new_record" class='button_icon_wrapper' data-bs-toggle="modal" data-bs-target="#basicModal"><i class='bx bx-plus button_icon_icon' ></i>New</button>
            <button id="clear_Filetrs" class='button_icon_wrapper'><i class='bx bx-filter-alt button_icon_icon'></i>Clear</button>
            <!-- <div class="date_picker_wrapper">
                <div class="center-icon-wrapper">
                    <i class='bx bx-calendar-alt' ></i>
                </div>
                <input id="date_range_picker"/>
            </div> -->
        </div>
    </div>
    <div class='table-Wrapper' style="flex: 1 1 auto; margin:15px 0 0 0; overflow:hidden;">
        <div class="tab-content" id="myTabContent" style="flex: 1 1 auto;position: relative;padding:0">
            <div class="tab-pane fade show active" id="grid_wrapper" role="tabpanel" aria-labelledby="home-tab" style="flex: 1 1 auto; height:100%">
                <div id="grid"></div>
            </div>
        </div>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" type="button" role="tab"></button>
            </li>
        </ul>
    </div>

    <!-- Modal -->
    <div id='modal_go_here'></div>
    <!-- Modal -->
</div> 

<?php include NEWFOOTERTOW ?>