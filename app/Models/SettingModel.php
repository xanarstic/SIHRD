<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
protected $table = 'setting';
protected $primaryKey = 'id_setting';
protected $allowedFields = ['namawebsite', 'icontab', 'iconlogin', 'iconmenu']; // Kolom yang dapat diubah
}