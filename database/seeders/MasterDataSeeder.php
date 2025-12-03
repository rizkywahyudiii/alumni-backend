<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Seed Industries
        $industries = [
            ['name' => 'Technology & Software'],
            ['name' => 'Finance & Banking'],
            ['name' => 'Education & Research'],
            ['name' => 'E-Commerce & Retail'],
            ['name' => 'Healthcare'],
            ['name' => 'Government / BUMN'],
            ['name' => 'Creative & Media'],
            ['name' => 'Manufacturing'],
        ];

        // Insert and keep IDs for referencing companies later if needed
        DB::table('industries')->insert($industries);

        // Get Industry IDs for mapping
        $techId = DB::table('industries')->where('name', 'Technology & Software')->value('id');
        $bankId = DB::table('industries')->where('name', 'Finance & Banking')->value('id');
        $ecomId = DB::table('industries')->where('name', 'E-Commerce & Retail')->value('id');

        // 2. Seed Skills (Hard Skills & Soft Skills)
        $skills = [
            // Technical / Hard Skills
            ['name' => 'Laravel', 'category' => 'Technical'],
            ['name' => 'React.js', 'category' => 'Technical'],
            ['name' => 'Node.js', 'category' => 'Technical'],
            ['name' => 'Python', 'category' => 'Technical'],
            ['name' => 'Data Analysis', 'category' => 'Technical'],
            ['name' => 'UI/UX Design', 'category' => 'Technical'],
            ['name' => 'SQL/Database', 'category' => 'Technical'],
            ['name' => 'Cloud Computing (AWS/GCP)', 'category' => 'Technical'],

            // Soft Skills
            ['name' => 'Leadership', 'category' => 'Soft Skill'],
            ['name' => 'Public Speaking', 'category' => 'Soft Skill'],
            ['name' => 'Project Management', 'category' => 'Soft Skill'],
            ['name' => 'Critical Thinking', 'category' => 'Soft Skill'],
            ['name' => 'English Proficiency', 'category' => 'Language'],
        ];

        DB::table('skills')->insert(array_map(function($skill) use ($now) {
            return array_merge($skill, ['created_at' => $now, 'updated_at' => $now]);
        }, $skills));

        // 3. Seed Top Companies (Verified)
        $companies = [
            [
                'name' => 'GoTo Group (Gojek Tokopedia)',
                'industry_id' => $techId,
                'location' => 'Jakarta',
                'website' => 'https://www.gotocompany.com',
                'is_verified' => true
            ],
            [
                'name' => 'Bank Mandiri',
                'industry_id' => $bankId,
                'location' => 'Jakarta',
                'website' => 'https://www.bankmandiri.co.id',
                'is_verified' => true
            ],
            [
                'name' => 'Shopee International Indonesia',
                'industry_id' => $ecomId,
                'location' => 'Jakarta',
                'website' => 'https://shopee.co.id',
                'is_verified' => true
            ],
            [
                'name' => 'Telkom Indonesia',
                'industry_id' => $techId,
                'location' => 'Bandung',
                'website' => 'https://www.telkom.co.id',
                'is_verified' => true
            ],
        ];

        DB::table('companies')->insert(array_map(function($company) use ($now) {
            return array_merge($company, ['created_at' => $now, 'updated_at' => $now]);
        }, $companies));
    }
}
