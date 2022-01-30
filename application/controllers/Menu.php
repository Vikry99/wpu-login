<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu extends CI_Controller
{
    public function index()
    {
        // ambil data dari session
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        $data['title'] = 'Menu Management';

        // kita query data menunya
        $data['menu'] = $this->db->get('user_menu')->result_array();

        // kita bikin rulesnya
        $this->form_validation->set_rules('menu', 'Menu', 'required');

        // kondisi untuk add menu
        if ($this->form_validation->run() == false) {
            // disni akan memanggil viewsnya
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('templates/footer');
        } else {
            // jika berhasil tambahkan baru
            $this->db->insert('user_menu', ['menu' => $this->input->post('menu')]);
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
                    New Menu Added!!
                    </div>');
            redirect('menu');
        }
    }
    public function submenu()
    {
        // ambil data dari session
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        $data['title'] = 'Submenu Management';

        // load menu model
        $this->load->model('Menu_model', 'menu');

        // Query datanya terlebih dahulu supaya bisa di pakai di index sub menu
        $data['subMenu'] = $this->menu->getSubMenu();
        $data['menu'] = $this->db->get('user_menu')->result_array();

        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('menu_id', 'Menu', 'required');
        $this->form_validation->set_rules('url', 'URL', 'required');
        $this->form_validation->set_rules('icon', 'icon', 'required');

        if ($this->form_validation->run() == false) {
            // disni akan memanggil viewsnya
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('menu/submenu', $data);
            $this->load->view('templates/footer');
        }
    }
}
