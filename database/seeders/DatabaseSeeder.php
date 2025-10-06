<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\GlobalService;
use App\Models\InitialServices;
use App\Models\Service;
use App\Models\ServiceReview;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->count(15)->create();
        Category::factory()->count(15)->create();
        GlobalService::factory()->count(15)->create();
        InitialServices::factory()->count(15)->create();
        Service::factory()->count(15)->create();
        ServiceReview::factory()->count(15)->create();
        Conversation::factory()->count(10)->create();
        ChatMessage::factory()->count(50)->create();
    }
}
