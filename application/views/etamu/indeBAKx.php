<h3 class="text-center">eTamu <?php echo SATKER_NAME ?></h3>
<div class="d-flex flex-row justify-content-center align-self-center">
    <div class="d-flex flex-column justify-content-center align-self-center w-100 p-2 col-6">
        <?php $this->load->view('widgets/form', ['formOptions' => ['showBtnCloseModal' => false, 'ajax' => false], 'attributes' => ['class' => 'form-create']]) ?>
    </div>
    <?php $this->load->view('etamu/preview') ?>
</div>
