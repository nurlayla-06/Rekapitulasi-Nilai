<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Login Page';
            $this->load->view('template/auth_header', $data);
            $this->load->view('auth/login');
            $this->load->view('template/auth_footer');
        } else {
            // validasinya sukses
            $this->_login();
        }
    }


    private function _login()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $user = $this->db->get_where('user', ['email' => $email])->row_array();

        // Jika user ada
        if ($user) {
            // Jika user aktif
            if ($user['is_active'] == 1) {
                // Cek password
                if (password_verify($password, $user['password'])) {
                    $data = [
                        'email' => $user['email'],
                        'role_id' => $user['role_id']
                    ];
                    $this->session->set_userdata($data);

                    // Arahkan sesuai role_id
                    if ($user['role_id'] == 1) {
                        redirect('admin');
                    } elseif ($user['role_id'] == 2) {
                        redirect('dosen');
                    } elseif ($user['role_id'] == 3) {
                        redirect('user');
                    } else {
                        $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Role ID tidak valid!</div>');
                        redirect('auth');
                    }
                } else {
                    $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Password Salah!</div>');
                    redirect('auth');
                }
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Email Belum di Aktivasi!</div>');
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Email Belum Pernah Terdaftar!</div>');
            redirect('auth');
        }
    }




    public function registrasi()
    {
        $this->form_validation->set_rules('name', 'Full Name', 'required|trim');
        $this->form_validation->set_rules('nim_nip', 'NIM/NIP', 'required|trim|is_unique[user.nim_nip]', [
            'is_unique' => 'This NIM/NIP has already been registered!'
        ]);
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[user.email]', [
            'is_unique' => 'This Email has already been registered!'
        ]);
        $this->form_validation->set_rules('status', 'Status', 'required');
        $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[3]|matches[password2]', [
            'matches' => 'Password tidak cocok!',
            'min_length' => 'Password minimal 3 karakter!'
        ]);
        $this->form_validation->set_rules('password2', 'Repeat Password', 'required|trim|matches[password1]');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'BD User Registrasi';
            $this->load->view('template/auth_header', $data);
            $this->load->view('auth/registrasi');
            $this->load->view('template/auth_footer');
        } else {
            $role_id = $this->input->post('status'); // Role ID sesuai pilihan

            $data = [
                'nama' => htmlspecialchars($this->input->post('name', true)),
                'nim_nip' => htmlspecialchars($this->input->post('nim_nip', true)),
                'email' => htmlspecialchars($this->input->post('email', true)),
                'image' => 'default.jpg',
                'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT),
                'role_id' => $role_id,
                'is_active' => 1,
                'date_created' => time()
            ];

            $this->db->insert('user', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Selamat! Akun Anda telah terdaftar. Silakan login!</div>');
            redirect('auth');
        }
    }



    public function logout()
    {
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('role_id');

        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Anda telah logout!</div>');
        redirect('auth');
    }
}
