<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Admin Akademik
        User::updateOrCreate(
            ['email' => 'admin@uinril.ac.id'],
            [
                'name' => 'Administrator Akademik',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'nim_nip' => '198801012015031001',
                'phone' => '081234567890',
                'status' => 'active',
            ]
        );

        // 2. Ketua Program Studi (Pihak Akademik)
        User::updateOrCreate(
            ['email' => 'kaprodi@uinril.ac.id'],
            [
                'name' => 'Dr. Kaprodi Sistem Informasi, M.Kom.',
                'password' => Hash::make('prodi123'),
                'role' => 'prodi',
                'nim_nip' => '197905152008011005',
                'phone' => '081234567891',
                'status' => 'active',
            ]
        );

        // 3. Dosen Pembimbing Akademik (Dosen PA 1)
        User::updateOrCreate(
            ['email' => 'dosenpa@uinril.ac.id'],
            [
                'name' => 'Dr. H. Ahmad Sudrajat, M.T.I.',
                'password' => Hash::make('dosen123'),
                'role' => 'dosen_pa',
                'nim_nip' => '198503102012011002',
                'phone' => '081234567892',
                'status' => 'active',
            ]
        );

        // 3b. Dosen Pembimbing Akademik (Dosen PA 2)
        User::updateOrCreate(
            ['email' => 'dosenpa2@uinril.ac.id'],
            [
                'name' => 'Siti Nurhaliza, S.Kom., M.Cs.',
                'password' => Hash::make('dosen123'),
                'role' => 'dosen_pa',
                'nim_nip' => '198807202015042003',
                'phone' => '081234567893',
                'status' => 'active',
            ]
        );

        // 4. Mahasiswa Peneliti / Uji
        User::updateOrCreate(
            ['email' => 'pingky@student.uinril.ac.id'],
            [
                'name' => 'Pingky Hera Veliyanti',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'nim_nip' => '2271020052',
                'phone' => '089876543210',
                'status' => 'active',
            ]
        );
    }
}
