<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Whatsapp_Model extends CI_Model
{
	function findOne($id)
	{
		$this->db->from(TBL_WHATSAPP);
		$this->db->where(['id' => $id]);

		if (($row = $this->db->get()->row())) {
			return $row;
		}

		$this->session->set_flashdata('error_message', 'Data jabatan tidak ditemukan #' . $id);
		return redirect('site/error');
	}

	function find($where = [], $offset = null, $limit = null)
	{
		$this->db->from(TBL_WHATSAPP);

		$this->_populateWhere($where);

		if ($limit) {
			$this->db->limit($limit);
		}

		if ($offset) {
			$this->db->offset($offset);
		}

		return $this->db->order_by('sent_time DESC')->get()->result();
	}

	function insert($data)
	{
		//TODO sent whatsapp here
		return $this->db->insert(TBL_WHATSAPP, $data);
	}

	function update($id, $data)
	{
		return $this->db->where('id', $id)->update(TBL_WHATSAPP, $data);
	}

	function delete($id)
	{
		$this->db->delete(TBL_WHATSAPP, array('id' => $id));
		return $this->db->affected_rows() > 0;
	}

	function num_rows($where = [])
	{
		$this->db->from(TBL_WHATSAPP);

		$this->_populateWhere($where);

		return $this->db->get()->num_rows();
	}

	private function _populateWhere($where)
	{
		foreach ($where as $key => $q) {
			switch ($key) {
				case 'like':
					if (is_array($q) && !empty($q)) {
						for ($i = 0; $i < count($q); $i++) {
							if ($i == 0) {
								$this->db->like($q[$i]);
							} else {
								$this->db->or_like($q[$i]);
							}
						}
					}
					break;
				default:
					if (is_array($q) && !empty($q)) {
						foreach ($q as $w) {
							$this->db->where($w);
						}
					}
					break;
			}
		}
	}
}
