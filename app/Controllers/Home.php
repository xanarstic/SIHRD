<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\LevelModel;
use App\Models\SettingModel;
use CodeIgniter\Files\File;
use Config\Services;

class Home extends BaseController
{
	public function index()
	{
		echo view('welcome_message');
	}

	public function login()
	{
		// Cek jika sudah login, arahkan ke dashboard
		if (session()->get('username')) {
			return redirect()->to('/home/dashboard');
		}

		$settingModel = new SettingModel();
		$setting = $settingModel->first(); // Ambil data pengaturan pertama

		echo view('login', ['setting' => $setting]);
	}

	public function loginProcess()
	{
		$username = $this->request->getPost('username');
		$password = $this->request->getPost('password');

		// Validasi login
		$userModel = new UserModel();
		$levelModel = new LevelModel();

		// Ambil data user berdasarkan username
		$user = $userModel->where('username', $username)->first();

		if ($user && password_verify($password, $user['password'])) {
			// Ambil nama level berdasarkan id_level
			$level = $levelModel->where('id_level', $user['id_level'])->first();

			// Set session
			session()->set([
				'username' => $user['username'],
				'level' => $level['level'], // Dapatkan nama level
				'logged_in' => true
			]);

			return redirect()->to('/home/dashboard');
		} else {
			// Jika login gagal
			session()->setFlashdata('error', 'Invalid username or password');
			return redirect()->to('/login');
		}
	}

	public function logout()
	{
		session()->destroy(); // Hapus sesi
		return redirect()->to('home/login');
	}

	public function dashboard()
	{
		// Mendapatkan data dari session
		$username = session()->get('username');
		$level = session()->get('level');

		// Jika session kosong, arahkan kembali ke halaman login atau sesuaikan dengan aplikasi Anda
		if (!$username || !$level) {
			return redirect()->to('home/login'); // Atau halaman yang sesuai
		}

		// Menentukan greeting berdasarkan waktu
		$greeting = $this->getGreeting();

		$settingModel = new SettingModel();
		$setting = $settingModel->first(); // Ambil data pengaturan pertama

		// Kirim data ke view
		echo view('menu', ['setting' => $setting]); // Menampilkan menu sebelum dashboard
		echo view('dashboard', [
			'username' => $username,
			'level' => $level,
			'greeting' => $greeting
		], ['setting' => $setting]);
		echo view('footer'); // Menampilkan footer setelah dashboard
	}

	private function getGreeting()
	{
		$hour = date('H');
		if ($hour >= 5 && $hour < 12) {
			return 'Good Morning';
		} elseif ($hour >= 12 && $hour < 17) {
			return 'Good Afternoon';
		} else {
			return 'Good Evening';
		}
	}

	public function lowongan()
	{
		echo view('lowongan');
	}

	public function karyawan()
	{
		echo view('karyawan');
	}

	public function lamar()
	{
		echo view('lamar');
	}

	public function user()
	{
		$db = \Config\Database::connect();
		$userModel = $db->table('user');
		$levelModel = $db->table('level');

		// Ambil data dari tabel user dan level
		$users = $userModel->get()->getResult();
		$levels = $levelModel->get()->getResult();

		$settingModel = new SettingModel();
		$setting = $settingModel->first(); // Ambil data pengaturan pertama

		// Kirim data ke view
		echo view('menu', ['setting' => $setting]);
		echo view('user', ['users' => $users, 'levels' => $levels]);
		echo view('footer');
	}

	public function addUser()
	{
		if ($this->request->getMethod() === 'post') {
			$data = [
				'username' => $this->request->getPost('username'),
				'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
				'email' => $this->request->getPost('email'),
				'nohp' => $this->request->getPost('nohp'),
				'id_level' => $this->request->getPost('id_level'),
			];

			$db = \Config\Database::connect();
			$userModel = $db->table('user');
			$userModel->insert($data);

			return redirect()->to('home/user');
		}
	}

	public function addLevel()
	{
		if ($this->request->getMethod() === 'post') {
			$data = [
				'level' => $this->request->getPost('level'),
			];

			$db = \Config\Database::connect();
			$levelModel = $db->table('level');
			$levelModel->insert($data);

			return redirect()->to('home/user');
		}
	}

	public function deleteUser($id)
	{
		$db = \Config\Database::connect();
		$userModel = $db->table('user');
		$userModel->delete(['id_user' => $id]);

		return redirect()->to('home/user')->with('message', 'User berhasil dihapus');
	}

	public function deleteLevel($id)
	{
		$db = \Config\Database::connect();
		$levelModel = $db->table('level');
		$levelModel->delete(['id_level' => $id]);

		return redirect()->to('home/user')->with('message', 'Level berhasil dihapus');
	}

	public function setting()
	{
		// Ambil data pengaturan dari database
		$settingModel = new SettingModel();
		$setting = $settingModel->first(); // Ambil data pengaturan pertama (misal hanya ada satu baris data)

		// Tampilkan halaman pengaturan dengan data
		echo view('menu', ['setting' => $setting]);
		echo view('setting', ['setting' => $setting]);
		echo view('footer');
	}

	public function updatesetting()
	{
		$settingModel = new SettingModel();

		// Validasi input
		$validation = \Config\Services::validation();
		if (!$this->validate([
			'namawebsite' => 'required|min_length[3]',
			'icontab' => 'permit_empty|is_image[icontab]',
			'iconlogin' => 'permit_empty|is_image[iconlogin]',
			'iconmenu' => 'permit_empty|is_image[iconmenu]'
		])) {
			// Jika validasi gagal, kembali ke form dengan error
			return redirect()->back()->withInput()->with('errors', $validation->getErrors());
		}

		// Ambil input dari form
		$data = [
			'namawebsite' => $this->request->getVar('namawebsite'),
		];

		// Menangani upload gambar jika ada
		$imageFields = ['icontab', 'iconlogin', 'iconmenu'];
		foreach ($imageFields as $field) {
			$file = $this->request->getFile($field);
			if ($file->isValid()) {
				$uploadPath = ROOTPATH . 'public/uploads/';
				$fileName = $file->getName();  // Mengambil nama file asli

				// Jika file sudah ada, hapus file lama
				if (file_exists($uploadPath . $fileName)) {
					unlink($uploadPath . $fileName);  // Menghapus file yang sudah ada
				}

				// Pindahkan file yang baru ke folder uploads dengan nama file yang sama
				$file->move($uploadPath, $fileName);

				// Simpan path file ke database
				$data[$field] = 'uploads/' . $fileName;
			}
		}

		// Update data pengaturan di database
		$settingModel->update(1, $data); // Mengupdate baris pertama di tabel pengaturan

		// Set flashdata dan redirect ke halaman pengaturan
		session()->setFlashdata('success', 'Pengaturan berhasil diperbarui!');
		return redirect()->to('home/setting');
	}
}
