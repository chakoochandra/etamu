<?php if (isset($showProfile) ? $showProfile : false) : ?>
    <div class="profile-small">
        <?php $this->load->view('widgets/profile_small') ?>
    </div>
<?php endif ?>

<?php if (isset($form)) echo my_form($form, array_merge([
    'action' => '',
    'title' => isset($formTitle) ? $formTitle : false,
    'backUrl' => isset($backUrl) ? $backUrl : '',
    'message' => isset($message) ? $message : null,
    'showBtnCloseModal' => isset($showBtnCloseModal) ? $showBtnCloseModal : true,
    'isConfirmation' => isset($isConfirmation) ? $isConfirmation : false,
    'submitLabel' => isset($submitLabel) ? $submitLabel : false,
    'submitIcon' => isset($submitIcon) ? $submitIcon : false,
], (isset($formOptions) ? $formOptions : [])), (isset($attributes) ? $attributes : [])) ?>