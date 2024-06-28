<?php

setlocale(LC_TIME, 'id_ID');
date_default_timezone_set("Asia/Jakarta");

class Core_Controller extends CI_Controller
{
    protected $types;
    protected $typesText;

    public $vars;

    public function __construct()
    {
        parent::__construct();
    }

    protected function set_content_type($data)
    {
        return $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    protected function viewAjax($view, $data = [])
    {
        if ($this->input->is_ajax_request()) {
            if (isset($this->vars['title'])) {
                $data['title'] = strtoupper($this->vars['title']);
            } else if (isset($data['title'])) {
                $data['title'] = strtoupper($data['title']);
            } else {
                $data['title'] = strtoupper(APP_SHORT_NAME);
            }

            return $this->set_content_type(array_merge($data, [
                'content' => $this->load->view($view, $data, true),
                'csrf_token_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash(),
            ]));
        }
    }

    protected function redirectAjax($data = [])
    {
        if ($this->input->is_ajax_request()) {
            return $this->set_content_type($data);
        }
    }

    public function check_gateway()
    {
        if (!DIALOGWA_API_URL || !DIALOGWA_SESSION || !DIALOGWA_TOKEN) return $this->set_content_type([
            'status' => false,
            'message' => 'DIALOGWA_API_URL, DIALOGWA_SESSION, dan DIALOGWA_TOKEN harus diset pada tabel configs'
        ]);

        $result = hit_api(DIALOGWA_API_URL . '/session/' . DIALOGWA_SESSION, 'get', null, DIALOGWA_TOKEN);
        if (!$result['status']) {
            $response = json_decode($result['response'], 1);
            $result['message'] = (isset($response['message']) ? $response['message'] : 'Cek url gateway') . ' | ' . DIALOGWA_API_URL . ' | ' . DIALOGWA_SESSION . ' | ' .
                substr(DIALOGWA_TOKEN, 0, 10) . '.....' . substr(DIALOGWA_TOKEN, -10);
        }
        return $this->set_content_type($result);
    }
}

class Notif_Controller extends Core_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Guests_Model', 'guests');
    }

    function next()
    {
        $type = $this->uri->segment(3) ?: 'antrian';
        $this->vars = [
            'allTypes' => $this->types,
            'selectedType' => $type,
            'main_body' => 'layout_content',
            'view' => 'notif/index',
            'data' => [],
            // 'data' => $this->_prepareData($type),
        ];

        $this->load->vars($this->vars);

        if ($this->input->is_ajax_request()) {
            return $this->viewAjax('layout_content', [
                'title' => 'Notifikasi Selanjutnya',
            ]);
        }

        $this->load->view('layout');
    }

    function notif()
    {
        $type = $this->uri->segment(3) ?: 'antrian';
        $this->vars = $this->_prepareData($type);

        $this->load->vars($this->vars);

        if ($this->input->is_ajax_request()) {
            $view = in_array($type, ['antrian', 'sidang']) ? 'antrian_sidang' : $type;
            return $this->viewAjax("notif/$view", [
                'title' => 'Notifikasi Selanjutnya',
            ]);
        }

        $this->load->view('layout');
    }

    function view_notif()
    {
        if (!$this->uri->segment(3) || !$this->uri->segment(4)) {
            return;
        }

        $this->load->model('Guests_Model', 'guests');
        $where = ['where' => [
            [
                'perkara_id' => $this->uri->segment(3),
                'phone_number' => ENVIRONMENT == 'development' ? WA_TEST_TARGET_ETAMU : $this->uri->segment(4),
            ]
        ]];
        $config = $this->pagination->set([
            'base_url' => base_url('whatsapp/index'),
            'total_rows' => $this->guests->num_rows($where),
            'per_page' => 10,
        ]);

        $data = $this->guests->find($where, $config['offset'], $config['per_page']);

        $this->vars = [
            'main_body' => 'notif/riwayat',
            'data' => $data,
            'offset' => $config['offset'] + 1,
            'selectedId' => $this->uri->segment(5),
        ];

        $this->load->vars($this->vars);

        if ($this->input->is_ajax_request()) {
            return $this->viewAjax("notif/riwayat", [
                'size' => 'modal-lg',
                'title' => 'Riwayat Notifikasi',
            ]);
        }

        $this->load->view('layout');
    }

    // function _prepareData($type)
    // {
    //     $where = get_notif_criteria($type);
    //     switch ($type) {
    //         case 'antrian':
    //         case 'sidang':
    //             $view = 'notif/antrian_sidang';
    //             $count = $this->sipp->num_sidangs($where);
    //             $data = $this->sipp->find_sidangs($where);
    //             // $count = $this->sipp->num_sidangs($where);
    //             // $data = $this->sipp->find_sidangs($where);
    //             break;
    //         case 'calendar':
    //             $view = 'notif/calendar';
    //             $count = $this->sipp->num_court_calendar($where);
    //             $data = $this->sipp->find_court_calendar($where);
    //             break;
    //         case 'jurnal':
    //             $view = 'notif/jurnal';
    //             $count = $this->sipp->num_sisa_panjar($where);
    //             $data = $this->sipp->find_sisa_panjar($where);
    //             break;
    //         case 'ac':
    //             $view = 'notif/ac';
    //             $count = $this->sipp->num_ac($where);
    //             $data = $this->sipp->find_ac($where);
    //             break;
    //     }

    //     $paginationVar = "pagination_$type";
    //     $this->load->library('pagination', null, $paginationVar);

    //     $config = $this->$paginationVar->set([
    //         'base_url' => base_url("whatsapp/notif/$type"),
    //         'total_rows' => $count,
    //     ]);

    //     return [
    //         'main_body' => "notif/$view",
    //         'offset' => $config['offset'] + 1,
    //         'list' => $data,
    //         'paginationVar' => $paginationVar,
    //         'selectedType' => $type,
    //     ];
    // }
}
