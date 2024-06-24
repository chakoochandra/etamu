<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Guests_Model extends CI_Model
{
	function findOne($id)
	{
		$this->db->select(TBL_GUESTS . '.*, ' . TBL_GUESTS . '.phone_number AS phone_tamu, ' . TBL_PERSONS . '.person, ' . TBL_PERSONS . '.phone AS phone_pegawai');
		$this->db->from(TBL_GUESTS);
		$this->db->join(TBL_PERSONS, TBL_PERSONS . '.id = person_to_meet', 'left');
		$this->db->where([TBL_GUESTS . '.id' => $id]);

		if (($row = $this->db->get()->row())) {
			return $row;
		}

		$this->session->set_flashdata('error_message', 'Data tamu tidak ditemukan #' . $id);
		return redirect('site/error');
	}

	function findLatest()
	{
		$this->db->select(TBL_GUESTS . '.*, ' . TBL_GUESTS . '.phone_number AS phone_tamu, ' . TBL_PERSONS . '.person, ' . TBL_PERSONS . '.phone AS phone_pegawai');
		$this->db->from(TBL_GUESTS);
		$this->db->join(TBL_PERSONS, TBL_PERSONS . '.id = person_to_meet', 'left');
		$this->db->where('visit_date <=', date('Y-m-d H:i:s'));
		$this->db->where('visit_date >=', date('Y-m-d H:i:s', strtotime('-30 minutes')));
		$this->db->order_by('visit_date', 'DESC');
		return $this->db->get()->row();
	}

	function find($where = [], $offset = null, $limit = null)
	{
		$this->db->select(TBL_GUESTS . '.*, ' . TBL_PERSONS . '.person');
		$this->db->from(TBL_GUESTS);
		$this->db->join(TBL_PERSONS, TBL_PERSONS . '.id = person_to_meet', 'left');

		$this->_populateWhere($where);

		if ($limit) {
			$this->db->limit($limit);
		}

		if ($offset) {
			$this->db->offset($offset);
		}

		return $this->db->order_by('visit_date DESC')->get()->result();
	}

	function insert($data)
	{
		return $this->db->insert(TBL_GUESTS, $data);
	}

	function update($id, $data)
	{
		return $this->db->where('id', $id)->update(TBL_GUESTS, $data);
	}

	function delete($id)
	{
		$this->db->delete(TBL_GUESTS, array('id' => $id));
		return $this->db->affected_rows() > 0;
	}

	function num_rows($where = [])
	{
		$this->db->from(TBL_GUESTS);

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
