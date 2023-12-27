
<?php
require_once '../assets/includes/init.php';
include NEWHEADERTOW;
?>

<div class='container-xxl flex-grow-1 container-p-y d-flex ' style=" margin-bottom:5px; flex-flow: column;">

    <div class="d-flex justify-content-between" style="flex: 0 1 auto;">
        <div>Table Generator</div>
    </div>
    <div class='Wrapper' style="flex: 1 1 auto; margin:15px 0 0 0; overflow:hidden;">
        <?php require_once 'tableGenerator.php'?>
    </div>

    <!-- Modal -->
    <div id='modal_go_here'></div>
    <!-- Modal -->
</div> 

<?php include NEWFOOTERTOW ?>