<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class courses_webinars_blogs extends Seeder
{
/**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed Blogs
        DB::table('blogs')->insert([
            [
                'title' => 'Introduction to Laravel',
                'content' => 'Laravel is a robust PHP framework for modern web applications.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Understanding MVC Architecture',
                'content' => 'MVC is a software design pattern that organizes code into Models, Views, and Controllers.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Database Optimization Tips',
                'content' => 'Learn how to optimize your database queries for better performance.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Seed Webinars
        DB::table('webinars')->insert([
            [
                'title' => 'Laravel 11 New Features',
                'url' => 'https://example.com/webinars/laravel11',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Modern PHP Development',
                'url' => 'https://example.com/webinars/php-development',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Building RESTful APIs',
                'url' => 'https://example.com/webinars/restful-apis',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Seed Courses
        DB::table('courses')->insert([
            [
                'title' => 'Mastering Laravel',
                'description' => 'A comprehensive course to master the Laravel framework.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Advanced PHP Programming',
                'description' => 'Learn advanced PHP programming techniques and concepts.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Database Design Essentials',
                'description' => 'An essential course for designing efficient databases.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
