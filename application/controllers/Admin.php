<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{

    public function index()
    {
        $data['title'] = 'Dashboard Admin';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        $this->load->view('template_admin/header', $data);
        $this->load->view('template_admin/sidebar', $data);
        $this->load->view('template_admin/topbar', $data);
        $this->load->view('admin/index', $data);
        $this->load->view('template_admin/footer');
    }

    public function input_data()
    {
        $data['title'] = 'Input Data';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        $this->load->view('template_admin/header', $data);
        $this->load->view('template_admin/sidebar', $data);
        $this->load->view('template_admin/topbar', $data);
        $this->load->view('admin/input_data', $data);
        $this->load->view('template_admin/footer');
    }

    public function edit_data()
    {
        $data['title'] = 'Edit Data';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        $this->load->view('template_admin/header', $data);
        $this->load->view('template_admin/sidebar', $data);
        $this->load->view('template_admin/topbar', $data);
        $this->load->view('admin/edit_data', $data);
        $this->load->view('template_admin/footer');
    }
}
