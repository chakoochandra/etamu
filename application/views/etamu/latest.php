<?php if ($latest) : ?>
    <div class="p-2 col">
        <div class="card mb-3">
            <div class="d-flex justify-content-between px-4 pt-4">
                <p class="card-text"><small class="text-muted"><?php echo formatDate($latest->visit_date, "%A, %d/%m/%y %H:%M") ?></small></p>
                <?php echo anchor(base_url("etamu/update/{$latest->id}/1"), '<i class="fa fa-pencil"></i>&nbsp;Ubah Data', [
                    'title'    => 'Perbarui',
                    'class'    => 'btn btn-xs btn-outline-warning btn-modal',
                ]) ?>
            </div>
            <?php if ($latest->photo && ($fileUrl = file_url('foto_tamu', $latest->photo))) : ?><img class="card-img-top w-100 px-4 pt-2" src="<?php echo $fileUrl ?>" alt="Foto Tamu"><?php endif ?>
            <div class="card-body d-flex flex-column">
                <span class="card-title big d-flex align-item-left mb-2">IDENTITAS TAMU</span>
                <dl class="row">
                    <dt class="col-sm-4">Yang Dituju</dt>
                    <dd class="col-sm-8"><?php echo $latest->person ?></dd>
                    <dt class="col-sm-4">Nama Tamu</dt>
                    <dd class="col-sm-8"><?php echo ($latest->gender == 0 ? 'Bpk. ' : 'Ibu ') . $latest->name ?></dd>
                    <dt class="col-sm-4">Kontak</dt>
                    <dd class="col-sm-8"><?php echo $latest->phone_number . ($latest->phone_number && $latest->email ? ' / ' : '') . $latest->email ?></dd>
                    <dt class="col-sm-4">Jumlah Tamu</dt>
                    <dd class="col-sm-8"><?php echo $latest->guest_count ?> orang</dd>
                    <dt class="col-sm-4">Instansi/Lembaga</dt>
                    <dd class="col-sm-8"><?php echo $latest->organization ?></dd>
                    <dt class="col-sm-4">Alamat</dt>
                    <dd class="col-sm-8"><?php echo $latest->address ?></dd>
                    <dt class="col-sm-4">Keperluan</dt>
                    <dd class="col-sm-8"><?php echo $latest->purpose ?: '-' ?></dd>
                    <dt class="col-sm-4">Pesan</dt>
                    <dd class="col-sm-8"><?php echo $latest->message ?: '-' ?></dd>
                </dl>
            </div>
        </div>
    </div>
<?php endif ?>