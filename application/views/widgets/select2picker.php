<link rel="stylesheet" href="<?php echo base_url('assets/vendor/AdminLTE-3.2.0/plugins/select2/css/select2.min.css') ?>">
<link rel="stylesheet" href="<?php echo base_url('assets/vendor/AdminLTE-3.2.0/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>">

<style>
   .dark-mode .select2-container--bootstrap4 .select2-selection {
      background-color: #343a40;
      color: #fff;
   }

   .dark-mode .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
      color: #fff;
   }

   .dark-mode .select2-container--focus .select2-selection--single .select2-selection__rendered {
      color: #6c757d;
   }

   .dark-mode .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow b {
      border-color: #fff transparent transparent transparent;
   }

   .dark-mode .select2-container--bootstrap4 .select2-selection {
      border: 1px solid #6c757d;
   }

   .select2-container--bootstrap4 .select2-selection__clear {
      margin-top: .7em;
      margin-right: 0;
      padding-left: 0.2em;
      background-color: transparent;
      color: #6c757d;
   }

   .dark-mode .select2-container--bootstrap4 .select2-dropdown .select2-results__option[aria-selected="true"] {
      color: inherit;
   }
</style>

<?php $field['placeholder'] = isset($field['options'][null]) ? $field['options'][null] : ('Pilih ' . (isset($field['placeholder']) ? $field['placeholder'] : '')) ?>

<?php $unique = $field['id'] . time() ?>
<?php $field['class'] .= ' myselect2_' . $unique ?>

<?php echo form_dropdown($field); ?>

<!-- Select2 -->
<script src="<?php echo base_url('assets/vendor/AdminLTE-3.2.0/plugins/select2/js/select2.full.min.js') ?>"></script>

<script type="text/javascript">
   $(function() {
      let isAjax = <?php echo isset($field['ajaxUrl']) ? 1 : 0 ?>;
      let optionsSelect = {
         theme: 'bootstrap4',
         allowClear: <?php echo !isset($field['allowClear']) || $field['allowClear'] ? 1 : 0 ?>,
         placeholder: '<?php echo isset($field['placeholder']) ? $field['placeholder'] : 'Pilih' ?>',
      };

      if (isAjax) {
         $('.myselect2_<?php echo $unique ?> + span.select2').ready(function() {
            $(this).addClass('form-control');
         })

         optionsSelect = Object.assign(optionsSelect, {
            ajax: {
               url: '<?php echo isset($field['ajaxUrl']) ? $field['ajaxUrl'] : '#' ?>',
               dataType: 'json',
               delay: 250,
               data: function(params) {
                  return {
                     keyword: params.term
                  };
               },
               processResults: function(response) {
                  return {
                     results: response.data
                  };
               },
               cache: true
            }
         });
      }

      $('.myselect2_<?php echo $unique ?>').select2(optionsSelect);

      if (isAjax) {
         let selectedValue = '<?php echo isset($field['selectedValue']) ? $field['selectedValue'] : '' ?>';
         let selectedText = '<?php echo isset($field['selectedText']) ? $field['selectedText'] : '' ?>';

         var $newOption = $("<option selected='selected'></option>").val(selectedValue).text(selectedText);
         $('.myselect2_<?php echo $unique ?>').append($newOption).trigger('change');
      }
   })
</script>