<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KuesionerController extends Controller
{
    /**
     * Data dummy rekomendasi berdasarkan kategori
     */
    private function getRekomendasi(string $kategori): array
    {
        return match ($kategori) {
            'Sangat Tinggi' => [
                'Batasi penggunaan media sosial maksimal 1 jam per hari',
                'Aktifkan fitur screen time di perangkat',
                'Lakukan detoks digital minimal 1 hari per minggu',
                'Konsultasi dengan konselor kesehatan digital',
                'Tingkatkan aktivitas fisik dan waktu di luar ruangan',
            ],
            'Tinggi' => [
                'Atur jadwal penggunaan perangkat secara terstruktur',
                'Gunakan aplikasi pelacak waktu layar',
                'Prioritaskan interaksi langsung dengan orang sekitar',
                'Luangkan waktu untuk hobi offline',
                'Pastikan tidur cukup minimal 7 jam per malam',
            ],
            'Sedang' => [
                'Pertahankan keseimbangan digital yang ada',
                'Monitor penggunaan media sosial setiap minggu',
                'Tambah aktivitas fisik minimal 3 kali seminggu',
                'Kurangi notifikasi yang tidak penting',
                'Coba teknik mindfulness 10 menit per hari',
            ],
            default => [
                'Pertahankan gaya hidup digital yang sehat',
                'Jadilah contoh positif bagi orang sekitar',
                'Tetap aware terhadap perubahan kebiasaan digital',
                'Manfaatkan teknologi secara produktif',
            ],
        };
    }

    /**
     * Tentukan kategori berdasarkan skor
     */
    private function getKategori(int $skor): string
    {
        if ($skor >= 80) return 'Sangat Tinggi';
        if ($skor >= 60) return 'Tinggi';
        if ($skor >= 40) return 'Sedang';
        return 'Rendah';
    }

    /**
     * Generate data dummy kuesioner
     */
    private function generateDummy(): array
    {
        $genders     = ['Laki-laki', 'Perempuan'];
        $regions     = ['Jawa', 'Sumatera', 'Kalimantan', 'Sulawesi', 'Bali', 'Nusa Tenggara'];
        $pendidikan  = ['SMA/SMK', 'D3', 'S1', 'S2', 'S3'];
        $roles       = ['Pelajar', 'Mahasiswa', 'Pekerja', 'Wiraswasta', 'Ibu Rumah Tangga'];
        $pendapatan  = ['< 1 Juta', '1-3 Juta', '3-5 Juta', '5-10 Juta', '> 10 Juta'];
        $kualitasTidur = ['Sangat Buruk', 'Buruk', 'Cukup', 'Baik', 'Sangat Baik'];
        $stresLevel  = ['Sangat Rendah', 'Rendah', 'Sedang', 'Tinggi', 'Sangat Tinggi'];
        $perangkat   = ['Smartphone', 'Laptop', 'Tablet', 'Smartwatch'];

        $data = [];

        for ($i = 1; $i <= 30; $i++) {
            $skor     = rand(20, 95);
            $kategori = $this->getKategori($skor);

            $data[] = [
                'user_id'               => 'USR-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'skor_ketergantungan'   => $skor,
                'kategori'              => $kategori,
                'gender'                => $genders[array_rand($genders)],
                'umur'                  => rand(15, 45),
                'region'                => $regions[array_rand($regions)],
                'tingkat_pendidikan'    => $pendidikan[array_rand($pendidikan)],
                'peran_harian'          => $roles[array_rand($roles)],
                'tingkat_pendapatan'    => $pendapatan[array_rand($pendapatan)],
                'jam_perangkat_per_hari'=> rand(2, 14),
                'buka_hp_per_hari'      => rand(10, 120),
                'notifikasi_per_hari'   => rand(5, 200),
                'menit_medsos'          => rand(30, 360),
                'menit_belajar'         => rand(0, 240),
                'hari_aktif_fisik'      => rand(0, 7),
                'jam_tidur'             => rand(4, 9),
                'kualitas_tidur'        => $kualitasTidur[array_rand($kualitasTidur)],
                'skor_kecemasan'        => rand(0, 21),
                'skor_depresi'          => rand(0, 27),
                'tingkat_stres'         => $stresLevel[array_rand($stresLevel)],
                'skor_kebahagiaan'      => rand(1, 10),
                'jenis_perangkat'       => $perangkat[array_rand($perangkat)],
                'rekomendasi'           => $this->getRekomendasi($kategori),
            ];
        }

        return $data;
    }

    /**
     * Tampilkan halaman data kuesioner
     */
    public function index()
    {
        $kuesioner = $this->generateDummy();

        $regions = ['Jawa', 'Sumatera', 'Kalimantan', 'Sulawesi', 'Bali', 'Nusa Tenggara'];
        $roles   = ['Pelajar', 'Mahasiswa', 'Pekerja', 'Wiraswasta', 'Ibu Rumah Tangga'];

        return view('admin.kuesioner', compact('kuesioner', 'regions', 'roles'));
    }
}