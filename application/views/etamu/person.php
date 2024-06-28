 <?php echo anchor("etamu/person_save", '<span class="fa fa-plus" aria-hidden="true"></span> Tambah', [
        'class' => 'btn btn-xs btn-outline-primary btn-modal',
    ]) ?>

 <?php echo $this->pagination->get_summary() ?>

 <table class="table table-hover table-sticky">
     <thead>
         <tr>
             <th>No.</th>
             <th>Person</th>
             <th>Jenis Kelamin</th>
             <th>No Handphone</th>
             <th>Urutan</th>
             <th></th>
         </tr>
     </thead>
     <tbody>
         <?php if ($data) : ?>
             <?php foreach ($data as $i => $row) : ?>
                 <tr lass="bg-default">
                     <td><?php echo $i + $offset ?></td>
                     <td><?php echo $row->person ?></td>
                     <td><?php echo $row->gender == 'L' ? 'Laki-Laki' : ($row->gender == 'P' ? 'Perempuan' : '-') ?></td>
                     <td><?php echo $row->phone ?></td>
                     <td><?php echo $row->order ?></td>
                     <td align="center" style="white-space: nowrap;">
                         <?php echo anchor(base_url("etamu/person_save/{$row->id}"), '<i class="fa fa-pencil"></i>', [
                                'title'    => 'Perbarui',
                                'class'    => 'btn btn-xs btn-outline-warning btn-modal',
                            ]) ?>
                         <?php echo anchor_confirm(base_url("etamu/person_delete/{$row->id}"), '<i class="fa fa-trash"></i>', [
                                'title'    => 'Hapus',
                                'class'    => 'btn btn-xs btn-outline-danger mr-2',
                                'data-confirm-message' => "Anda yakin akan menghapus data {$row->person}?",
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