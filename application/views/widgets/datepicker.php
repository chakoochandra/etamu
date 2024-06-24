<?php echo form_datepicker($field) ?>

<script>
    $(document).ready(function() {
        let format = '<?php echo isset($field['format']) ? $field['format'] : 'yyyy-mm-dd' ?>';
        let endDate = '<?php echo isset($field['endDate']) ? $field['endDate'] : '+1y' ?>';

        switch (format) {
            case 'yyyy':
                var options = {
                    format: "yyyy",
                    viewMode: "years",
                    minViewMode: "years",
                    autoclose: true,
                    clearBtn: true,
                    todayBtn: 'linked',
                    todayHighlight: true,
                    endDate: endDate,
                };
                break;
            default:
                var options = {
                    format: format,
                    autoclose: true,
                    clearBtn: true,
                    todayHighlight: true,
                    todayBtn: 'linked',
                    endDate: endDate,
                };
                break;
        }

        $('#<?php echo $field['id'] ?>').datepicker(options);
    })
</script>
