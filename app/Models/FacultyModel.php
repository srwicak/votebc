<?php

namespace App\Models;

use CodeIgniter\Model;

class FacultyModel extends Model
{
    protected $table = 'faculties';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'code'];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';

    protected $validationRules = [
        'name' => 'required',
        'code' => 'required|is_unique[faculties.code,id,{id}]'
    ];

    public function getDepartments($facultyId)
    {
        return $this->db->table('departments')
                       ->where('faculty_id', $facultyId)
                       ->get()
                       ->getResultArray();
    }
}