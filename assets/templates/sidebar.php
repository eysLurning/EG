

<?php

class Sidebar {

    private $glob;

    public function __construct($glob) {
        $this->glob = $glob;
    }

    public function render() {
        ?>
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
                // Loop over all available side nav items and display them
                foreach($this->glob->side_nav as $nav_item) {
                    if ($this->isNavItemVisible($nav_item)) {
                        $this->renderNavItem($nav_item);
                    }
                }
                ?>
            </ul>
            <!-- /Links -->
        </aside>
        <?php
    }

    private function isNavItemVisible($navItem) {
        return isset($_SESSION['auth']) && $_SESSION['auth'] >= $navItem->auth && $navItem->active;
    }

    private function renderNavItem($navItem) {
        ?>
        <li>
            <?=$navItem->children ? "<div class='sidebar-icon-link'>":'' ?>
                <a href="/<?=$this->glob->name?>/<?=$navItem->name?>">
                    <i class='bx <?=$navItem->icon?>'></i>
                    <span class='link-name'><?=$navItem->display_name?></span>
                </a>
                <?=$navItem->children ? "<i class='bx bx-chevron-down arrow'></i></div>": '' ?>
            <ul class='sidebar-sub-menu <?=$navItem->children ? "": "blank"?>'>
                <li><a class='link-name' href="/<?=$this->glob->name?>/<?=$navItem->name?>"><?=$navItem->display_name?></a></li>
                <?php 
                if($navItem->children) {
                    foreach($navItem->children as $child) {
                        $this->renderChildItem($navItem,$child);
                    }
                }
                ?>
                <?php $this->renderUserProfile(); ?>
                <?php $this->renderNotifications(); ?>
            </ul>
        </li>
        <?php
    }
    private function renderChildItem($navItem,$child) {
        ?>
        <li><a class href="<?=$this->getChildItemLink($navItem,$child)?>"><?=$child->display_name?></a></li>
        <?php
    }
    private function getChildItemLink($navItem,$child) {
        // Customize the link generation logic based on your requirements
        return '/'.$this->glob->name.'/'.$navItem->name.'/'.$child->display_name ?: "javascript:void(0)";
    }

    // Additional Features

    private function renderUserProfile() {
        // Add logic to render a user profile section
        // ...
    }

    private function renderNotifications() {
        // Add logic to render a notification or alert system
        // ...
    }
}

$sidebar = new Sidebar($glob);
$sidebar->render();

?>
