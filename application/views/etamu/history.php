<div class="d-flex">
    <?php echo filter_form($form, 'etamu/history') ?>
</div>

<?php echo $this->pagination->get_summary() ?>

<table class="table table-hover table-sticky">
    <thead>
        <tr>
            <th>No.</th>
            <th>Tanggal</th>
            <th>Yang Dituju</th>
            <th>Nama Tamu</th>
            <th>Instansi/Lembaga</th>
            <th>Alamat</th>
            <th>Tujuan</th>
            <th>Pesan</th>
            <th>Jumlah Tamu</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php if ($data) : ?>
            <?php foreach ($data as $i => $row) : ?>
                <tr <?php echo $row->status == 1 ? 'class="bg-danger"' : ($row->status == 2 ? 'class="bg-success"' : 'class="bg-default"') ?>>
                    <td><?php echo $i + $offset ?></td>
                    <td align="center"><?php echo formatDate($row->visit_date, "%A, %d/%m/%y %H:%M:%S") ?></td>
                    <td><?php echo $row->person ?><?php echo $row->status === '0' ? '<br><span class="badge badge-warning">Tidak Bertemu</span>' : ($row->status == 1 ? '<br><span class="badge badge-primary">Bertemu</span>' : '') ?></td>
                    <td style="white-space: nowrap;"><?php echo ($row->gender == 0 ? 'Bpk. ' : 'Ibu ') . $row->name ?></td>
                    <td><?php echo $row->organization ?></td>
                    <td><?php echo $row->address ?: '' ?><?php $row->address && $row->phone_number ? '<br/>' : '' ?><?php echo $row->phone_number ? "<div><span style='white-space: nowrap;'><i class='fa fa-whatsapp'></i>&nbsp;$row->phone_number</span></div>" : '' ?><?php echo $row->email ? "<br/><span style='white-space: nowrap;'><i class='fa fa-at'></i>&nbsp;$row->email</span>" : '' ?></td>
                    <td align="center">
                        <p class="msg-text"><?php echo $row->purpose ?></p>
                    </td>
                    <td>
                        <p class="msg-text"><?php echo $row->message ?></p>
                    </td>
                    <td align="center"><?php echo $row->guest_count ?> orang</td>
                    <td align="center" style="white-space: nowrap;">
                        <?php echo $row->photo ? anchor(base_url("etamu/photo/{$row->id}"), '<i class="fa fa-user-circle-o"></i>', [
                            'title'    => 'Lihat Foto',
                            'class'    => 'btn btn-xs btn-outline-primary btn-modal',
                        ]) : '' ?>
                        <?php echo anchor(base_url("etamu/update/{$row->id}"), '<i class="fa fa-pencil"></i>', [
                            'title'    => 'Perbarui',
                            'class'    => 'btn btn-xs btn-outline-warning btn-modal',
                        ]) ?>
                        <?php echo anchor_confirm(base_url("etamu/delete/{$row->id}"), '<i class="fa fa-trash"></i>', [
                            'title'    => 'Hapus',
                            'class'    => 'btn btn-xs btn-outline-danger mr-2',
                            'data-confirm-message' => "Anda yakin akan menghapus data tamu {$row->name}?",
                        ]) ?>
                    </td>
                </tr>
            <?php endforeach ?>
        <?php else : ?>
            <tr>
                <td colspan="9">Belum ada data tamu</td>
            </tr>
        <?php endif ?>
    </tbody>
</table>

<?php echo $this->pagination->create_links() ?>

<script src="<?php echo base_url('assets/js/readmore.js') ?>"></script>

<script>
    new Readmore('.msg-text', {
        collapsedHeight: 100,
        speed: 500,
        lessLink: '<a href="#" class="text-warning mt-1">Sembunyikan</a>',
        moreLink: '<a href="#" class="text-info mt-1">Tampilkan</a>',
        afterToggle: function(trigger, element, expanded) {
            if (!expanded) { // The "Close" link was clicked
                window.scrollTo({
                    top: element.offsetTop,
                    behavior: 'smooth'
                })
            }
            // console.log('afterToggle called', arguments);
        },
        beforeToggle: function(trigger, element, expanded) {
            // console.log('beforeToggle called', arguments);
        }
    });
</script>