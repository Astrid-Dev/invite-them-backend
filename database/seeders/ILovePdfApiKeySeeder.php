<?php

namespace Database\Seeders;

use App\Models\ILovePdfApiKey;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ILovePdfApiKeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ILovePdfApiKey::query()
            ->insert([
                [
                    'public_key' => 'project_public_7a579f9f12f28109cf6ddb28bde56804_-EiFKd5f700b92cc1778d3affadf0c73c35bc',
                    'secret_key' => 'secret_key_c3b0f14bd5a9b52893cee473f3ac8c16_rKqhaf2b40c8b66a524a50cf3d8719b51117e',
                    'remaining_files' => 250
                ],
                [
                    'public_key' => 'project_public_964ad2c3e769b219e2243ccf7dc96a34_uVPE246143d6d75e2dcb441713cc00bc36838',
                    'secret_key' => 'secret_key_311ba69d1ffd778693831c4029985ba9_Jm-Vxb03eb0905d5b4731c9cd813651c39cc5',
                    'remaining_files' => 250
                ],
                [
                    'public_key' => 'project_public_761f50b55ccc1cc5c03a38f5a76d0ad6_-wbbB9222e4c139b073cc88f9897dbac101f0',
                    'secret_key' => 'secret_key_77968b52d11045ff8206c426b7f045b0_30aP_075d24de4ed43c11b5706924dc97f0c7',
                    'remaining_files' => 250
                ]
            ]);
    }
}
