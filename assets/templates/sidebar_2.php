<?php ?>


<aside id="sidebar" class=''>
    <!-- Logo -->
    <div class='logo-details'>
        <img src='../assets/images/logox200.png'/>
        <!-- <i class='bx bxl-c-plus-plus'></i> -->
        <span class='logo-name'>Infinity</span>
        <!-- <i class='bx bx-menu' id="btn" ></i> -->
        <i class='bx bx-chevron-right toggle' id="btn"></i>
    </div>
    <!-- /Logo -->
    <!-- Links -->
    <ul class='nav-links'>
        <!-- Search -->
        <!-- <li>
            <i class='bx bx-search' ></i>
            <input type="text" placeholder="Search...">
            <span class="tooltip">Search</span>
        </li> -->
        <!-- /Search -->
        <!-- Single Link -->
        <li>
            <a href="/infinity/home">
                <i class='bx bx-home'></i>
                <span class="link-name">Home</span>
            </a>
            <ul class="sidebar-sub-menu blank">
                <li><a class="link-name" href="/infinity/home">Home</a></li>
            </ul>
        </li>
        <!-- /Single Link -->

        <li>
            <a href="/infinity/home">
                <i class='bx bx-grid-alt' ></i>
                <span class="link-name">Dashboard</span>
            </a>
            <ul class="sidebar-sub-menu blank">
                <li><a class="link-name" href="/infinity/home">Dashboard</a></li>
            </ul>
        </li>

        <li>
            <div class='sidebar-icon-link'>
                <a href="/infinity/files">
                <i class='bx bx-folder'></i>
                    <span class='link-name'>Files</span>
                </a>
                <i class='bx bx-chevron-down arrow'></i>
            </div>
            <ul class='sidebar-sub-menu'>
                <li><a class='link-name' href="/infinity/files">Files</a></li>
                <li><a id='sidebar-upload-files'class href="javascript:void(0)">Upload Files</a></li>
                <li><a class href="javascript:void(0)">Temp Files</a></li>
                <li><a class href="javascript:void(0)">Other</a></li>
            </ul>
        </li>

        <li>
            <div class='sidebar-icon-link'>
                <a href="/infinity/fs">
                    <i class='bx bx-folder' ></i>
                    <span class='link-name'>FS OLD</span>
                </a>
                <i class='bx bx-chevron-down arrow'></i>
            </div>
            <ul class='sidebar-sub-menu'>
                <li><a class='link-name' href="/infinity/fs">FS OLD</a></li>
                <li><a class href="javascript:void(0)">Catagorys</a></li>
                <li><a class href="javascript:void(0)">Companys</a></li>
                <li><a class href="javascript:void(0)">Other</a></li>
            </ul>
        </li>

        


        <li>
            <a href="/infinity/todos">
                <i class='bx bx-task' ></i>
                <span class="link-name">Todo</span>
            </a>
            <ul class="sidebar-sub-menu blank">
                <li><a class="link-name" href="/infinity/todos">Todo</a></li>
            </ul>
        </li>
        <li>
            <a href="/infinity/test">
                <i class='bx bx-data'></i>
                <span class="link-name">Test</span>
            </a>
            <ul class="sidebar-sub-menu blank">
                <li><a class="link-name" href="/infinity/test">Test</a></li>
            </ul>
        </li>
        <li>
            <a href="/infinity/ui">
                <i class='bx bx-box'></i>
                <span class="link-name">UI</span>
            </a>
            <ul class="sidebar-sub-menu blank">
                <li><a class="link-name" href="/infinity/test">UI</a></li>
            </ul>
        </li>

        <li>
            <a href="/infinity/deals">
                <i class='bx bx-cart'></i>
                <span class="link-name">Deals</span>
            </a>
            <ul class="sidebar-sub-menu blank">
                <li><a class="link-name" href="/infinity/deals">Deals</a></li>
            </ul>
        </li>

        <li>
            <a href="/infinity/lists">
                <i class='bx bx-cart'></i>
                <span class="link-name">Lists</span>
            </a>
            <ul class="sidebar-sub-menu blank">
                <li><a class="link-name" href="/infinity/lists">Lists</a></li>
            </ul>
        </li>

        


        <li>
            <a href="/infinity/settings/profile.php#user_settings">
                <i class='bx bx-cog' ></i>
                <span class="link-name">Setting</span>
            </a>
            <ul class="sidebar-sub-menu blank">
                <li><a class="link-name" href="/infinity/settings/profile.php#user_settings">Setting</a></li>
            </ul>
        </li>
        
        
        
        <?php if($_SESSION['auth'] > 1){?>
        <li>
            <a href="/infinity/settings/admin.php">
                <i class='bx bx-shield'></i>
                <span class="link-name">Admin</span>
            </a>
            <ul class="sidebar-sub-menu blank">
                <li><a class="link-name" href="/infinity/settings/admin.php">Admin</a></li>
            </ul>
        </li>
        <?php } ?>


        <!-- <li>
            <a id='mode-toggle' href="javascript:void(0)" class="mode-toggler">
                <i class='bx bx-sun'></i>
                <span class="link-name">Mode</span>
            </a>
        </li> -->
        <!-- <li>
            <div class="profile-details">
                <div class="profile-content">
                    <img src="<?=$_SESSION['user']['picture']?>" alt="profileImg">
                </div>
                <div class="name-job">
                    <div class="profile_name"><?=$_SESSION['user']['full_name']?></div>
                    <div class="job"><?=
                            $_SESSION['auth'] > 1 ?
                             'Admin':
                             ($_SESSION['auth'] === 1 ?
                              'User' : null)
                               ?></div>
                </div>
                <i class='bx bx-log-out' ></i>
            </div>
        </li> -->

    </ul>
</aside>








<!-- Sidebar 1 -->
<?php ?>


<aside id="sidebar" class=''>
    <!-- Logo -->
    <div class='logo-details'>
        <img src='../assets/images/logox200.png'/>
        <span class='logo-name'>Infinity</span>
        <i class='bx bx-chevron-right toggle' id="btn"></i>
    </div>
    <!-- /Logo -->
    
    <!-- Links -->
    <ul class='nav-links'>
        <?php 
        //loop over all the avalable side nav items and display them
        foreach($glob->side_nav as $nav_item){  
            //generate the side links based on auth level 
            if(isset($_SESSION['auth']) && $_SESSION['auth'] >= $nav_item->auth){
                // only display active links (this can be turnd off per user)
                if($nav_item->active){
                    ?>
                    <li>
                        <?=$nav_item->children ? "<div class='sidebar-icon-link'>":'' ?>
                            <a href="/<?=$glob->name?>/<?=$nav_item->name?>">
                                <i class='bx <?=$nav_item->icon?>'></i>
                                <span class='link-name'><?=$nav_item->display_name?></span>
                            </a>
                            <?=$nav_item->children ? "<i class='bx bx-chevron-down arrow'></i>
                            </div>": '' ?>
                        <ul class='sidebar-sub-menu <?=$nav_item->children ? "": "blank"?>'>
                            <li><a class='link-name' href="/<?=$glob->name?>/<?=$nav_item->name?>"><?=$nav_item->display_name?></a></li>
                            <?php 
                                if($nav_item->children){
                                    foreach($nav_item->children as $child){
                                        ?>
                                        <li><a class href="<?='/'.$glob->name.'/'.$nav_item->name.'/'.$child->display_name ?:"javascript:void(0)"?> "><?=$child->display_name?></a></li> 
                                        <?php
                                    }
                                }
                            ?>
                        </ul>
                    </li>
                    <?php
                }
            } 
        }
        ?>

</ul>
<!-- /Links -->
</aside>



<!-- //to add back later when i activate the session -->
<!-- <li>
    <div class="profile-details">
        <div class="profile-content">
            <img src="<?=isset($_SESSION['auth']['picture']) &&  $_SESSION['user']['picture']?>" alt="profileImg">
        </div> 
        <div class="name-job">
            <div class="profile_name"><?=isset($_SESSION['user']['full_name']) && $_SESSION['user']['full_name']?></div>
            <div class="job"><?=
                     isset($_SESSION['auth']) && $_SESSION['auth'] > 1 ?
                     'Admin':
                     ( isset($_SESSION['auth']) && $_SESSION['auth'] === 1 ?
                      'User' : null)
                       ?></div>
        </div>
        <i class='bx bx-log-out' ></i>
    </div>
</li> -->