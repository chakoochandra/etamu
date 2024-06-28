<div class="card m-0">
    <h3 class="text-center p-4">eTAMU <?php echo strtoupper(SATKER_NAME) ?></h3>

    <div class="card-body pb-2">
        <?php echo form_open_multipart(uri_string(), ['class' => $form['formClass']]) ?>

        <div class="d-flex flex-row">
            <div class="p-2 d-flex flex-column justify-content-center w-50" id="container-preview">
                <h4 class="text-center">REKAM WAJAH</h4>

                <?php echo '<div class="input-group mb-0 align-items-center container-camera" style="display: block;">' ?>
                <?php $form['photo']['id'] = $form['photo']['name'] ?>
                <?php $form['photo']['class'] = (isset($form['photo']['class']) ? $form['photo']['class'] : '') ?>
                <?php echo switch_input('form_camera', $form['photo']) ?>
                <?php echo '</div>' ?>
            </div>
            <div class="p-2 d-flex flex-column">
                <?php if (isset($options['message']) && $options['message']) : ?>
                    <div class="p-2">
                        <?php echo alert($options['message'], 'Kelengkapan Data', ['class' => 'small alert-danger']) ?>
                    </div>
                <?php endif ?>
                <?php if (isset($form['visit_date'])) : ?>
                    <div class="p-2">
                        <?php echo '<label for="' . $form['visit_date']['name'] . '">' . $form['visit_date']['placeholder'] . '</label>' ?>
                        <?php echo '<div class="input-group mb-0 align-items-center justify-content-center">' ?>
                        <?php $form['visit_date']['id'] = $form['visit_date']['name'] ?>
                        <?php $form['visit_date']['class'] = 'form-control' ?>
                        <?php echo switch_input('form_datetimepicker', $form['visit_date']) ?>
                        <?php echo '</div>' ?>
                    </div>
                <?php endif ?>
                <div class="p-2">
                    <?php echo '<label for="' . $form['person_to_meet']['name'] . '">' . $form['person_to_meet']['label'] . '</label>' ?>
                    <?php $form['person_to_meet']['id'] = $form['person_to_meet']['name'] ?>
                    <?php $form['person_to_meet']['class'] = 'form-control' ?>
                    <?php echo switch_input('form_dropdown', $form['person_to_meet']) ?>
                </div>
                <div class="p-2">
                    <?php echo '<label for="' . $form['name']['name'] . '">' . $form['name']['placeholder'] . '</label>' ?>
                    <?php $form['name']['id'] = $form['name']['name'] ?>
                    <?php $form['name']['class'] = 'form-control' ?>
                    <?php echo switch_input('form_textfield', $form['name']) ?>
                </div>
                <div class="p-2 d-flex align-items-center">
                    <?php echo '<label class="mr-4" for="' . $form['gender']['name'] . '">' . $form['gender']['label'] . '</label>' ?>
                    <?php $form['gender']['id'] = $form['gender']['name'] ?>
                    <?php $form['gender']['class'] = 'form-control' ?>
                    <?php echo switch_input('form_toggler', $form['gender']) ?>
                </div>
                <div class="d-flex flex-row">
                    <div class="p-2">
                        <?php echo '<label for="' . $form['phone_number']['name'] . '">' . $form['phone_number']['placeholder'] . '</label>' ?>
                        <?php echo '<div class="input-group mb-0 align-items-center justify-content-center">' ?>
                        <?php $form['phone_number']['id'] = $form['phone_number']['name'] ?>
                        <?php $form['phone_number']['class'] = 'form-control' ?>
                        <?php echo switch_input('form_textfield', $form['phone_number']) ?>
                        <?php echo '</div>' ?>
                        <?php echo switch_input('form_info', $form['form_info']) ?>
                    </div>
                    <div class="p-2">
                        <?php echo '<label for="' . $form['guest_count']['name'] . '">' . $form['guest_count']['placeholder'] . '</label>' ?>
                        <?php $form['guest_count']['id'] = $form['guest_count']['name'] ?>
                        <?php $form['guest_count']['class'] = 'form-control' ?>
                        <?php echo switch_input('form_textinput', $form['guest_count']) ?>
                    </div>
                </div>
                <div class="p-2">
                    <?php echo '<label for="' . $form['organization']['name'] . '">' . $form['organization']['placeholder'] . '</label>' ?>
                    <?php $form['organization']['id'] = $form['organization']['name'] ?>
                    <?php $form['organization']['class'] = 'form-control' ?>
                    <?php echo switch_input('form_textinput', $form['organization']) ?>
                </div>
            </div>
            <div class="p-2 d-flex flex-column">
                <div class="p-2">
                    <?php echo '<label for="' . $form['address']['name'] . '">' . $form['address']['placeholder'] . '</label>' ?>
                    <?php $form['address']['id'] = $form['address']['name'] ?>
                    <?php $form['address']['class'] = 'form-control' ?>
                    <?php echo switch_input('form_textarea', $form['address']) ?>
                </div>
                <div class="p-2">
                    <?php echo '<label for="' . $form['purpose']['name'] . '">' . $form['purpose']['placeholder'] . '</label>' ?>
                    <?php $form['purpose']['id'] = $form['purpose']['name'] ?>
                    <?php $form['purpose']['class'] = 'form-control' ?>
                    <?php echo switch_input('form_textarea', $form['purpose']) ?>
                </div>
                <div class="p-2">
                    <?php echo '<label for="' . $form['message']['name'] . '">' . $form['message']['placeholder'] . '</label>' ?>
                    <?php $form['message']['id'] = $form['message']['name'] ?>
                    <?php $form['message']['class'] = 'form-control' ?>
                    <?php echo switch_input('form_textarea', $form['message']) ?>
                </div>
                <?php if ($form['status']['visible']) : ?>
                    <?php echo '<label for="' . $form['status']['name'] . '">' . $form['status']['label'] . '</label>' ?>
                    <div class="p-2 d-flex justify-content-between align-items-center">
                        <?php $form['status']['id'] = $form['status']['name'] ?>
                        <?php $form['status']['class'] = 'form-control' ?>
                        <?php echo switch_input('form_toggler', $form['status']) ?>
                    </div>
                <?php endif ?>
                <div class="p-2 d-flex flex-row">
                    <?php if ($form['showBtnCloseModal']) : ?>
                        <button type="button" class="col btn btn-warning m-1" data-dismiss="modal" style="min-width: 100px;"><i class="fa fa-times"></i> Batal</button>
                    <?php endif ?>
                    <button type="submit" class="col btn btn-success m-1" style="min-width: 100px;"><i class="fa fa-save"></i> Simpan</button>
                </div>
            </div>
        </div>

        <?php echo form_close() ?>
    </div>
</div>