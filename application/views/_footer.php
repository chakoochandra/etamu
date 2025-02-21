<link rel="stylesheet" href="<?php echo base_url('assets/css/waviy.css') ?>">

<div class="modal fade" id="modal-input" role="dialog" style="z-index: 1040;">
    <div id="modal-input-dialog" class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" style="color: darkturquoise; text-align: center; font-weight: bold; ">KETIKKAN NOMOR PERKARA</h4>
            </div>
            <div class="modal-body"></div>
            <big>
                <h1 id="counter" class="mb-3" style="color: red; text-align: center; margin-top: 0; display: none;"></h1>
            </big>
        </div>
    </div>
</div>

<!-- Bootstrap 4 -->
<script src="<?php echo base_url('assets/vendor/AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url('assets/vendor/AdminLTE-3.2.0/dist/js/adminlte.min.js') ?>"></script>

<!-- overlayScrollbars -->
<script src="<?php echo base_url('assets/vendor/AdminLTE-3.2.0/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') ?>"></script>

<!-- bootstrap datepicker -->
<script src="<?php echo base_url('assets/bootstrap-datepicker/bootstrap-datepicker.min.js') ?>"></script>
<script src="<?php echo base_url('assets/js/datepicker.js') ?>"></script>

<script src="<?php echo base_url('assets/vendor/AdminLTE-3.2.0/plugins/jquery-knob/jquery.knob.min.js') ?>"></script>
<script src="<?php echo base_url('assets/js/toastr.min.js') ?>"></script>
<script src="<?php echo base_url('assets/js/jquery.history.min.js') ?>"></script>
<script src="<?php echo base_url('assets/vendor/webcam-easy/webcam-easy.js') ?>"></script>
<script src="<?php echo base_url('assets/js/main.js') ?>"></script>

<script type='text/javascript'>
    var csrf_token_name = '<?php echo $this->security->get_csrf_token_name() ?>';
    var csrf_hash = '<?php echo $this->security->get_csrf_hash() ?>';

    toastr.options = {
        "debug": false,
        "positionClass": "toast-custom-top-full-width",
        "progressBar": true,
        "onclick": null,
        "fadeIn": 300,
        "fadeOut": 1000,
        "timeOut": 5000,
        "extendedTimeOut": 1000
    }

    <?php if ($this->session->flashdata('success')) { ?>
        toastr.success("<?php echo $this->session->flashdata('success') ?>");
    <?php } else if ($this->session->flashdata('error')) { ?>
        toastr.error("<?php echo $this->session->flashdata('error') ?>");
    <?php } else if ($this->session->flashdata('warning')) { ?>
        toastr.warning("<?php echo $this->session->flashdata('warning') ?>");
    <?php } else if ($this->session->flashdata('info')) { ?>
        toastr.info("<?php echo $this->session->flashdata('info') ?>");
    <?php } ?>

    function checkGateway() {
        fetch('<?php echo base_url('etamu/check_gateway') ?>')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                if (data.status) {
                    const result = JSON.parse(data.response);
                    if (result.name) {
                        $('#container-info-sidebar').append(`<div class="callout callout-${result.is_expired || result.is_out_of_limit || !result.status?'danger':'success'} d-flex flex-column mb-0 ml-4 py-1" role="alert">
                            <span class="badge badge-${result.status?'success':'danger'}">${result.status?'Aktif':'Tidak Aktif'}</span>
                            <span>Sesi: <strong>${result.name}</strong></span>
                            <span>${result.number}</span>
                            <span>Limit : ${new Intl.NumberFormat('id-ID').format(result.limit_message)} pesan</span>
                            <span>Expired: ${moment(result.expires).format('dddd, Do MMMM YYYY h:mm')}</span>
                        </div>`);
                    } else {
                        $('button.btn-submit').hide();
                        $('#container-info').append(`<div class="callout callout-danger d-flex align-items-center mb-0 ml-4 py-1" role="alert">${result.message}</div>`);
                    }
                } else {
                    $('button.btn-submit').hide();
                    $('#container-info').append(`<div class="callout callout-danger d-flex align-items-center mb-0 ml-4 py-1" role="alert">${data.message}</div>`);
                    $('#container-info-sidebar').append(`<div class="callout callout-danger d-flex align-items-center mb-0 ml-4 py-1" role="alert">${data.message}</div>`);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
            });
    }

    $(document).ready(function() {
        checkGateway();
    })

    $("body").on("submit", ".form-create", function(e) {
        e.preventDefault();
        postAjax($(".form-create").attr("action"), new FormData(this), false, false);
    });
</script>