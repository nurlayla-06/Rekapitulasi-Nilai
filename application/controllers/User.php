<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{

    public function index()
    {
        $data['title'] = 'Dashboard Mahasiswa';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        $this->load->view('template_user/header', $data);
        $this->load->view('template_user/sidebar', $data);
        $this->load->view('template_user/topbar', $data);
        $this->load->view('user/index', $data);
        $this->load->view('template_user/footer');
    }

    public function data_nilai()
    {
        $data['title'] = 'Dashboard Mahasiswa';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        $this->load->view('template_user/header', $data);
        $this->load->view('template_user/sidebar', $data);
        $this->load->view('template_user/topbar', $data);
        $this->load->view('user/data_nilai', $data);
        $this->load->view('template_user/footer');
    }
}
