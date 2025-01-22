<?php

namespace App\Models;

use CodeIgniter\Model;

class PelamarModel extends Model
{
    protected $table = 'pelamar';
    protected $primaryKey = 'id_pelamar';
    protected $allowedFields = ['id_user', 'id_lowongan', 'tgl_lahir', 'alamat', 'cv', 'surat', 'status'];
}