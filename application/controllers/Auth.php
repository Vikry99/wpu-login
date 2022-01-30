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
        // memnambahkan rules
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        // menambah rules untuk password
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        // validasi login
        if ($this->form_validation->run() == false) {
            $data['title'] = 'Login Page';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/login');
            $this->load->view('templates/auth_footer');
        } else {
            // ketika validasinya llos
            $this->_login();
        }
    }


    private function _login()
    {
        // ambil terlebih dahulu email yang sudah lolos
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        // lalu query ke database ambil data user terlebih dahulu lalu query menggunakan query CI, row_array untuk mendapatkan data satu baris
        $user = $this->db->get_where('user', ['email' => $email])->row_array();

        // cek jika ada usernya
        if ($user) {
            // usernya apakah aktif atau tidak
            if ($user['is_actived'] == 1) {
                // Cek passwordnya menggunakan syntak php password_perify untuk menyamakan password yang di ketik form dan yang sudah di hash
                if (password_verify($password, $user['password'])) {
                    // kita siapkan data di dalam session email dan rolenya apa, role id untuk menentukan menunya nnti
                    $data = [
                        'email' => $user['email'],
                        'role_id' => $user['role_id']
                    ];
                    // lalu simpan kedalam session
                    $this->session->set_userdata($data);
                    // lalu kita arahkan jika user ke user jika admin ke admin
                    // melakukan pengecekan user admin atau user
                    if ($user['role_id'] == 1) {
                        redirect('admin');
                    } else {
                        redirect('user');
                    }
                } else {
                    $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
                    Wrong password!!
                    </div>');
                    redirect('auth');
                }
            } else {
                // jika tidak aktif
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
                 This Email is not been activited!
                 </div>');
                redirect('auth');
            }
        } else {
            // jika usernya tidak ada
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
            Email is not registered!
          </div>');
            redirect('auth');
        }
    }



    public function registration()
    {
        // lalu kita tambahkan rulenya terlebih dahulu yang akan di terapkan ke formnya 
        // rules untuk name, kita harus mengetahui terlebih dahulu name di registrationnya di form name
        // di dalam rules name ada set_rules untuk memnentukan mana yang akan di set rulesnyam lalu di tanda kurung pertama ada nama name itu adalah namenya
        // yang kedua adalah Name untuk memberi notifnya untuk name,
        // lalu ketiga required itu method untuk kolom yang wajib di isi dan trim itu untuk ketika kita menambahka spasi di depan atau belakang formnya itu tidak akan di masukan ke data base
        $this->form_validation->set_rules('name', 'Name', 'required|trim');

        // selanjutnya untuk email untuk valid_email itu berguna untuk supaya user akan mengetikan struktur email yang valid ada @ ada .com dll
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[user.email]', [
            'is_unique' => 'This email has already registered!'
        ]);
        // selanjutnya untuk email untuk valid_email itu berguna untuk min_length itu berguna untuk ketika user memasukan password itu tidak boleh kurang dari 3 dan matches itu berguna untuk password 1 dan 2 itu sama
        $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[3]|matches[password2]', ['matches' => 'Password dont match!', 'min_length' => 'Password min 3 Characters']);
        // selanjutnya untuk email untuk valid_email itu berguna untuk min_length itu berguna untuk ketika user memasukan password itu tidak boleh kurang dari 3 dan matches itu berguna untuk password 1 dan 2 itu sama
        $this->form_validation->set_rules('password2', 'Password', 'required|matches[password1]');

        // mnggunakan statment if untuk melakukan validation jika form validasi gagal maka akan di tampilkan kembali halaman ini
        if ($this->form_validation->run() == false) {
            $data['title'] = 'User Registration';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/registration');
            $this->load->view('templates/auth_footer');
        } else {
            $data = [
                // htmlspecialchars untuk ketika user memasukan user tidak bisa memasukan syntak
                'name' => htmlspecialchars($this->input->post('name', true)),
                'email' => htmlspecialchars($this->input->post('email', true)),
                'image' => 'default.jpg',
                // password menggunakan function php password_hash(), menggunakan enkripsi dari php PASSWORD_DEFAULT
                'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT),
                'role_id' => 2,
                'is_actived' => 1,
                'date_created' => time()
            ];

            // lalu datanya akan di insert ke database
            $this->db->insert('user', $data);
            // membuat flash data memakai session
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Congratulation your account has been created. Please Login
          </div>');
            // lalu redirect jika berhasil ke halaman login
            redirect('auth');
        }
    }

    public function logout()
    {
        // tugasnya membersihkan session dan kembali ke halaman login
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('role_id');

        // setelah itu redirect ke halaman login/auth
        // membuat flash data memakai session
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
        You have been logged out
        </div>');
        // lalu redirect jika berhasil ke halaman login
        redirect('auth');
    }
}
