<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImagesTableSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        DB::table('images')->truncate();

        DB::table('images')->insert([
            ['image_id'=>53,'path'=>'jcpyi89isyRNgb8yAAOBRugP8h1SpTn19fi0VQ05.jpg','created_at'=>'2025-08-15','updated_at'=>'2025-08-15','user_id'=>3001],
            ['image_id'=>54,'path'=>'NVUVTZtVDO13PBgA6hJye9ZTBcaW6bRy8KntwXwA.jpg','created_at'=>'2025-08-15','updated_at'=>'2025-08-15','user_id'=>3002],
            ['image_id'=>55,'path'=>'TTpKmEAT779PgmltxMyyr6hFSMMXZuAGMDvWV7J0.jpg','created_at'=>'2025-08-15','updated_at'=>'2025-08-15','user_id'=>3003],
            ['image_id'=>56,'path'=>'eLhGK43veIFlSNTPiHJAOUHXWq7anCWT2f4IQkv7.jpg','created_at'=>'2025-08-15','updated_at'=>'2025-08-15','user_id'=>3004],
            ['image_id'=>57,'path'=>'rRpWzLJTFQ59HG0LglkJFmUFRfWSKHVSNGuy5WjY.jpg','created_at'=>'2025-08-15','updated_at'=>'2025-08-15','user_id'=>3005],
            ['image_id'=>58,'path'=>'WAADVuuqPwfs8KSAoR2g4IDSMERhdwJd6mopZxqa.jpg','created_at'=>'2025-08-15','updated_at'=>'2025-08-15','user_id'=>3006],
            ['image_id'=>59,'path'=>'i6KibDYVxYP27HvsW3JtNyAgVLFj6lEcoCVUGBMN.jpg','created_at'=>'2025-08-15','updated_at'=>'2025-08-15','user_id'=>3007],
            ['image_id'=>60,'path'=>'JAPjBzONLu8QRpF64imqR1Zvx2visj7pm6pcoviv.jpg','created_at'=>'2025-08-15','updated_at'=>'2025-08-15','user_id'=>3008],
            ['image_id'=>61,'path'=>'oSJ1X4GnGWtSEws5JGesjPNYjUjoyrZKT6fA0tgk.jpg','created_at'=>'2025-08-15','updated_at'=>'2025-08-15','user_id'=>3009],
            ['image_id'=>62,'path'=>'ISMXg5e1524cksf8lFjGqMKx0rs4zYeLiUeKZngz.jpg','created_at'=>'2025-08-15','updated_at'=>'2025-08-15','user_id'=>3010],
            ['image_id'=>63,'path'=>'hDT4dRoJjgvC1P6JkbNEz5RFEghUTCKGqFl1LhU7.jpg','created_at'=>'2025-08-15','updated_at'=>'2025-08-15','user_id'=>3011],
        ]);
    }
}
