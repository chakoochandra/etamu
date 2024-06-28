<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Whatsapp_Model', 'whatsapp');
	}

	function send_notif_etamu($id)
	{
		$auth = $this->_authenticate();
		if ($auth && $auth['status'] !== 200) {
			return $auth;
		}

		$this->load->model('Guests_Model', 'guests');

		if (($guest = $this->guests->findOne($id))) {
			foreach ((is_development() ? cleansePhoneNumbers(WA_TEST_TARGET_ETAMU) : cleansePhoneNumbers($guest->phone_pegawai . ',' . $guest->phone_tamu . ',' . WA_TEST_TARGET_ETAMU)) as $no) {
				$data = [
					'type' => 'eTamu',
					'text' => $this->_template_etamu($guest),
					'sent_by' => 'system',
				];

				$send = send_wa(
					$no,
					$data['text'],
					60,
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

	private function _authenticate()
	{
		if (!WA_TEST_TARGET) {
			return [
				'status' => 405,
				'error' => 'Variabel WA_TEST_TARGET belum diset pada tabel configs'
			];
		}
		if (!DIALOGWA_API_URL) {
			return [
				'status' => 405,
				'error' => 'Variabel DIALOGWA_API_URL belum diset pada tabel configs'
			];
		}
		if (!DIALOGWA_TOKEN) {
			return [
				'status' => 405,
				'error' => 'Variabel DIALOGWA_TOKEN belum diset pada tabel configs. Token didapat dari <strong><a href="https://dialogwa.id/#paket-0" target="blank">https://dialogwa.id</a></strong>'
			];
		}
		if (!DIALOGWA_SESSION) {
			return [
				'status' => 405,
				'error' => 'Variabel DIALOGWA_SESSION belum diset pada tabel configs. Buat sesi di <strong><a href="https://dialogwa.id/#paket-0" target="blank">https://dialogwa.id</a></strong>'
			];
		}

		$headers = $this->input->request_headers();
		if (!isset($headers['Authorization'])) {
			return [
				'status' => 401,
				'error' => 'Tidak memiliki akses!'
			];
		}

		$authorizationHeader = $headers['Authorization'];
		if (!preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)) {
			return [
				'status' => 401,
				'error' => 'Tidak memiliki akses!'
			];
		}

		if ($matches[1] != DIALOGWA_TOKEN) {
			return [
				'status' => 401,
				'error' => 'Tidak memiliki akses!'
			];
		}
	}

	private function _template_etamu($guest)
	{
		return '🏛️ *INFORMASI eTAMU ' . strtoupper(SATKER_NAME) . '*

Informasi tamu untuk *' . ($guest->person_gender == 'L' ? 'Bpk. ' : ($guest->person_gender == 'P' ? 'Ibu ' : '')) . $guest->person . '* pada ' . formatDate($guest->visit_date, "%A, %d/%m/%y %H:%M") . '.

*Identitas Tamu*
*' . ($guest->gender == 0 ? 'Bpk. ' : ($guest->gender == 1 ? 'Ibu ' : '')) . $guest->name . '* ' . ($guest->organization ? "($guest->organization)" : '') . ($guest->phone_number || $guest->email ? '
' . ($guest->phone_number . ($guest->phone_number && $guest->email ? ' / ' : '') . $guest->email) : '') . ($guest->address ? '
' . $guest->address : '') . '

*Jumlah Tamu*
' . $guest->guest_count . ' orang

*Keperluan*
' . ($guest->purpose ?: '-') . '

*Pesan*
_' . ($guest->message ?: '-') . '_

ℹ️ _*Pesan ini dikirim oleh sistem secara otomatis. Balas OK untuk informasi lebih lanjut*_';
	}
}
