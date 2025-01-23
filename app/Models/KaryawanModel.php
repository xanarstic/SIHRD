<?php

namespace App\Models;

use CodeIgniter\Model;

class KaryawanModel extends Model
{
    protected $table = 'karyawan';
    protected $primaryKey = 'id_karyawan';
    protected $allowedFields = ['id_user', 'gaji', 'divisi'];
    protected $useTimestamps = false;
    protected $returnType = 'object';
}
