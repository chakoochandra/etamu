<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Configs_Model extends CI_Model
{
	function get_all()
	{
		return $this->db->from(TBL_CONFIGS)->order_by('category ASC, key ASC, value ASC')->get()->result();
	}

	function save($id, $fields)
	{
		return $this->db->where('id', $id)->update('configs', $fields);
	}
}
