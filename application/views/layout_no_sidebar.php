<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo ($title = isset($title) ? $title : APP_NAME) ?></title>

    <link rel="shortcut icon" href="<?php echo base_url('assets/images/favicon.ico') ?>" type="image/png">

    <!-- Google Font: Source Sans Pro -->
    <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"> -->
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url('assets/font-awesome/css/font-awesome.min.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/vendor/AdminLTE-3.2.0/plugins/fontawesome-free/css/all-custom.css') ?>">

    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="<?php echo base_url('assets/vendor/AdminLTE-3.2.0/plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo base_url('assets/vendor/AdminLTE-3.2.0/dist/css/adminlte.min.css') ?>">

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/toastr.min.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/jquery-ui.min.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/busy-load.min.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/dark.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/title.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/antrian.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/main.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/glow.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/particles/particles.css') ?>">

    <!-- jQuery -->
    <script src="<?php echo base_url('assets/vendor/AdminLTE-3.2.0/plugins/jquery/jquery.min.js') ?>"></script>
    <script type="text/javascript">
        var $ = jQuery.noConflict();
    </script>

    <script src="<?php echo base_url('assets/js/jquery-ui.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/js/busy-load.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/particles/particles.min.js') ?>"></script>

    <script src="<?php echo base_url('assets/vendor/moment/moment.js') ?>"></script>
    <script src="<?php echo base_url('assets/vendor/moment/locale/id.js') ?>"></script>
</head>

<body id="my-layout-plain" class="<?php echo get_layout_classes('mode-layout-plain') . ' dark-mode' ?>">
    <div id="particles-js"></div> <!-- stats - count particles -->

    <div class="container-main d-flex justify-content-center">
        <?php $this->load->view($main_body) ?>
    </div>

    <script type='text/javascript'>
        $(document).ready(function() {
            particlesJS.load('particles-js', '<?php echo base_url('assets/particles/particles.json') ?>', function() {});
        });
    </script>

    <?php $this->load->view('_footer') ?>
</body>

</html>