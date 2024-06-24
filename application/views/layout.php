<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo isset($title) ? $title : APP_NAME ?></title>

    <link rel="shortcut icon" href="<?php echo base_url('assets/images/favicon.ico') ?>" type="image/png">

    <!-- Google Font: Source Sans Pro -->
    <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"> -->

    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url('assets/font-awesome/css/font-awesome.min.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/vendor/AdminLTE-3.2.0/plugins/fontawesome-free/css/all-custom.css') ?>">

    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?php echo base_url('assets/vendor/AdminLTE-3.2.0/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo base_url('assets/vendor/AdminLTE-3.2.0/dist/css/adminlte.min.css') ?>">

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/toastr.min.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/jquery-ui.min.css') ?>">

    <link rel="stylesheet" href="<?php echo base_url('assets/css/light.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/dark.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/antrian.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/glow.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/main.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/busy-load.min.css') ?>">

    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="<?php echo base_url('assets/bootstrap-datepicker/bootstrap-datepicker.min.css') ?>">

    <!-- jQuery -->
    <script src="<?php echo base_url('assets/vendor/AdminLTE-3.2.0/plugins/jquery/jquery.min.js') ?>"></script>
    <script type="text/javascript">
        var $ = jQuery.noConflict();
    </script>

    <script src="<?php echo base_url('assets/vendor/moment/moment.js') ?>"></script>
    <script src="<?php echo base_url('assets/vendor/moment/locale/id.js') ?>"></script>
    
    <script src="<?php echo base_url('assets/js/jquery-ui.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/js/busy-load.min.js') ?>"></script>
</head>

<body id="my-layout" class="sidebar-mini layout-footer-fixed layout-navbar-fixed layout-fixed dark-mode sidebar-closed sidebar-collapse">
    <div class="wrapper">
        <?php if (!isset($hasNavigation) || $hasNavigation) {
            $this->load->view('_navigation');
        } ?>

        <div class="container-fluid text-right"><span style="font-size: x-small;"><?php echo get_client_ip() ?></span></div>

        <div class="content-wrapper" style="min-height: 393px;">
            <section class="content container-main">
                <?php $this->load->view($main_body) ?>
            </section>
        </div>
    </div>

    <script type="text/javascript">
        <?php if ($this->session->flashdata('welcome')) : ?>
            openModal('<?php echo base_url('site/view_profile/' . $this->user->id) ?>', {
                title: 'Selamat Datang <?php echo $this->user->nama_lengkap ?>!',
            });
        <?php endif ?>
    </script>

    <?php $this->load->view('_footer') ?>
</body>

</html>