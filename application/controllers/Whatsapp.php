<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Whatsapp extends Notif_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Whatsapp_Model', 'whatsapp');
	}

	public function index()
	{
		$date = $this->input->get('tanggal') ?: null;
		$type = $this->input->get('tipe') ?: null;
		$teks = $this->input->get('teks') ?: null;

		$where = ['where' => []];

		if ($date) {
			$where['where'][] = ['DATE(sent_time)' => $date];
		}
		if ($type) {
			$where['where'][] = ['type' => $type];
		}
		if ($teks) {
			$where['where'][] = ['text LIKE "%' . $teks . '%"' => NULL];
		}

		$config = $this->pagination->set([
			'base_url' => base_url('whatsapp/index'),
			'total_rows' => $this->whatsapp->num_rows($where),
			'per_page' => 20,
		]);

		$this->vars = [
			'type' => $type,
			'main_body' => 'layout_content',
			'view' => 'whatsapp/index',
			'wa' => $this->whatsapp->find($where, $config['offset'], $config['per_page']),
			'title' => 'Notifikasi Whatsapp',
			'offset' => $config['offset'] + 1,
		];

		$this->vars['selectedDate'] = $date;
		$this->vars['form']['tanggal'] = [
			'id' => 'date-antrian',
			'type' => 'form_datepicker',
			'name' => 'tanggal',
			'value' => $this->form_validation->set_value('tanggal', $date),
			'placeholder' => 'Tanggal Kirim',
		];

		$this->vars['form']['tipe'] = [
			'type' => 'form_dropdown',
			'name' => 'tipe',
			'options' => [null => 'Pilih Tipe Notif', 'Internal' => 'Internal', 'Eksternal' => 'Eksternal'],
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
}
