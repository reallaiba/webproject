<?php

namespace Database\Seeders;

use App\Models\Bid;
use App\Models\Category;
use App\Models\Gig;
use App\Models\MarketplaceNotification;
use App\Models\Message;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            ['name' => 'Web Development', 'icon' => 'Code'],
            ['name' => 'Graphic Design', 'icon' => 'Design'],
            ['name' => 'Writing & Content', 'icon' => 'Write'],
            ['name' => 'Digital Marketing', 'icon' => 'Market'],
            ['name' => 'Mobile Apps', 'icon' => 'App'],
            ['name' => 'Data & Analytics', 'icon' => 'Data'],
        ])->map(fn ($category) => Category::updateOrCreate(['name' => $category['name']], $category));

        User::updateOrCreate(
            ['email' => 'admin@freelancehub.com'],
            ['name' => 'Admin', 'password' => Hash::make('admin123'), 'role' => 'admin', 'status' => 'active']
        );

        $client = User::updateOrCreate(
            ['email' => 'client@demo.com'],
            ['name' => 'Demo Client', 'password' => Hash::make('password'), 'role' => 'client', 'status' => 'active', 'location' => 'Lahore']
        );

        $ali = User::updateOrCreate(
            ['email' => 'ali@demo.com'],
            ['name' => 'Ali Khan', 'password' => Hash::make('password'), 'role' => 'freelancer', 'status' => 'active', 'bio' => 'Full-stack web developer.', 'skills' => 'PHP, Laravel, MySQL, JavaScript', 'location' => 'Karachi']
        );

        $sara = User::updateOrCreate(
            ['email' => 'sara@demo.com'],
            ['name' => 'Sara Ahmed', 'password' => Hash::make('password'), 'role' => 'freelancer', 'status' => 'active', 'bio' => 'Designer and brand specialist.', 'skills' => 'UI Design, Logo Design, Branding', 'location' => 'Islamabad']
        );

        $project = Project::updateOrCreate(
            ['client_id' => $client->id, 'title' => 'Need a Website for My Business'],
            ['category_id' => $categories->first()->id, 'description' => 'Build a responsive business website with services, contact form, and admin-friendly content sections.', 'budget' => 500, 'status' => 'open']
        );

        Bid::updateOrCreate(
            ['project_id' => $project->id, 'freelancer_id' => $ali->id],
            ['bid_amount' => 450, 'proposal' => 'I can build this in Laravel with a clean responsive design.', 'status' => 'pending']
        );

        Bid::updateOrCreate(
            ['project_id' => $project->id, 'freelancer_id' => $sara->id],
            ['bid_amount' => 400, 'proposal' => 'I can create a polished site with strong visuals and layout.', 'status' => 'pending']
        );

        Gig::updateOrCreate(
            ['freelancer_id' => $ali->id, 'title' => 'I will build a Laravel website'],
            ['description' => 'Professional Laravel website with database, auth, dashboard, and deployment guidance.', 'price' => 300, 'delivery_days' => 7, 'status' => 'active']
        );

        Gig::updateOrCreate(
            ['freelancer_id' => $sara->id, 'title' => 'I will design a modern logo'],
            ['description' => 'Clean brand-ready logo design with color palette and export files.', 'price' => 80, 'delivery_days' => 3, 'status' => 'active']
        );

        Message::updateOrCreate(
            ['sender_id' => $client->id, 'receiver_id' => $ali->id, 'project_id' => $project->id, 'message' => 'Hi Ali, can you share your timeline for this website?'],
            ['is_read' => false]
        );

        Message::updateOrCreate(
            ['sender_id' => $ali->id, 'receiver_id' => $client->id, 'project_id' => $project->id, 'message' => 'Yes, I can complete the main website in seven days.'],
            ['is_read' => false]
        );

        MarketplaceNotification::updateOrCreate(
            ['user_id' => $client->id, 'type' => 'bid', 'title' => 'Demo Bid Received'],
            ['message' => 'Ali Khan and Sara Ahmed have placed bids on your website project.', 'link' => route('projects.bids', $project, false), 'is_read' => false]
        );

        MarketplaceNotification::updateOrCreate(
            ['user_id' => $ali->id, 'type' => 'message', 'title' => 'Demo Message Received'],
            ['message' => 'Demo Client sent you a message about the website project.', 'link' => route('messages', ['with' => $client->id], false), 'is_read' => false]
        );
    }
}
