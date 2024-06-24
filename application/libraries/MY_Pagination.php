<?php

defined('BASEPATH') or exit('No direct script access allowed');

class MY_Pagination extends CI_Pagination
{
    protected $total_rows = 0;
    protected $offset;
    protected $first_page = 1;
    protected $cur_page_segment = 3;
    protected $pagination_class = "pagination-page";

    public function __construct($params = array())
    {
        parent::__construct($params);
        $this->per_page = 50;
    }

    public function get_total_rows()
    {
        return $this->total_rows;
    }

    public function get_summary()
    {
        if (!$this->total_rows) {
            return '<div class="d-flex justify-content-end align-items-end small">Tidak ada data</div>';
        }
        $initRow = $this->offset + 1;
        $endRow = min($this->offset + $this->per_page, $this->total_rows);
        return "<div class='d-flex justify-content-end align-items-end small pagination-summary'>Menampilkan {$initRow} - {$endRow} dari {$this->total_rows}</div>";
    }

    public function set($config)
    {
        //logic untuk handle pagination pada filtering & crud
        $CI = get_instance();
        if (strpos(current_url(), $config['base_url']) !== false || strpos(current_url() . '/index', $config['base_url']) !== false) {
            $config['cur_page'] = $CI->uri->segment(isset($config['cur_page_segment']) ? $config['cur_page_segment'] : $this->cur_page_segment);
        } else {
            $config['cur_page'] = $CI->session->flashdata(removeSpecialChars($config['base_url']));
        }

        $config['cur_page'] = $config['cur_page'] ?: $this->first_page;
        $CI->session->set_flashdata(removeSpecialChars($config['base_url']), $config['cur_page']);

        $config['first_url'] = $config['base_url'];
        $config['per_page'] = isset($config['per_page']) ? $config['per_page'] : $this->per_page;
        $config['offset'] = max(0, ($config['cur_page'] ? is_int(intval($config['cur_page'])) : 0) - 1) * $config['per_page'];
        $config['num_links'] = floor($config['total_rows'] / $config['per_page']);
        $config['use_page_numbers'] = true;
        $config['reuse_query_string'] = true;
        $config['full_tag_open'] = '<ul class="pagination ' . (isset($config['pagination_class']) ? $config['pagination_class'] : $this->pagination_class) . '">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = false;
        $config['last_link'] = false;
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo';
        $config['prev_tag_open'] = '<li class="page-item prev">';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '&raquo';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><span>';
        $config['cur_tag_close'] = '</span></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';

        $this->initialize($config);

        return $config;
    }
}
