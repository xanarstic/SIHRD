<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'id_user';
    protected $allowedFields = ['username', 'email', 'nohp', 'password', 'id_level']; // Pastikan semua kolom yang digunakan ada di sini
}
