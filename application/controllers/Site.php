<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Site extends Notif_Controller
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
			'view' => 'site/index',
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

		$this->load->view('layout');
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

		return $this->viewAjax('widgets/form', [
			'message' => $this->vars['message'],
		]);
	}

	function photo($id)
	{
		$this->vars = [
			'main_body' => 'layout_content',
			'title' => 'Foto Tamu',
			'guest' => $this->guests->findOne($id)
		];

		$this->load->vars($this->vars);

		return $this->viewAjax('site/photo');
	}

	function delete($id)
	{
		$guest = $this->guests->findOne($id);
		$success = $this->guests->delete($id);

		if ($success) {
			delete_file('foto_tamu', $guest->photo);
		}

		return $this->redirectAjax([
			'redirect' => base_url('site/history'),
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
			'base_url' => base_url('site/history'),
			'total_rows' => $this->guests->num_rows($where),
			'per_page' => 20,
		]);

		$this->vars = [
			'main_body' => 'layout_content',
			'view' => 'site/history',
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

	private function _prepare_form($guestId = null, $fromHomepage = true)
	{
		$this->form_validation->set_rules('photo', 'Foto', 'trim');
		$this->form_validation->set_rules('person_to_meet', 'Yang Dituju', 'trim|required');
		$this->form_validation->set_rules('name', 'Nama Tamu', 'trim|required');
		$this->form_validation->set_rules('gender', 'Jenis Kelamin', 'trim|required');
		$this->form_validation->set_rules('email', 'Email', 'trim');
		$this->form_validation->set_rules('phone_number', 'No Handphone', 'trim');
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
			if (!WA_TEST_TARGET) {
				$this->vars['message'] = 'Variabel WA_TEST_TARGET belum diset pada tabel configs';
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
						'email' => $this->input->post('email'),
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

							if (SEND_NOTIFICATION) {
								// $this->send_notif($id);
								$ch = curl_init(base_url("site/send_notif/$id"));
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($ch, CURLOPT_TIMEOUT, 1);
								curl_exec($ch);
								curl_close($ch);
							}
						}

						return [
							'redirect' => base_url($fromHomepage ? 'site' : 'site/history'),
							'status' => true,
							'message' => "Berhasil menyimpan data tamu",
						];
					}

					$this->vars['message'] = 'Terjadi Kesalahan';
				}

				$this->vars['message'] = my_validation_errors();
			}
		}

		$guest = $guestId ? $this->guests->findOne($guestId) : null;

		$persons = [null => 'Pilih yang dituju'];
		foreach ($this->persons->find() as $obj) {
			$persons[$obj->id] = $obj->person;
		}

		$this->vars['form']['photo'] = [
			'type' => 'camera',
			'name' => 'photo',
			'label' => 'Rekam Wajah',
			'value' => $this->form_validation->set_value('photo', $guest && $guest->photo ? FOLDER_ROOT_UPLOAD . "foto_tamu/$guest->photo" : null),
			'class' => $guestId ? 'update' : 'create'
		];

		if (!empty($guestId)) {
			$this->vars['form']['visit_date'] = [
				'id' => 'date-sidang',
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

		$this->vars['form']['email'] = [
			'name' => 'email',
			'type' => 'email',
			'icon'  => 'at',
			'placeholder' => 'Email',
			'value' => $this->form_validation->set_value('email', $guest ? $guest->email : null),
		];

		$this->vars['form']['phone'] = [
			'icon'  => 'phone',
			'name'  => 'phone',
			'placeholder' => 'No Handpone',
			'value' => $this->form_validation->set_value('phone', $guest ? $guest->phone_number : null),
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
			'value' => $this->form_validation->set_value('message', $guest ? $guest->message : null),
		];

		$this->vars['form']['guest_count'] = [
			'name' => 'guest_count',
			'placeholder' => 'Jumlah Tamu',
			'type' => 'number',
			'value' => $this->form_validation->set_value('guest_count', $guest ? $guest->guest_count : null),
		];

		$this->vars['form']['status'] = [
			'type' => 'form_dropdown',
			'name' => 'status',
			'label' => 'Bertemu/Tidak Bertemu',
			'options' => [null => 'Pilih Status', 2 => 'Bertemu', 1 => 'Tidak Bertemu'],
			'selected' => $this->form_validation->set_value('status', $guest ? $guest->status : null),
			'visible' => !$fromHomepage
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

	function send_notif($id)
	{
		if (($guest = $this->guests->findOne($id))) {
			foreach ((is_development() ? cleansePhoneNumbers(WA_TEST_TARGET) : cleansePhoneNumbers($guest->phone_pegawai . ',' . $guest->phone_tamu . ',' . WA_TEST_TARGET)) as $no) {
				$data = [
					'type' => 'eTamu',
					'text' => get_template($guest),
					'sent_by' => 'system',
				];

				$send = send_wa(
					$no,
					$data['text'],
					file_path('foto_tamu', $guest->photo)
				);

				$sentTime = isset($send[$no]['sent_time']) ? $send[$no]['sent_time'] : date('Y-m-d H:i:s');

				$statusCode = 0;
				$message = '';
				if (isset($send[$no]['status'])) {
					$message = $send[$no]['message'];
					$statusCode = $send[$no]['status'] == 200;
				} else if (isset($send['status'])) {
					$message = isset($send['response']) ? $send['response'] : $send['message'];
					$statusCode = $send['status'];
				}

				$this->whatsapp->insert(array_merge($data, [
					'phone_number' => $no,
					'sent_time' => $sentTime,
					'success' => $statusCode,
					'callback' => isset($send[$no]['reference']) ?  $send[$no]['reference'] : '',
					'note' => $message,
				]));
			}
		}
	}
}
