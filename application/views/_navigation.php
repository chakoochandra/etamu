<?php

function getMenu()
{
    $CI = get_instance();
    $CI->db->distinct();
    $CI->db->select('a.*');
    $CI->db->from('tref_menu a');
    return $CI->db->where('a.status = 1')->order_by('a.parent ASC, a.order ASC')->get()->result();
}

function iterateMenu($menus, $parent = null)
{
    $data = array();
    foreach ($menus as $menu) {
        if ($menu->parent == $parent || (empty($parent) && empty($menu->parent))) {
            $temp = array();
            $temp['title'] = $menu->label;
            $temp['icon'] = $menu->icon ?: 'circle-o';

            if ($menu->url) {
                $temp['href'] = base_url($menu->url);
            }

            if ($menu->iconClass) {
                $temp['iconClass'] = $menu->iconClass;
            }

            if ($menu->menuClass) {
                $temp['class'] = $menu->menuClass;
            }

            $child = iterateMenu($menus, $menu->id);
            if ($child) {
                $temp['child'] = $child;
            }

            $data[] = $temp;
        }
    }
    return $data;
}

$type = isset($type) ? $type : '';

$menus = get_or_set_menu();


?>

<nav id="my-navbar" class="main-header navbar navbar-expand navbar-dark">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fa fa-bars"></i></a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
    </ul>
</nav>

<aside id="my-sidebar" class="main-sidebar elevation-4 sidebar-dark-primary">
    <a href="<?php echo base_url("/") ?>" class="d-flex brand-link">
        <img src="<?php echo base_url('assets/images/icon.jpg') ?>" alt="Logo <?php echo APP_SHORT_NAME ?>" class="brand-image" style="opacity: .8;">
        <span class="brand-text font-weight-bold"><?php echo APP_SHORT_NAME ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image justify-content-center align-self-center">
                <img src="<?php echo base_url('assets/images/icon.jpg') ?>" class="img-circle elevation-4" alt="User" style="object-fit: cover;">
            </div>
            <div class="info" style="line-height: 1;">
                Admin
                <span style="font-size: x-small;"><?php echo get_client_ip() ?></span>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="my-menu nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <?php
                $this->load->helper('mymenu_helper');
                echo (new mymenu($menus))->printMenu();
                ?>
            </ul>

            <div id="container-info-sidebar" class="d-flex my-5 py-5"></div>
        </nav>
    </div>
</aside>