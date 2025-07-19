<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dosen extends CI_Controller
{

    public function index()
    {
        $data['title'] = 'Dashboard Dosen';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        $this->load->view('template_dosen/header', $data);
        $this->load->view('template_dosen/sidebar', $data);
        $this->load->view('template_dosen/topbar', $data);
        $this->load->view('dosen/index', $data);
        $this->load->view('template_dosen/footer');
    }

    public function edit_nilai()
    {
        $data['title'] = 'Edit Nilai';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        $this->load->view('template_dosen/header', $data);
        $this->load->view('template_dosen/sidebar', $data);
        $this->load->view('template_dosen/topbar', $data);
        $this->load->view('dosen/edit_nilai', $data);
        $this->load->view('template_dosen/footer');
    }

    public function input_nilai()
    {
        $data['title'] = 'Input Nilai';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        $this->load->view('template_dosen/header', $data);
        $this->load->view('template_dosen/sidebar', $data);
        $this->load->view('template_dosen/topbar', $data);
        $this->load->view('dosen/input_nilai', $data);
        $this->load->view('template_dosen/footer');
    }
}
