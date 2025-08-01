<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coursesData = [
            [
                'title' => 'Laravel Fundamentals',
                'description' => 'Learn the basics of Laravel framework including routing, controllers, views, and database interactions.',
                'image' => 'https://via.placeholder.com/640x480/ff6b6b/ffffff?text=Laravel',
                'duration_hours' => 40,
                'active' => true,
                'modules' => [
                    [
                        'title' => 'Getting Started with Laravel',
                        'description' => 'Introduction to Laravel framework, installation, and basic concepts',
                        'lessons' => [
                            [
                                'title' => 'What is Laravel?',
                                'description' => 'Understanding the Laravel framework and its benefits',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=ImtZ5yENzgE',
                                'duration_minutes' => 15,
                            ],
                            [
                                'title' => 'Installing Laravel',
                                'description' => 'Step-by-step guide to install Laravel',
                                'type' => 'text',
                                'content' => 'Laravel can be installed using Composer. First, make sure you have Composer installed on your system. Then run: composer create-project laravel/laravel my-project',
                                'duration_minutes' => 10,
                            ],
                            [
                                'title' => 'Laravel Directory Structure',
                                'description' => 'Understanding Laravel\'s directory structure and organization',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=pTNy0YjndnU',
                                'duration_minutes' => 20,
                            ],
                        ],
                    ],
                    [
                        'title' => 'Routing and Controllers',
                        'description' => 'Learn about Laravel routing system and controllers',
                        'lessons' => [
                            [
                                'title' => 'Basic Routing',
                                'description' => 'Creating basic routes in Laravel',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=rIfdg_Ot-LI',
                                'duration_minutes' => 25,
                            ],
                            [
                                'title' => 'Route Parameters',
                                'description' => 'Working with route parameters and constraints',
                                'type' => 'text',
                                'content' => 'Route parameters are defined by wrapping the parameter name in curly braces: Route::get(\'/user/{id}\', function ($id) { return \'User \'.$id; });',
                                'duration_minutes' => 15,
                            ],
                            [
                                'title' => 'Creating Controllers',
                                'description' => 'How to create and use controllers in Laravel',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=qqKOAm_Zmrg',
                                'duration_minutes' => 30,
                            ],
                        ],
                    ],
                    [
                        'title' => 'Views and Blade Templates',
                        'description' => 'Working with views and the Blade templating engine',
                        'lessons' => [
                            [
                                'title' => 'Creating Views',
                                'description' => 'How to create and return views in Laravel',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=gumHSV3ot4E',
                                'duration_minutes' => 20,
                            ],
                            [
                                'title' => 'Blade Syntax',
                                'description' => 'Understanding Blade templating syntax',
                                'type' => 'text',
                                'content' => 'Blade is Laravel\'s templating engine. Use {{ $variable }} to display data, @if for conditionals, and @foreach for loops.',
                                'duration_minutes' => 25,
                            ],
                            [
                                'title' => 'Layout and Components',
                                'description' => 'Creating layouts and reusable components with Blade',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=7amLV3VSLjM',
                                'duration_minutes' => 35,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Advanced PHP Development',
                'description' => 'Deep dive into advanced PHP concepts, design patterns, and best practices for enterprise applications.',
                'image' => 'https://via.placeholder.com/640x480/4ecdc4/ffffff?text=PHP',
                'duration_hours' => 60,
                'active' => true,
                'modules' => [
                    [
                        'title' => 'Object-Oriented Programming',
                        'description' => 'Advanced OOP concepts in PHP',
                        'lessons' => [
                            [
                                'title' => 'Classes and Objects',
                                'description' => 'Understanding classes, objects, and instantiation',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=Anz0ArcQ5kI',
                                'duration_minutes' => 30,
                            ],
                            [
                                'title' => 'Inheritance and Polymorphism',
                                'description' => 'Working with inheritance and polymorphic behavior',
                                'type' => 'text',
                                'content' => 'Inheritance allows classes to inherit properties and methods from parent classes. Use the extends keyword to create child classes.',
                                'duration_minutes' => 25,
                            ],
                            [
                                'title' => 'Interfaces and Abstract Classes',
                                'description' => 'Implementing contracts with interfaces and abstract classes',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=EU7PRmCpx-0',
                                'duration_minutes' => 40,
                            ],
                        ],
                    ],
                    [
                        'title' => 'Design Patterns',
                        'description' => 'Common design patterns in PHP development',
                        'lessons' => [
                            [
                                'title' => 'Singleton Pattern',
                                'description' => 'Implementing the Singleton design pattern',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=hUE_j6q0LTQ',
                                'duration_minutes' => 20,
                            ],
                            [
                                'title' => 'Factory Pattern',
                                'description' => 'Creating objects using the Factory pattern',
                                'type' => 'text',
                                'content' => 'The Factory pattern provides an interface for creating objects without specifying their exact classes.',
                                'duration_minutes' => 15,
                            ],
                            [
                                'title' => 'Observer Pattern',
                                'description' => 'Implementing the Observer pattern for event handling',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=_BpmfnqjgzQ',
                                'duration_minutes' => 35,
                            ],
                        ],
                    ],
                    [
                        'title' => 'Performance Optimization',
                        'description' => 'Techniques for optimizing PHP application performance',
                        'lessons' => [
                            [
                                'title' => 'Code Profiling',
                                'description' => 'How to profile and analyze PHP code performance',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=e4Kog2QKofE',
                                'duration_minutes' => 25,
                            ],
                            [
                                'title' => 'Caching Strategies',
                                'description' => 'Implementing effective caching mechanisms',
                                'type' => 'text',
                                'content' => 'Caching can significantly improve application performance by storing frequently accessed data in memory.',
                                'duration_minutes' => 20,
                            ],
                            [
                                'title' => 'Database Optimization',
                                'description' => 'Optimizing database queries and connections',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=BHwzDmr6d7s',
                                'duration_minutes' => 30,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Vue.js for Beginners',
                'description' => 'Complete introduction to Vue.js framework for building interactive web applications.',
                'image' => 'https://via.placeholder.com/640x480/45b7d1/ffffff?text=Vue.js',
                'duration_hours' => 35,
                'active' => true,
                'modules' => [
                    [
                        'title' => 'Vue.js Fundamentals',
                        'description' => 'Getting started with Vue.js framework',
                        'lessons' => [
                            [
                                'title' => 'Introduction to Vue.js',
                                'description' => 'What is Vue.js and why use it?',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=nhBVL41-_Cw',
                                'duration_minutes' => 15,
                            ],
                            [
                                'title' => 'Vue Instance and Data Binding',
                                'description' => 'Creating Vue instances and understanding data binding',
                                'type' => 'text',
                                'content' => 'Vue.js uses declarative rendering to bind data to the DOM. Use {{ }} for text interpolation and v-bind for attribute binding.',
                                'duration_minutes' => 20,
                            ],
                            [
                                'title' => 'Directives and Event Handling',
                                'description' => 'Working with Vue directives and handling events',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=5LYrN_cAJoA',
                                'duration_minutes' => 25,
                            ],
                        ],
                    ],
                    [
                        'title' => 'Components and Props',
                        'description' => 'Building reusable components in Vue.js',
                        'lessons' => [
                            [
                                'title' => 'Creating Components',
                                'description' => 'How to create and register Vue components',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=cjXXOWlKiMw',
                                'duration_minutes' => 30,
                            ],
                            [
                                'title' => 'Props and Communication',
                                'description' => 'Passing data between parent and child components',
                                'type' => 'text',
                                'content' => 'Props are custom attributes you can register on a component. Use props to pass data from parent to child components.',
                                'duration_minutes' => 18,
                            ],
                            [
                                'title' => 'Component Lifecycle',
                                'description' => 'Understanding Vue component lifecycle hooks',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=BQ67DkYBWcI',
                                'duration_minutes' => 22,
                            ],
                        ],
                    ],
                    [
                        'title' => 'Vue Router and State Management',
                        'description' => 'Routing and state management in Vue applications',
                        'lessons' => [
                            [
                                'title' => 'Setting up Vue Router',
                                'description' => 'Installing and configuring Vue Router',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=juocv4AtrHo',
                                'duration_minutes' => 25,
                            ],
                            [
                                'title' => 'Route Parameters and Guards',
                                'description' => 'Working with dynamic routes and navigation guards',
                                'type' => 'text',
                                'content' => 'Dynamic routes use parameters to match multiple URLs. Navigation guards control access to routes.',
                                'duration_minutes' => 20,
                            ],
                            [
                                'title' => 'Introduction to Vuex',
                                'description' => 'Managing application state with Vuex',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=5lVQgZzLMHc',
                                'duration_minutes' => 35,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Database Design and Optimization',
                'description' => 'Learn how to design efficient databases and optimize query performance.',
                'image' => 'https://via.placeholder.com/640x480/f9ca24/ffffff?text=Database',
                'duration_hours' => 25,
                'active' => false,
                'modules' => [
                    [
                        'title' => 'Database Design Principles',
                        'description' => 'Fundamentals of database design and normalization',
                        'lessons' => [
                            [
                                'title' => 'Entity-Relationship Modeling',
                                'description' => 'Creating ER diagrams and understanding relationships',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=QpdhBUYk7Kk',
                                'duration_minutes' => 30,
                            ],
                            [
                                'title' => 'Normalization',
                                'description' => 'Understanding database normalization forms',
                                'type' => 'text',
                                'content' => 'Normalization is the process of organizing data to reduce redundancy and improve data integrity. It involves dividing tables and establishing relationships.',
                                'duration_minutes' => 25,
                            ],
                            [
                                'title' => 'Schema Design Best Practices',
                                'description' => 'Best practices for designing database schemas',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=ztHopE5Wnpc',
                                'duration_minutes' => 20,
                            ],
                        ],
                    ],
                    [
                        'title' => 'Query Optimization',
                        'description' => 'Techniques for optimizing database queries',
                        'lessons' => [
                            [
                                'title' => 'Understanding Indexes',
                                'description' => 'How to create and use database indexes effectively',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=ITcOiLSfVJQ',
                                'duration_minutes' => 28,
                            ],
                            [
                                'title' => 'Query Execution Plans',
                                'description' => 'Reading and analyzing query execution plans',
                                'type' => 'text',
                                'content' => 'Execution plans show how the database engine executes queries. Use EXPLAIN to analyze query performance.',
                                'duration_minutes' => 22,
                            ],
                            [
                                'title' => 'Advanced Query Techniques',
                                'description' => 'Writing efficient complex queries',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=BuRSRbsJCNE',
                                'duration_minutes' => 35,
                            ],
                        ],
                    ],
                    [
                        'title' => 'Performance Monitoring',
                        'description' => 'Monitoring and maintaining database performance',
                        'lessons' => [
                            [
                                'title' => 'Performance Metrics',
                                'description' => 'Key metrics for database performance monitoring',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=3IIvwOh-HaE',
                                'duration_minutes' => 20,
                            ],
                            [
                                'title' => 'Database Maintenance',
                                'description' => 'Regular maintenance tasks for optimal performance',
                                'type' => 'text',
                                'content' => 'Regular maintenance includes updating statistics, rebuilding indexes, and monitoring log file growth.',
                                'duration_minutes' => 15,
                            ],
                            [
                                'title' => 'Troubleshooting Performance Issues',
                                'description' => 'Identifying and resolving common performance problems',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=f1VwbDDVT-c',
                                'duration_minutes' => 30,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'API Development with Laravel',
                'description' => 'Build robust APIs using Laravel, including authentication, validation, and documentation.',
                'image' => 'https://via.placeholder.com/640x480/6c5ce7/ffffff?text=API',
                'duration_hours' => 45,
                'active' => true,
                'modules' => [
                    [
                        'title' => 'RESTful API Basics',
                        'description' => 'Understanding REST principles and API design',
                        'lessons' => [
                            [
                                'title' => 'REST Principles',
                                'description' => 'Understanding RESTful architecture principles',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=7YcW25PHnAA',
                                'duration_minutes' => 25,
                            ],
                            [
                                'title' => 'HTTP Status Codes',
                                'description' => 'Proper use of HTTP status codes in APIs',
                                'type' => 'text',
                                'content' => 'HTTP status codes communicate the result of an API request: 200 for success, 404 for not found, 500 for server errors.',
                                'duration_minutes' => 15,
                            ],
                            [
                                'title' => 'API Versioning',
                                'description' => 'Strategies for versioning your APIs',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=iEaM1OJ2SeQ',
                                'duration_minutes' => 20,
                            ],
                        ],
                    ],
                    [
                        'title' => 'Laravel API Resources',
                        'description' => 'Creating and managing API resources in Laravel',
                        'lessons' => [
                            [
                                'title' => 'API Controllers',
                                'description' => 'Creating controllers for API endpoints',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=TTK8uQOjpT0',
                                'duration_minutes' => 30,
                            ],
                            [
                                'title' => 'Resource Classes',
                                'description' => 'Using Laravel resource classes for data transformation',
                                'type' => 'text',
                                'content' => 'Resource classes provide a convenient way to transform models into JSON responses with consistent formatting.',
                                'duration_minutes' => 25,
                            ],
                            [
                                'title' => 'Validation and Error Handling',
                                'description' => 'Implementing proper validation and error responses',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=7Q7E8C8BuEg',
                                'duration_minutes' => 35,
                            ],
                        ],
                    ],
                    [
                        'title' => 'API Authentication',
                        'description' => 'Implementing authentication for your APIs',
                        'lessons' => [
                            [
                                'title' => 'Token-based Authentication',
                                'description' => 'Implementing token authentication with Laravel Sanctum',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=TzAJfjCn7Ks',
                                'duration_minutes' => 40,
                            ],
                            [
                                'title' => 'JWT Authentication',
                                'description' => 'Working with JSON Web Tokens for API authentication',
                                'type' => 'text',
                                'content' => 'JWT tokens are self-contained and include user information. They\'re stateless and don\'t require server-side session storage.',
                                'duration_minutes' => 30,
                            ],
                            [
                                'title' => 'API Rate Limiting',
                                'description' => 'Implementing rate limiting to protect your APIs',
                                'type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=RqvCm4blAzs',
                                'duration_minutes' => 25,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($coursesData as $courseData) {
            $modules = $courseData['modules'];
            unset($courseData['modules']);

            $course = Course::create($courseData);

            foreach ($modules as $moduleIndex => $moduleData) {
                $lessons = $moduleData['lessons'];
                unset($moduleData['lessons']);

                $module = Module::create([
                    'course_id' => $course->id,
                    'title' => $moduleData['title'],
                    'description' => $moduleData['description'],
                    'order' => $moduleIndex + 1,
                ]);

                foreach ($lessons as $lessonIndex => $lessonData) {
                    Lesson::create([
                        'module_id' => $module->id,
                        'title' => $lessonData['title'],
                        'description' => $lessonData['description'],
                        'type' => $lessonData['type'],
                        'content' => $lessonData['content'] ?? null,
                        'video_url' => $lessonData['video_url'] ?? null,
                        'duration_minutes' => $lessonData['duration_minutes'],
                        'order' => $lessonIndex + 1,
                    ]);
                }
            }
        }
    }
}
