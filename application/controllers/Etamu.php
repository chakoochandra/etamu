<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Etamu extends Core_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Guests_Model', 'guests');
        $this->load->model('Persons_Model', 'persons');
        $this->load->model('Whatsapp_Model', 'whatsapp');
    }

    public function index()
    {
        $this->vars = [
            'main_body' => 'layout_content',
            'view' => 'etamu/index',
            'title' => 'Form Tamu',
            'latest' => $this->guests->findLatest(),
        ];

        $result = $this->_prepare_form();
        if ($result && $result['status']) {
            if ($this->input->is_ajax_request()) {
                return $this->redirectAjax($result);
            }
        }

        $this->load->vars($this->vars);

        if ($this->input->is_ajax_request()) {
            return $this->viewAjax('layout_content');
        }

        $this->load->view('layout_no_sidebar');
    }

    function update($id, $fromHomepage = false)
    {
        $this->vars = [
            'main_body' => 'layout_content',
            'title' => 'Perbarui Data Tamu',
            'message' => ''
        ];

        $result = $this->_prepare_form($id, $fromHomepage);
        if ($result && $result['status']) {
            if ($this->input->is_ajax_request()) {
                return $this->redirectAjax($result);
            }
        }

        $this->load->vars($this->vars);

        return $this->viewAjax('etamu/index', [
            'message' => $this->vars['message'],
            'size' => 'modal-lg'
        ]);
    }

    function delete($id)
    {
        $guest = $this->guests->findOne($id);
        $success = $this->guests->delete($id);

        if ($success) {
            delete_file('foto_tamu', $guest->photo);
        }

        return $this->redirectAjax([
            'redirect' => base_url('etamu/history'),
            'status' => true,
            'message' => ($success ? 'Berhasil' : 'Gagal') . ' menghapus data tamu',
        ]);
    }

    public function history()
    {
        $date = $this->input->get('tanggal') ?: null;
        $type = $this->input->get('tipe') ?: null;
        $teks = $this->input->get('teks') ?: null;

        $where = ['where' => []];

        if ($date) {
            $where['where'][] = ['DATE(visit_date)' => $date];
        }
        if ($type) {
            $where['where'][] = ['person_to_meet' => $type];
        }
        if ($teks) {
            $where['where'][] = ['name LIKE "%' . $teks . '%" OR organization LIKE "%' . $teks . '%" OR address LIKE "%' . $teks . '%" OR purpose LIKE "%' . $teks . '%" OR message LIKE "%' . $teks . '%"' => NULL];
        }

        $config = $this->pagination->set([
            'base_url' => base_url('etamu/history'),
            'total_rows' => $this->guests->num_rows($where),
            'per_page' => 20,
        ]);

        $this->vars = [
            'main_body' => 'layout_content',
            'view' => 'etamu/history',
            'data' => $this->guests->find($where, $config['offset'], $config['per_page']),
            'title' => 'Riwayat Tamu',
            'offset' => $config['offset'] + 1,
        ];

        $this->vars['selectedDate'] = $date;
        $this->vars['form']['tanggal'] = [
            'id' => 'date-antrian',
            'type' => 'form_datepicker',
            'name' => 'tanggal',
            'value' => $this->form_validation->set_value('tanggal', $date),
            'placeholder' => 'Tanggal Bertamu',
        ];

        $persons = [null => 'Pilih yang dituju'];
        foreach ($this->persons->find() as $obj) {
            $persons[$obj->id] = $obj->person;
        }
        $this->vars['form']['tipe'] = [
            'type' => 'form_dropdown',
            'name' => 'tipe',
            'options' => $persons,
            'selected' => $this->input->get('tipe'),
        ];

        $this->vars['form']['teks'] = [
            'name' => 'teks',
            'placeholder' => 'Cari teks',
            'value' => $this->input->get('teks'),
        ];

        $this->load->vars($this->vars);

        if ($this->input->is_ajax_request()) {
            return $this->viewAjax('layout_content');
        }

        $this->load->view('layout');
    }

    function photo($id)
    {
        $this->vars = [
            'main_body' => 'layout_content',
            'title' => 'Foto Tamu',
            'guest' => $this->guests->findOne($id)
        ];

        $this->load->vars($this->vars);

        return $this->viewAjax('etamu/photo');
    }

    public function person()
    {
        if (!$this->input->is_ajax_request() && !$this->ion_auth->logged_in()) {
            redirect('site/login', 'refresh');
        }

        $where = ['where' => []];

        $config = $this->pagination->set([
            'base_url' => base_url('etamu/person'),
            'total_rows' => $this->persons->num_rows($where),
            'per_page' => 20,
        ]);

        $this->vars = [
            'main_body' => 'layout_content',
            'view' => 'etamu/person',
            'data' => $this->persons->find($where, $config['offset'], $config['per_page']),
            'title' => 'Daftar Person',
            'offset' => $config['offset'] + 1,
        ];

        $this->load->vars($this->vars);

        if ($this->input->is_ajax_request()) {
            return $this->viewAjax('layout_content');
        }

        $this->load->view('layout');
    }

    function person_save($id = null)
    {
        $this->vars = [
            'main_body' => 'layout_content',
            'title' => 'Simpan Data Person',
            'message' => ''
        ];

        $result = $this->_prepare_form_person($id);
        if ($result && $result['status']) {
            if ($this->input->is_ajax_request()) {
                return $this->redirectAjax($result);
            }
        }

        $this->load->vars($this->vars);

        return $this->viewAjax('widgets/form', [
            'message' => $this->vars['message'],
        ]);
    }

    function person_delete($id)
    {
        $person = $this->persons->findOne($id);
        $success = $this->persons->delete($id);

        return $this->redirectAjax([
            'redirect' => base_url('etamu/person'),
            'status' => true,
            'message' => ($success ? 'Berhasil' : 'Gagal') . ' menghapus data ' . $person->person,
        ]);
    }

    private function _prepare_form_person($id)
    {
        $this->form_validation->set_rules('person', 'Person', 'trim|required');
        $this->form_validation->set_rules('gender', 'Jenis Kelamin', 'trim|required');
        $this->form_validation->set_rules('phone', 'No Handphone/Whatsapp', 'trim');


        if (isset($_POST) && !empty($_POST)) {
            if ($this->form_validation->run() === TRUE) {
                $data = [
                    'person' => $this->input->post('person'),
                    'gender' => $this->input->post('gender'),
                    'phone' => $this->input->post('phone'),
                    'order' => $this->input->post('order'),
                ];

                if ($id ? $this->persons->update($id, $data) : $this->persons->insert($data)) {
                    return [
                        'redirect' => base_url('etamu/person'),
                        'status' => true,
                        'message' => "Berhasil menyimpan data {$data['person']}",
                    ];
                }

                $this->vars['message'] = 'Terjadi Kesalahan';
            }

            $this->vars['message'] = my_validation_errors();
        }

        $person = $id ? $this->persons->findOne($id) : null;

        $this->vars['form']['person'] = [
            'name' => 'person',
            'placeholder' => 'Jabatan / Nama',
            'value' => $this->form_validation->set_value('person', $person ? $person->person : null),
        ];

        $this->vars['form']['gender'] = [
            'type' => 'form_toggler',
            'name' => 'gender',
            'label' => 'Jenis Kelamin',
            'options' => ['L' => 'Laki-laki', 'P' => 'Perempuan'],
            'btnWidth' => [130, 130],
            'value' => $this->form_validation->set_value('gender', $person ? $person->gender : null),
            'divClass' => 'justify-content-left',
        ];

        $this->vars['form']['phone'] = [
            'icon'  => 'phone',
            'name'  => 'phone',
            'placeholder' => 'No Handpone/Whatsapp',
            'value' => $this->form_validation->set_value('phone', $person ? $person->phone : null),
        ];
        $this->vars['form']['form_info'] = [
            'type' => 'form_info',
            'info' => SEND_NOTIFICATION ? 'Yang bersangkutan akan menerima informasi register tamu pada nomor Whatsapp yang diinputkan' : '',
        ];

        $this->vars['form']['order'] = [
            'name' => 'order',
            'placeholder' => 'Urutan',
            'type' => 'number',
            'value' => $this->form_validation->set_value('order', $person ? $person->order : null),
        ];
    }

    private function _prepare_form($guestId = null, $fromHomepage = true)
    {
        $this->form_validation->set_rules('photo', 'Foto', 'trim');
        $this->form_validation->set_rules('person_to_meet', 'Yang Dituju', 'trim|required');
        $this->form_validation->set_rules('name', 'Nama Tamu', 'trim|required');
        $this->form_validation->set_rules('gender', 'Jenis Kelamin', 'trim|required');
        // $this->form_validation->set_rules('email', 'Email', 'trim');
        $this->form_validation->set_rules('phone_number', 'No Handphone/Whatsapp', 'trim');
        $this->form_validation->set_rules('organization', 'Instansi/Lembaga/Perusahaan', 'trim');
        $this->form_validation->set_rules('address', 'Alamat', 'trim');
        $this->form_validation->set_rules('purpose', 'Tujuan Bertamu', 'trim');
        $this->form_validation->set_rules('message', 'Pesan', 'trim');
        $this->form_validation->set_rules('guest_count', 'Jumlah Tamu', 'trim|required');

        if ($guestId) {
            $this->form_validation->set_rules('visit_date', 'Waktu Bertamu', 'trim|required');
        }

        if (!$fromHomepage) {
            $this->form_validation->set_rules('status', 'Bertemu/Tidak Bertemu', 'trim|required');
        }

        if (isset($_POST) && !empty($_POST)) {
            $this->vars['message'] = '';
            if (!WA_TEST_TARGET_ETAMU) {
                $this->vars['message'] = 'Variabel WA_TEST_TARGET_ETAMU belum diset pada tabel configs';
            }
            if (!DIALOGWA_API_URL) {
                $this->vars['message'] = 'Variabel DIALOGWA_API_URL belum diset pada tabel configs';
            }
            if (!DIALOGWA_TOKEN) {
                $this->vars['message'] = 'Variabel DIALOGWA_TOKEN belum diset pada tabel configs. Token didapat dari <strong><a href="https://dialogwa.id/#paket-0" target="blank">https://dialogwa.id</a></strong>';
            }
            if (!DIALOGWA_SESSION) {
                $this->vars['message'] = 'Variabel DIALOGWA_SESSION belum diset pada tabel configs. Buat sesi di <strong><a href="https://dialogwa.id/#paket-0" target="blank">https://dialogwa.id</a></strong>';
            }

            if (!$this->vars['message']) {
                if ($this->form_validation->run() === TRUE) {
                    $data = [
                        'visit_date' => $this->input->post('visit_date') ?: date('Y-m-d H:i:s'),
                        'person_to_meet' => $this->input->post('person_to_meet'),
                        'name' => $this->input->post('name'),
                        'gender' => $this->input->post('gender'),
                        // 'email' => $this->input->post('email'),
                        'phone_number' => $this->input->post('phone_number'),
                        'organization' => $this->input->post('organization'),
                        'address' => $this->input->post('address'),
                        'purpose' => $this->input->post('purpose'),
                        'message' => $this->input->post('message'),
                        'guest_count' => $this->input->post('guest_count'),
                        'status' => $this->input->post('status'),
                    ];

                    $filename = time() . ".png";
                    if ($this->input->post('photo') && file_put_contents(FOLDER_ROOT_UPLOAD . "foto_tamu/$filename", base64_decode(str_replace('data:image/png;base64,', '', $this->input->post('photo'))))) {
                        $data['photo'] = $filename;
                    }

                    if ($guestId ? $this->guests->update($guestId, $data) : $this->guests->insert($data)) {
                        if ($fromHomepage) {
                            $id = $guestId ?: $this->db->insert_id();

                            if (!$guestId && SEND_NOTIFICATION) {
                                hit_api(base_url("api/send_notif_etamu/$id"), 'GET', null, DIALOGWA_TOKEN);
                                // hit_api_async(base_url("api/send_notif_etamu/$id"), 'GET', null, DIALOGWA_TOKEN);
                            }
                        }

                        return [
                            'redirect' => base_url($fromHomepage ? 'etamu' : 'etamu/history'),
                            'status' => true,
                            'message' => "Berhasil menyimpan data tamu",
                        ];
                    }

                    $this->vars['message'] = 'Terjadi Kesalahan';
                }

                $this->vars['message'] = my_validation_errors();
            }
        }

        $this->vars['form']['formClass'] = $guestId ? 'form-ajax' : 'form-create';
        $this->vars['form']['showBtnCloseModal'] = $guestId ? true : false;

        $guest = $guestId ? $this->guests->findOne($guestId) : null;

        $persons = [null => 'Pilih yang dituju'];
        foreach ($this->persons->find() as $obj) {
            $persons[$obj->id] = ($obj->gender == 'L' ? 'Bpk. ' : ($obj->gender == 'P' ? 'Ibu ' : '')) . $obj->person;
        }

        $this->vars['form']['photo'] = [
            'type' => 'form_camera',
            'name' => 'photo',
            'value' => $this->form_validation->set_value('photo', $guest && $guest->photo ? FOLDER_ROOT_UPLOAD . "foto_tamu/$guest->photo" : null),
            'class' => $guestId ? 'update' : 'create',
            'divClass' => 'container-camera',
            // 'visible' => false,
        ];

        if (!empty($guestId)) {
            $this->vars['form']['visit_date'] = [
                'type' => 'form_datetimepicker',
                'name' => 'visit_date',
                'value' => $this->form_validation->set_value('visit_date', $guest ? $guest->visit_date : null),
                'placeholder' => 'Tanggal Bertamu',
                'visible' => !empty($guestId)
            ];
        }

        $this->vars['form']['person_to_meet'] = [
            'type' => 'form_dropdown',
            'name' => 'person_to_meet',
            'label' => 'Yang Dituju',
            'options' => $persons,
            'selected' => $this->form_validation->set_value('person_to_meet', $guest ? $guest->person_to_meet : null),
        ];

        $this->vars['form']['name'] = [
            'name' => 'name',
            'placeholder' => 'Nama Tamu',
            'value' => $this->form_validation->set_value('name', $guest ? $guest->name : null),
        ];

        $this->vars['form']['gender'] = [
            'type' => 'form_toggler',
            'name' => 'gender',
            'label' => 'Jenis Kelamin',
            'options' => [0 => 'Laki-laki', 1 => 'Perempuan'],
            'btnWidth' => [130, 130],
            'value' => $this->form_validation->set_value('gender', $guest ? $guest->gender : null),
            'divClass' => 'justify-content-left',
        ];

        // $this->vars['form']['email'] = [
        //     'name' => 'email',
        //     'type' => 'email',
        //     'icon'  => 'at',
        //     'placeholder' => 'Email',
        //     'value' => $this->form_validation->set_value('email', $guest ? $guest->email : null),
        // ];

        $this->vars['form']['phone_number'] = [
            'icon'  => 'whatsapp',
            'name'  => 'phone_number',
            'placeholder' => 'No Handpone/Whatsapp',
            'value' => $this->form_validation->set_value('phone_number', $guest ? $guest->phone_number : null),
        ];
        $this->vars['form']['form_info'] = [
            'type' => 'form_info',
            'info' => SEND_NOTIFICATION ? 'Tamu akan menerima informasi register tamu pada nomor Whatsapp yang diinputkan' : '',
        ];

        $this->vars['form']['guest_count'] = [
            'name' => 'guest_count',
            'placeholder' => 'Jumlah Tamu',
            'type' => 'number',
            'value' => $this->form_validation->set_value('guest_count', $guest ? $guest->guest_count : null),
        ];

        $this->vars['form']['organization'] = [
            'name'  => 'organization',
            'placeholder' => 'Instansi/Lembaga/Perusahaan',
            'value' => $this->form_validation->set_value('organization', $guest ? $guest->organization : null),
        ];

        $this->vars['form']['address'] = [
            'name'  => 'address',
            'type'  => 'form_textarea',
            'placeholder' => 'Alamat',
            'value' => $this->form_validation->set_value('address', $guest ? $guest->address : null),
        ];

        $this->vars['form']['purpose'] = [
            'name'  => 'purpose',
            'type'  => 'form_textarea',
            'placeholder' => 'Tujuan Bertamu',
            'value' => $this->form_validation->set_value('purpose', $guest ? $guest->purpose : null),
        ];

        $this->vars['form']['message'] = [
            'name'  => 'message',
            'type'  => 'form_textarea',
            'placeholder' => 'Pesan',
            'rows' => '2',
            'value' => $this->form_validation->set_value('message', $guest ? $guest->message : null),
        ];

        $this->vars['form']['status'] = [
            'type' => 'form_toggler',
            'name' => 'status',
            'label' => 'Bertemu/Tidak Bertemu',
            'options' => [2 => 'Bertemu', 1 => 'Tidak Bertemu'],
            'btnWidth' => [130, 130],
            'value' => $this->form_validation->set_value('status', $guest ? $guest->status : null),
            'divClass' => 'justify-content-left',
            'visible' => !$fromHomepage
        ];
    }
}
