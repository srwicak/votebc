<?php

namespace App\Models;

use CodeIgniter\Model;

class DepartmentModel extends Model
{
    protected $table = 'departments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'code', 'faculty_id'];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';

    protected $validationRules = [
        'name' => 'required',
        'code' => 'required|is_unique[departments.code,id,{id}]',
        'faculty_id' => 'required|is_natural_no_zero'
    ];

    public function getFaculty($departmentId)
    {
        return $this->select('faculties.*')
                   ->join('faculties', 'faculties.id = departments.faculty_id')
                   ->where('departments.id', $departmentId)
                   ->first();
    }
}