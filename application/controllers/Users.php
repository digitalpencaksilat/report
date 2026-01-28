<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Users extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_inventory');

        // Wajib Login
        if (!$this->session->userdata('id_user')) {
            redirect('auth');
        }

        // HANYA ADMIN yang boleh akses
        if ($this->session->userdata('role') != 'admin') {
            show_404();
        }
    }

    public function index()
    {
        $data['title'] = 'User Manager';
        $data['users'] = $this->M_inventory->get_all_users();

        $this->load->view('templates/header', $data);
        $this->load->view('users/v_index', $data);
        $this->load->view('templates/footer');
    }

    public function proses_tambah()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        // Cek apakah username sudah ada (Manual check simple)
        $cek = $this->db->get_where('tb_users', ['username' => $username])->row();
        if ($cek) {
            $this->session->set_flashdata('error', 'Username sudah digunakan!');
            redirect('users');
            return;
        }

        $data = [
            'nama_lengkap' => $this->input->post('nama_lengkap'),
            'username'     => $username,
            // [HASHING] Enkripsi password sebelum disimpan
            'password'     => password_hash($password, PASSWORD_DEFAULT),
            'role'         => $this->input->post('role')
        ];

        if ($this->M_inventory->insert_user($data)) {
            $this->session->set_flashdata('success', 'User berhasil ditambahkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menambahkan user.');
        }
        redirect('users');
    }

    public function proses_edit()
    {
        $id_user = $this->input->post('id_user');
        $password = $this->input->post('password');

        $data = [
            'nama_lengkap' => $this->input->post('nama_lengkap'),
            'username'     => $this->input->post('username'),
            'role'         => $this->input->post('role')
        ];

        // Jika password diisi, maka update password baru (di-hash)
        // Jika kosong, berarti tidak ganti password
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($this->M_inventory->update_user($id_user, $data)) {
            $this->session->set_flashdata('success', 'Data user berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui data user.');
        }
        redirect('users');
    }

    public function hapus($id)
    {
        // Mencegah hapus diri sendiri
        if ($id == $this->session->userdata('id_user')) {
            $this->session->set_flashdata('error', 'Anda tidak bisa menghapus akun sendiri!');
            redirect('users');
            return;
        }

        if ($this->M_inventory->delete_user($id)) {
            $this->session->set_flashdata('success', 'User berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus user.');
        }
        redirect('users');
    }
}
