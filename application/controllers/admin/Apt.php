<?php defined('BASEPATH') or exit('No direct script access allowed');

class Apt extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin/apt_model', 'apt_model');
	}

	public function details($id)
	{
		$data['dokumen'] = $this->apt_model->get_dokumen_by_id($id);
		$data['view'] = 'admin/borang/detail_dokumen.php';
		$this->load->view('admin/layout', $data);
	}

	public function index()
	{
		redirect(base_url('admin/apt/dokumen/evaluasi'));
	}

	public function dokumen($id_kategori)
	{
		// $this->db->select('id');
		// $query = $this->db->get_where('kategori_dokumen',array('singkatan'=>$kategori));
		// $kategori = $query->row_array();
		$get_kategori = $this->db->get_where('kategori_dokumen', array('id' => $id_kategori));
		$resultRow = $get_kategori->row();
		$class_menu = $resultRow->singkatan;
		$data['ambil_dokumen'] = $this->apt_model->ambil_dokumen($id_kategori);
		$data['class_menu'] = $class_menu;
		$data['view'] = 'admin/borang/apt/index';
		$this->load->view('admin/layout', $data);
	}

	public function kategori()
	{
		$data['ambil_kategori'] = $this->apt_model->ambil_kategori();
		$data['view'] = 'admin/borang/kategori/index_kategori';
		$this->load->view('admin/layout', $data);
	}

	public function tambah()
	{
		$data['view'] = 'admin/borang/apt/tambah_borang';
		$this->load->view('admin/layout', $data);
	}

	public function store($kategori)
	{
		if ($this->input->post('submit')) {
			$this->form_validation->set_rules('nama', 'Nama Dokumen', 'trim|required');
			$this->form_validation->set_rules('deskripsi', 'Deskripsi Dokumen', 'trim|required');
			$this->form_validation->set_rules('tahun', 'Tahun Dokumen', 'trim|required');

			if ($this->form_validation->run() == FALSE) {
				$data['view'] = 'admin/borang/apt/tambah_borang';
				$this->load->view('admin/layout', $data);
			} else {

				$upload_path = './uploads/dokumen';

				if (!is_dir($upload_path)) {
					mkdir($upload_path, 0777, TRUE);
				}
				$config = array(
					'upload_path' => $upload_path,
					'allowed_types' => "docx|pdf|",
					'overwrite' => FALSE,
				);

				$this->load->library('upload', $config);
				$this->upload->do_upload('dokumen');
				$dokumen = $this->upload->data();

				// $this->db->select('id');
				// $query = $this->db->get_where('kategori_dokumen',array('singkatan'=>$kategori));
				// $id_kategori = $query->row_array();

				$data = array(
					'nama_dokumen' => $this->input->post('nama'),
					'deskripsi' => $this->input->post('deskripsi'),
					'id_prodi' => '',
					'id_kategori_dokumen' => $kategori,
					'tahun' => $this->input->post('tahun'),
					'file' => $upload_path . '/' . $dokumen['file_name'],
				);

				$data = $this->security->xss_clean($data);
				$result = $this->apt_model->add_evaluasi_borang($data);
				if ($result) {
					$this->session->set_flashdata('msg', 'Dokumen baru berhasil ditambahkan!');
					redirect(base_url('admin/apt/dokumen/' . $kategori));
				}
			}
		} else {
			$data['view'] = 'admin/borang/apt/tambah_borang';
			$this->load->view('admin/layout', $data);
		}
	}

	public function destroy($id, $kategori)
	{
		$this->db->delete('dokumen_apt', array('id' => $id));
		$this->session->set_flashdata('msg', 'Dokumen berhasil dihapus!');
		redirect(base_url('admin/apt/dokumen/' . $kategori));
	}
}
