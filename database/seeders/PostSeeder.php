<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure a user exists
        $userId = DB::table('users')->first()?->id ?? DB::table('users')->insertGetId([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $posts = [
            [
                'title' => 'Why I Spent 6 Hours Debugging a Missing Semicolon',
                'content' => 'It was a dark and stormy night when I realized... the semicolon was there all along. I had just been looking at the wrong line. For 6 hours. Send coffee.',
                'views' => 1240,
            ],
            [
                'title' => 'The Infinite Loop That Changed My Life',
                'content' => 'While (true) I searched for meaning. And then my laptop caught fire. Metaphorically speaking. But also literally. The fan was broken.',
                'views' => 892,
            ],
            [
                'title' => 'CSS: Cascading Sheets of Suffering',
                'content' => 'I tried to center a div. It took 3 days. Then I discovered flexbox. Then I cried tears of joy. Then I discovered I could use grid. Then I cried again.',
                'views' => 5430,
            ],
            [
                'title' => 'My Keyboard Has More Coffee Than Code',
                'content' => 'Statistics show that 87% of keyboard spills happen during production deploys. The other 13%? Those are the ones that actually destroyed the laptop.',
                'views' => 2100,
            ],
            [
                'title' => 'Stack Overflow: The Programmer\'s Best Friend',
                'content' => 'I have copied and pasted code so many times, I\'m pretty sure I own half of Stack Overflow by now. No, I don\'t understand what it does. But it works!',
                'views' => 3890,
            ],
            [
                'title' => 'Git Commits: A Love Story',
                'content' => '"Fixed bug" - narrator voice: It was not, in fact, fixed. Twenty-seven commits later, we finally discovered the bug was in production all along.',
                'views' => 4210,
            ],
            [
                'title' => 'The Meeting That Could Have Been an Email',
                'content' => 'We spent 45 minutes discussing a 5-minute solution. Then it took 3 days to implement. Classic.',
                'views' => 6780,
            ],
        ];

        foreach ($posts as $post) {
            DB::table('posts')->insert([
                'user_id' => $userId,
                'title' => $post['title'],
                'content' => $post['content'],
                'slug' => Str::slug($post['title']),
                'views' => $post['views'],
                'is_published' => true,
                'published_at' => now()->subDays(rand(1, 30)),
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(0, 20)),
            ]);
        }
    }
}
