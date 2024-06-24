<div class="d-flex">
    <?php echo filter_form($form, 'whatsapp') ?>
</div>

<?php echo $this->pagination->get_summary() ?>

<table class="table table-hover table-sticky">
    <thead>
        <tr>
            <th>No.</th>
            <th>Tanggal Kirim</th>
            <th>Nomor Tujuan</th>
            <th>Pengirim</th>
            <th>Tipe</th>
            <th>Status</th>
            <th>Pesan</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($wa as $i => $row) : ?>
            <tr <?php echo $row->success == 0 ? 'class="bg-danger"' : ($row->success == 2 ? 'class="bg-warning"' : 'class="bg-default"') ?>>
                <td><?php echo $i + $offset ?></td>
                <td align="center"><?php echo formatDate($row->sent_time, "%d/%m/%y %H:%M:%S") ?></td>
                <td><?php echo $row->phone_number ?></td>
                <td><span class="badge badge-<?php echo $row->sent_by == 'system' ? 'default' : 'primary' ?>"><?php echo $row->sent_by ?></span></td>
                <td align="left"><?php echo $row->type ?></td>
                <td><span class="badge badge-<?php echo $row->success == 0 ? 'danger' : ($row->success == 2 ? 'danger' : 'primary') ?>" data-toggle="tooltip" title="<?php echo $row->success == 1 ? '' : $row->note ?>"><?php echo $row->success == 1 ? 'Terkirim' : 'Tidak Terkirim' ?></span><br /><span class="text-sm font-italic"><?php echo $row->success == 1 ? '' : $row->note ?></span></td>
                <td align="left">
                    <p class="msg-text"><?php echo html_entity_decode($row->text, ENT_HTML5, 'UTF-8') ?></p>
                </td>
            </tr>
        <?php endforeach ?>
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