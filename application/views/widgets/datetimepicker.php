<script src="<?php echo base_url('assets/vendor/moment/moment.js') ?>"></script>
<script src="<?php echo base_url('assets/vendor/moment/locale/id.js') ?>"></script>

<link rel="stylesheet" href="<?php echo base_url('assets/bootstrap-datepicker/bootstrap-datetimepicker.min.css') ?>">

<input id="datetimepicker-<?php echo $field['name'] ?>" type='text' name="<?php echo $field['name'] ?>" placeholder="<?php echo isset($field['placeholder']) ? $field['placeholder'] : 'Pilih Waktu' ?>" value="<?php echo (new DateTime($field['value']))->format('d F Y H:i') ?>" class="form-control" />
<div class="input-group-append">
    <div class="input-group-text">
        <span class="fa fa-calendar"></span>
    </div>
</div>

<script src="<?php echo base_url('assets/bootstrap-datepicker/bootstrap-datetimepicker.min.js') ?>"></script>
<script>
    $(document).ready(function() {
        if (window.jQuery().datetimepicker) {
            $('#datetimepicker-<?php echo $field['name'] ?>').datetimepicker({
                format: '<?php echo isset($field['format']) ? $field['format'] : 'DD MMMM YYYY HH:mm' ?>',
                icons: {
                    time: 'fa fa-clock-o',
                    date: 'fa fa-calendar',
                    up: 'fa fa-chevron-up',
                    down: 'fa fa-chevron-down',
                    previous: 'fa fa-chevron-left',
                    next: 'fa fa-chevron-right',
                    today: 'fa fa-check',
                    clear: 'fa fa-trash',
                    close: 'fa fa-times'
                }
            });

            $('form').on('submit', function() {
                var datetimeValue = $('#datetimepicker-<?php echo $field['name'] ?>').val();
                if (datetimeValue) {
                    $('#datetimepicker-<?php echo $field['name'] ?>').val(moment(datetimeValue, 'DD MMMM YYYY HH:mm').format('YYYY-MM-DD HH:mm:ss'));
                }
            });
        }
    })
</script>