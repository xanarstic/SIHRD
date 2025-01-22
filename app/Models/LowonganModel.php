<?php

namespace App\Models;

use CodeIgniter\Model;

class LowonganModel extends Model
{
    protected $table = 'lowongan';
    protected $primaryKey = 'id_lowongan';
    protected $allowedFields = ['nama_lowongan', 'syarat'];
    protected $useTimestamps = true;
    protected $returnType = 'object';
}
