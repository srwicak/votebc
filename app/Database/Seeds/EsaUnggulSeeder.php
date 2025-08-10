<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EsaUnggulSeeder extends Seeder
{
    public function run()
    {
        // First, we need to handle the foreign key constraints
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        
        // Insert Fakultas for Esa Unggul University Bekasi
        // First, clear existing faculties
        $this->db->table('faculties')->emptyTable();
        
        $faculties = [
            [
                'name' => 'Fakultas Ekonomi dan Bisnis',
                'code' => 'FEB',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Fakultas Ilmu Komputer',
                'code' => 'FASILKOM',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Fakultas Ilmu Kesehatan',
                'code' => 'FIKES',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Fakultas Hukum',
                'code' => 'FH',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Fakultas Ilmu Komunikasi',
                'code' => 'FIKOM',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Fakultas Keguruan dan Ilmu Pendidikan',
                'code' => 'FKIP',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Fakultas Teknik',
                'code' => 'FT',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Fakultas Desain dan Industri Kreatif',
                'code' => 'FDIK',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Fakultas Psikologi',
                'code' => 'FPsi',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('faculties')->insertBatch($faculties);
        
        // Now clear and insert departments
        $this->db->table('departments')->emptyTable();
        
        // Insert Jurusan for Esa Unggul University Bekasi
        $departments = [
            // Fakultas Ekonomi dan Bisnis
            [
                'name' => 'Manajemen',
                'code' => 'MNJ',
                'faculty_id' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Akuntansi',
                'code' => 'AKT',
                'faculty_id' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bisnis Digital',
                'code' => 'BD',
                'faculty_id' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Fakultas Ilmu Komputer
            [
                'name' => 'Teknik Informatika',
                'code' => 'TI',
                'faculty_id' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Sistem Informasi',
                'code' => 'SI',
                'faculty_id' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Fakultas Ilmu Kesehatan
            [
                'name' => 'Keperawatan',
                'code' => 'KEP',
                'faculty_id' => 3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Kesehatan Masyarakat',
                'code' => 'KM',
                'faculty_id' => 3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Gizi',
                'code' => 'GZ',
                'faculty_id' => 3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Fisioterapi',
                'code' => 'FT',
                'faculty_id' => 3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Fakultas Hukum
            [
                'name' => 'Ilmu Hukum',
                'code' => 'IH',
                'faculty_id' => 4,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Fakultas Ilmu Komunikasi
            [
                'name' => 'Ilmu Komunikasi',
                'code' => 'IKOM',
                'faculty_id' => 5,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Marketing Komunikasi',
                'code' => 'MARKOM',
                'faculty_id' => 5,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Fakultas Keguruan dan Ilmu Pendidikan
            [
                'name' => 'Pendidikan Guru Sekolah Dasar',
                'code' => 'PGSD',
                'faculty_id' => 6,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Pendidikan Bahasa Inggris',
                'code' => 'PBI',
                'faculty_id' => 6,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Fakultas Teknik
            [
                'name' => 'Teknik Industri',
                'code' => 'TIN',
                'faculty_id' => 7,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Teknik Sipil',
                'code' => 'TS',
                'faculty_id' => 7,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Fakultas Desain dan Industri Kreatif
            [
                'name' => 'Desain Komunikasi Visual',
                'code' => 'DKV',
                'faculty_id' => 8,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Desain Produk',
                'code' => 'DP',
                'faculty_id' => 8,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Fakultas Psikologi
            [
                'name' => 'Psikologi',
                'code' => 'PSI',
                'faculty_id' => 9,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('departments')->insertBatch($departments);
        
        // Re-enable foreign key checks
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        
        // Re-insert Admin User (Super Admin)
        $users = [
            [
                'nim' => 'ADMIN001',
                'name' => 'Admin Utama',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin',
                'is_super_admin' => 1,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
        ];

        // Check if users table exists and has records
        $userExists = $this->db->table('users')->countAllResults() > 0;
        
        if ($userExists) {
            // Update existing admin user
            $this->db->table('users')->where('is_super_admin', 1)->update([
                'name' => 'Admin Utama',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            // Check if operator exists
            $operatorExists = $this->db->table('users')->where('role', 'operator')->countAllResults() > 0;
            
            if (!$operatorExists) {
                // Insert operator user
                $this->db->table('users')->insert($users[1]);
            }
        } else {
            // Insert both users
            $this->db->table('users')->insertBatch($users);
        }
    }
}