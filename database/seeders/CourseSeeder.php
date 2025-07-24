<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            [
                'title' => 'Laravel Fundamentals',
                'description' => 'Learn the basics of Laravel framework including routing, controllers, views, and database interactions.',
                'image' => 'https://via.placeholder.com/640x480/ff6b6b/ffffff?text=Laravel',
                'duration_hours' => 40,
                'active' => true,
            ],
            [
                'title' => 'Advanced PHP Development',
                'description' => 'Deep dive into advanced PHP concepts, design patterns, and best practices for enterprise applications.',
                'image' => 'https://via.placeholder.com/640x480/4ecdc4/ffffff?text=PHP',
                'duration_hours' => 60,
                'active' => true,
            ],
            [
                'title' => 'Vue.js for Beginners',
                'description' => 'Complete introduction to Vue.js framework for building interactive web applications.',
                'image' => 'https://via.placeholder.com/640x480/45b7d1/ffffff?text=Vue.js',
                'duration_hours' => 35,
                'active' => true,
            ],
            [
                'title' => 'Database Design and Optimization',
                'description' => 'Learn how to design efficient databases and optimize query performance.',
                'image' => 'https://via.placeholder.com/640x480/f9ca24/ffffff?text=Database',
                'duration_hours' => 25,
                'active' => false,
            ],
            [
                'title' => 'API Development with Laravel',
                'description' => 'Build robust APIs using Laravel, including authentication, validation, and documentation.',
                'image' => 'https://via.placeholder.com/640x480/6c5ce7/ffffff?text=API',
                'duration_hours' => 45,
                'active' => true,
            ],
        ];

        foreach ($courses as $courseData) {
            Course::create($courseData);
        }
    }
}
