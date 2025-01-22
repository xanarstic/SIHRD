<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\LevelModel;
use App\Models\SettingModel;
use App\Models\LowonganModel;
use App\Models\PelamarModel;
use CodeIgniter\Files\File;
use Config\Services;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Home extends BaseController
{
	public function index()
	{
		echo view('welcome_message');
	}

	// buat cek captcha offline
	// private $forceOfflineCaptcha = true;

	private function isInternetAvailable()
	{
		// buat cek captcha offline
		// if ($this->forceOfflineCaptcha) {
		// 	return false; // Simulasikan tidak ada internet
		// }

		// Logika asli cek internet
		$connected = @fsockopen("www.google.com", 80);
		if ($connected) {
			fclose($connected);
			return true;
		}
		return false;
	}

	public function generateCaptcha()
	{
		// Atur lebar dan tinggi gambar
		$width = 150;
		$height = 50;

		// Buat gambar kosong
		$image = imagecreate($width, $height);

		// Warna latar belakang dan teks
		$bgColor = imagecolorallocate($image, 255, 255, 255); // Putih
		$textColor = imagecolorallocate($image, 0, 0, 0); // Hitam

		// Buat teks CAPTCHA
		$captchaText = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 6);
		session()->set('captcha', $captchaText); // Simpan teks CAPTCHA ke session

		// Tambahkan teks ke gambar
		imagestring($image, 5, 10, 15, $captchaText, $textColor);

		// Set header untuk gambar
		header("Content-Type: image/png");
		imagepng($image); // Output gambar
		imagedestroy($image); // Hapus gambar dari memori
	}


	private function verifyCaptcha($captchaResponse)
	{
		$secretKey = '6LcZjL8qAAAAAD4Kx7Fh-oK3VbrhPO3vA6Wb2BFW'; // Ganti dengan Secret Key Anda
		$verifyURL = "https://www.google.com/recaptcha/api/siteverify";

		$response = file_get_contents($verifyURL . "?secret={$secretKey}&response={$captchaResponse}");
		$responseData = json_decode($response);
		return $responseData->success;
	}

	public function login()
	{
		if (session()->get('username')) {
			return redirect()->to('/home/dashboard');
		}

		$settingModel = new SettingModel();
		$setting = $settingModel->first(); // Ambil data pengaturan pertama

		$captchaMode = $this->isInternetAvailable() ? 'online' : 'offline';

		echo view('header', ['setting' => $setting]);
		echo view('login', ['setting' => $setting, 'captchaMode' => $captchaMode]);
	}

	public function loginProcess()
	{
		$username = $this->request->getPost('username');
		$password = $this->request->getPost('password');
		$captchaResponse = $this->request->getPost('g-recaptcha-response');
		$offlineCaptcha = $this->request->getPost('captcha');

		// Validasi login
		$userModel = new UserModel();
		$levelModel = new LevelModel();

		// Ambil data user berdasarkan username
		$user = $userModel->where('username', $username)->first();

		// Validasi reCAPTCHA
		if ($this->isInternetAvailable()) {
			if (!$this->verifyCaptcha($captchaResponse)) {
				session()->setFlashdata('error', 'Captcha verification failed. Please try again.');
				return redirect()->to('home/login');
			}
		} else {
			if (session()->get('captcha') !== $offlineCaptcha) {
				session()->setFlashdata('error', 'Invalid offline CAPTCHA. Please try again.');
				return redirect()->to('home/login');
			}
		}

		if ($user && password_verify($password, $user['password'])) {
			// Ambil nama level berdasarkan id_level
			$level = $levelModel->where('id_level', $user['id_level'])->first();

			// Set session
			session()->set([
				'id_user' => $user['id_user'], // Tambahkan id_user ke session
				'username' => $user['username'],
				'level' => $level['level'], // Dapatkan nama level
				'logged_in' => true
			]);

			return redirect()->to('/home/dashboard');
		} else {
			// Jika login gagal
			session()->setFlashdata('error', 'Invalid username or password');
			return redirect()->to('home/login');
		}
	}

	public function registerProcess()
	{
		$username = $this->request->getPost('username');
		$email = $this->request->getPost('email');
		$nohp = $this->request->getPost('nohp');
		$password = $this->request->getPost('password');
		$captchaResponse = $this->request->getPost('g-recaptcha-response');
		$offlineCaptcha = $this->request->getPost('captcha');

		// Validasi reCAPTCHA
		if ($this->isInternetAvailable()) {
			if (!$this->verifyCaptcha($captchaResponse)) {
				session()->setFlashdata('error', 'Captcha verification failed. Please try again.');
				return redirect()->to('home/login');
			}
		} else {
			if (session()->get('captcha') !== $offlineCaptcha) {
				session()->setFlashdata('error', 'Invalid offline CAPTCHA. Please try again.');
				return redirect()->to('home/login');
			}
		}

		// Validasi input
		if (!$username || !$email || !$nohp || !$password) {
			session()->setFlashdata('error', 'Semua kolom wajib diisi.');
			return redirect()->to('home/login');
		}

		// Cek apakah username atau email sudah ada
		$userModel = new UserModel();
		$existingUser = $userModel->where('username', $username)->orWhere('email', $email)->first();

		if ($existingUser) {
			session()->setFlashdata('error', 'Username atau Email sudah digunakan.');
			return redirect()->to('home/login');
		}

		// Hash password
		$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

		// Simpan user baru
		$userModel->insert([
			'username' => $username,
			'email' => $email,
			'nohp' => $nohp,
			'password' => $hashedPassword,
			'id_level' => 3, // Default id_level untuk pengguna baru
		]);

		session()->setFlashdata('success', 'Registrasi berhasil. Silakan login.');
		return redirect()->to('home/login');
	}

	public function logout()
	{
		session()->destroy(); // Hapus sesi
		return redirect()->to('home/login');
	}

	public function dashboard()
	{
		$username = session()->get('username');
		$level = session()->get('level');

		// Periksa apakah pengguna sudah login
		if (!$username || !$level) {
			return redirect()->to('home/login');
		}

		// Menentukan greeting berdasarkan waktu
		$greeting = $this->getGreeting();

		// Ambil data setting aplikasi
		$settingModel = new SettingModel();
		$setting = $settingModel->first();

		// Menghitung jumlah lowongan yang tersedia
		$lowonganModel = new LowonganModel();
		$availableJobs = $lowonganModel->where('status', 'available')->countAllResults();

		// Kirim data ke view
		echo view('header', ['setting' => $setting]);
		echo view('menu', [
			'setting' => $setting,
			'level' => $level // Kirim level pengguna ke view menu
		]);
		echo view('dashboard', [
			'username' => $username,
			'level' => $level,
			'greeting' => $greeting,
			'availableJobs' => $availableJobs // Tambahkan jumlah lowongan yang tersedia
		]);
		echo view('footer');
	}

	private function getGreeting()
	{
		// Set timezone ke GMT+7
		date_default_timezone_set('Asia/Jakarta');

		$hour = date('H');
		if ($hour >= 5 && $hour < 12) {
			return 'Good Morning';
		} elseif ($hour >= 12 && $hour < 17) {
			return 'Good Afternoon';
		} else {
			return 'Good Evening';
		}
	}

	public function getLowongan($id_lowongan)
	{
		$lowonganModel = new \App\Models\LowonganModel();
		$lowongan = $lowonganModel->find($id_lowongan);

		return $this->response->setJSON($lowongan);
	}

	public function lowongan()
	{
		$username = session()->get('username');
		$level = session()->get('level');
		$id_user = session()->get('id_user');

		if (!$username || !$level) {
			return redirect()->to('home/login');
		}

		$settingModel = new SettingModel();
		$setting = $settingModel->first(); // Ambil data pengaturan pertama

		// Model untuk mengakses data lowongan
		$lowonganModel = new \App\Models\LowonganModel();
		$pelamarModel = new \App\Models\PelamarModel();
		$data['lowongans'] = $lowonganModel->findAll();

		// Ambil semua lowongan
		$lowongans = $lowonganModel->findAll();

		// Periksa apakah user sudah melamar ke setiap lowongan
		foreach ($lowongans as $lowongan) {
			$lowongan->sudah_lamar = $pelamarModel->where('id_user', $id_user)
				->where('id_lowongan', $lowongan->id_lowongan)
				->countAllResults() > 0; // True jika sudah melamar
		}

		echo view('header', ['setting' => $setting]);
		echo view('menu', [
			'setting' => $setting,
			'level' => $level
		]);
		echo view('lowongan', [
			'setting' => $setting,
			'lowongans' => $lowongans,
			'level' => $level
		]);
		echo view('footer');
	}

	public function tambahLowongan()
	{
		// Ambil data dari POST
		$nama_lowongan = $this->request->getPost('nama_lowongan');
		$syarat = $this->request->getPost('syarat');

		// Menyimpan data ke database
		$data = [
			'nama_lowongan' => $nama_lowongan,
			'syarat' => $syarat
		];

		$db = \Config\Database::connect();
		$lowonganModel = $db->table('lowongan');
		$lowonganModel->insert($data); // Menyimpan data lowongan baru

		// Setelah menyimpan, redirect ke halaman lowongan
		return redirect()->to('/home/lowongan');
	}

	public function editLowongan()
	{
		$id_lowongan = $this->request->getPost('id_lowongan');
		$nama_lowongan = $this->request->getPost('nama_lowongan');
		$syarat = $this->request->getPost('syarat');

		$lowonganModel = new \App\Models\LowonganModel();

		$data = [
			'nama_lowongan' => $nama_lowongan,
			'syarat' => $syarat,
		];

		log_message('debug', 'Data yang diterima: ' . json_encode($data));

		if ($lowonganModel->update($id_lowongan, $data)) {
			session()->setFlashdata('success', 'Lowongan berhasil diperbarui.');
		} else {
			session()->setFlashdata('error', 'Gagal memperbarui lowongan.');
		}

		return redirect()->to('/home/lowongan');
	}


	public function deleteLowongan($id_lowongan)
	{
		$lowonganModel = new \App\Models\LowonganModel();

		// Hapus lowongan berdasarkan ID
		$lowonganModel->delete($id_lowongan);

		// Redirect kembali ke halaman lowongan dengan pesan sukses
		session()->setFlashdata('success', 'Lowongan berhasil dihapus.');
		return redirect()->to('/home/lowongan');
	}

	public function lamar()
	{
		$pelamarModel = new PelamarModel();
		$id_user = session()->get('id_user');

		if ($this->request->getMethod() === 'post') {
			$idLowongan = $this->request->getPost('id_lowongan');

			// Periksa apakah user sudah melamar
			$sudahMelamar = $pelamarModel->where('id_user', $id_user)
				->where('id_lowongan', $idLowongan)
				->countAllResults() > 0;

			if ($sudahMelamar) {
				return redirect()->back()->with('error', 'Anda sudah melamar ke lowongan ini. Tunggu email dari HRD.');
			}

			// Proses form jika belum melamar
			$tglLahir = $this->request->getPost('tgl_lahir');
			$alamat = $this->request->getPost('alamat');
			$cv = $this->request->getFile('cv');
			$surat = $this->request->getFile('surat');

			if ($cv->isValid() && !$cv->hasMoved() && $surat->isValid() && !$surat->hasMoved()) {
				$cv->move('uploads/cv_files');
				$surat->move('uploads/surat_files');

				$data = [
					'id_user' => $id_user,
					'id_lowongan' => $idLowongan,
					'tgl_lahir' => $tglLahir,
					'alamat' => $alamat,
					'cv' => 'uploads/cv_files/' . $cv->getName(),
					'surat' => 'uploads/surat_files/' . $surat->getName(),
					'status' => 'Pending',
				];

				$pelamarModel->insert($data);
				return redirect()->to('home/lowongan')->with('success', 'Lamaran Anda berhasil dikirim.');
			}

			return redirect()->back()->with('error', 'Terjadi kesalahan dalam mengunggah file.');
		}
	}

	public function karyawan()
	{
		$username = session()->get('username');
		$level = session()->get('level');

		if (!$username || !$level) {
			return redirect()->to('home/login');
		}

		$db = \Config\Database::connect();
		$karyawanModel = $db->table('karyawan');
		$userModel = $db->table('user');

		// Ambil semua data karyawan dan join dengan data user
		$karyawans = $karyawanModel->select('karyawan.*, user.username')
			->join('user', 'karyawan.id_user = user.id_user')
			->get()->getResult();

		$settingModel = new SettingModel();
		$setting = $settingModel->first(); // Ambil data pengaturan pertama

		// Kirim data ke view
		echo view('header', ['setting' => $setting]);
		echo view('menu', [
			'setting' => $setting,
			'level' => $level
		]); // Jika ada menu
		echo view('karyawan', ['karyawans' => $karyawans]);
		echo view('footer'); // Jika ada footer
	}

	public function editKaryawan($id_karyawan)
	{
		$db = \Config\Database::connect();
		$karyawanModel = $db->table('karyawan');

		if ($this->request->getMethod() === 'post') {
			$data = [
				'gaji' => $this->request->getPost('gaji'),
				'divisi' => $this->request->getPost('divisi'),
			];

			$karyawanModel->where('id_karyawan', $id_karyawan)->update($data);
			return redirect()->to('/home/karyawan');
		}
	}

	public function pelamar()
	{
		$username = session()->get('username');
		$level = session()->get('level');

		if (!$username || !$level) {
			return redirect()->to('home/login');
		}

		$settingModel = new SettingModel();
		$setting = $settingModel->first();

		// Load data pelamar dengan join ke tabel users dan lowongan
		$db = \Config\Database::connect();
		$builder = $db->table('pelamar');
		$builder->select('pelamar.*, user.username, lowongan.nama_lowongan, lowongan.syarat');
		$builder->join('user', 'pelamar.id_user = user.id_user', 'left');
		$builder->join('lowongan', 'pelamar.id_lowongan = lowongan.id_lowongan', 'left');
		$pelamars = $builder->get()->getResultArray();

		echo view('header', ['setting' => $setting]);
		echo view('menu', [
			'setting' => $setting,
			'level' => $level
		]);
		echo view('lamar', ['pelamars' => $pelamars]); // Kirim data pelamar ke view
		echo view('footer');
	}

	public function updateStatusPelamar()
	{
		$pelamarModel = new PelamarModel();
		$userModel = new UserModel();

		$idPelamar = $this->request->getPost('id_pelamar');
		$status = $this->request->getPost('status');

		// Ambil data pelamar berdasarkan ID
		$pelamar = $pelamarModel->find($idPelamar);

		if (!$pelamar) {
			return redirect()->back()->with('error', 'Pelamar tidak ditemukan.');
		}

		// Update status pelamar
		$pelamarModel->update($idPelamar, ['status' => $status]);

		// Kirim email ke user terkait menggunakan PHPMailer
		$user = $userModel->find($pelamar['id_user']);

		// Konfigurasi PHPMailer
		$mail = new PHPMailer(true);
		try {
			$mail->isSMTP();
			$mail->Host = 'smtp.gmail.com';  // Ganti dengan SMTP server yang sesuai
			$mail->SMTPAuth = true;
			$mail->Username = 'your-email@gmail.com';  // Ganti dengan email pengirim
			$mail->Password = 'your-email-password';  // Ganti dengan password email pengirim
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
			$mail->Port = 587;

			$mail->setFrom('your-email@gmail.com', 'Admin');
			$mail->addAddress($user['email'], $user['username']);  // Alamat email tujuan

			if ($status == 'Diterima') {
				$mail->Subject = 'Lamaran Diterima';
				$mail->Body = 'Selamat! Lamaran Anda diterima dan status Anda diubah menjadi "Security".';
			} else {
				$mail->Subject = 'Lamaran Ditolak';
				$mail->Body = 'Mohon maaf, lamaran Anda ditolak.';
			}

			// Kirim email
			$mail->send();

		} catch (Exception $e) {
			echo "Pesan tidak dapat dikirim. Mailer Error: {$mail->ErrorInfo}";
		}

		// Ubah id_level jika diterima
		if ($status == 'Diterima') {
			$userModel->update($pelamar['id_user'], ['id_level' => 2]);
		}

		return redirect()->back()->with('success', 'Status pelamar diperbarui dan email dikirim.');
	}


	public function deletePelamar()
	{
		if ($this->request->getMethod() === 'post') {
			$idPelamar = $this->request->getPost('id_pelamar');

			$pelamarModel = new \App\Models\PelamarModel();

			if ($pelamarModel->delete($idPelamar)) {
				return redirect()->to('/home/pelamar')->with('success', 'Data pelamar berhasil dihapus.');
			} else {
				return redirect()->to('/home/pelamar')->with('error', 'Gagal menghapus data pelamar.');
			}
		}

		throw new \CodeIgniter\Exceptions\PageNotFoundException();
	}

	public function getLowonganById()
	{
		if ($this->request->isAJAX()) {
			$data = $this->request->getJSON();
			log_message('debug', 'Request Data: ' . json_encode($data));
			$idLowongan = $data->id_lowongan ?? null;

			if (!$idLowongan) {
				return $this->response->setJSON(['error' => 'ID Lowongan not provided'], 400);
			}

			$lowonganModel = new \App\Models\LowonganModel();
			$lowongan = $lowonganModel->find($idLowongan);

			if ($lowongan) {
				return $this->response->setJSON($lowongan);
			} else {
				return $this->response->setJSON(['error' => 'Lowongan not found'], 404);
			}
		}
		throw new \CodeIgniter\Exceptions\PageNotFoundException();
	}

	public function user()
	{
		$db = \Config\Database::connect();
		$userModel = $db->table('user');
		$levelModel = $db->table('level');
		$level = session()->get('level');

		// Ambil data dari tabel user dan level
		$users = $userModel->get()->getResult();
		$levels = $levelModel->get()->getResult();

		// Join tabel user dan level
		$users = $userModel->select('user.*, level.level AS level_name')
			->join('level', 'user.id_level = level.id_level')
			->get()
			->getResult();

		$settingModel = new SettingModel();
		$setting = $settingModel->first(); // Ambil data pengaturan pertama

		// Kirim data ke view
		echo view('header', ['setting' => $setting]);
		echo view('menu', [
			'setting' => $setting,
			'level' => $level
		]);
		echo view('user', ['users' => $users, 'levels' => $levels]);
		echo view('footer');
	}

	public function addUser()
	{
		if ($this->request->getMethod() === 'post') {
			$db = \Config\Database::connect();
			$userModel = $db->table('user');

			// Data untuk tabel user
			$userData = [
				'username' => $this->request->getPost('username'),
				'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
				'email' => $this->request->getPost('email'),
				'nohp' => $this->request->getPost('nohp'),
				'id_level' => $this->request->getPost('id_level'),
			];

			// Insert data ke tabel user
			$userModel->insert($userData);

			// Dapatkan id_user yang baru saja ditambahkan
			$insertedId = $db->insertID();

			// Jika level adalah 2 (karyawan), tambahkan data ke tabel karyawan
			if ($this->request->getPost('id_level') == 2) {
				$karyawanModel = $db->table('karyawan');
				$karyawanData = [
					'id_user' => $insertedId,
					'gaji' => $this->request->getPost('gaji'),
					'divisi' => $this->request->getPost('divisi'),
				];
				$karyawanModel->insert($karyawanData);
			}

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
		$username = session()->get('username');
		$level = session()->get('level');

		if (!$username || !$level) {
			return redirect()->to('home/login');
		}

		// Ambil data pengaturan dari database
		$settingModel = new SettingModel();
		$setting = $settingModel->first(); // Ambil data pengaturan pertama (misal hanya ada satu baris data)

		// Tampilkan halaman pengaturan dengan data
		echo view('header', ['setting' => $setting]);
		echo view('menu', [
			'setting' => $setting,
			'level' => $level
		]);
		echo view('setting', ['setting' => $setting]);
		echo view('footer');
	}

	public function updatesetting()
	{
		$settingModel = new SettingModel();

		// Validasi input
		$validation = \Config\Services::validation();
		if (
			!$this->validate([
				'namawebsite' => 'required|min_length[3]',
				'icontab' => 'permit_empty|is_image[icontab]',
				'iconlogin' => 'permit_empty|is_image[iconlogin]',
				'iconmenu' => 'permit_empty|is_image[iconmenu]'
			])
		) {
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
				$data[$field] = $fileName;
			}
		}

		// Update data pengaturan di database
		$settingModel->update(1, $data); // Mengupdate baris pertama di tabel pengaturan

		// Set flashdata dan redirect ke halaman pengaturan
		session()->setFlashdata('success', 'Pengaturan berhasil diperbarui!');
		return redirect()->to('home/setting');
	}
}
