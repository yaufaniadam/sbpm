<?php defined('BASEPATH') OR exit('No direct script access allowed');

	class Dashboard extends MY_Controller {
		public function __construct(){
			parent::__construct();
		}

		public function index(){
			$data['view'] = 'admin/dashboard/index'; 
			$this->load->view('admin/layout', $data);
		}
		
	}

?>	
