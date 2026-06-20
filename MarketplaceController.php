<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Earning;
use App\Models\Gig;
use App\Models\MarketplaceNotification;
use App\Models\Message;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Project;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    public function home(): View
    {
        $this->ensureDemoProjectsExist();

        return view('home', [
            'stats' => [
                [
                    'label' => 'Users',
                    'value' => User::where('role', '!=', 'admin')->count(),
                    'help' => 'Clients and freelancers registered in the marketplace.',
                    'link' => route('projects.index'),
                ],
                [
                    'label' => 'Projects',
                    'value' => Project::count(),
                    'help' => 'Posted client projects you can open and review.',
                    'link' => route('projects.index'),
                ],
                [
                    'label' => 'Freelancers',
                    'value' => User::where('role', 'freelancer')->count(),
                    'help' => 'Available freelancers who can bid or receive messages.',
                    'link' => route('gigs.index'),
                ],
                [
                    'label' => 'Completed',
                    'value' => Project::where('status', 'completed')->count(),
                    'help' => 'Finished projects with earnings and reviews.',
                    'link' => route('projects.index', ['status' => 'completed']),
                ],
            ],
            'categories' => Category::orderBy('name')->get(),
            'freelancers' => User::where('role', 'freelancer')->where('status', 'active')->limit(4)->get(),
        ]);
    }

    public function dashboard(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('dashboard', [
            'user' => $user,
            'clientProjects' => $user->isClient() ? Project::withCount('bids')->with('bids.freelancer')->where('client_id', $user->id)->latest()->limit(5)->get() : collect(),
            'freelancerBids' => $user->isFreelancer() ? Bid::with('project.client')->where('freelancer_id', $user->id)->latest()->limit(5)->get() : collect(),
            'recentMessages' => Message::with('sender')->where('receiver_id', $user->id)->latest()->limit(4)->get(),
            'recentNotifications' => MarketplaceNotification::where('user_id', $user->id)->latest()->limit(4)->get(),
        ]);
    }

    public function projects(Request $request): View
    {
        $this->ensureDemoProjectsExist();
        $this->createDemoBidsForOpenProjects();

        $projects = Project::with(['client', 'category', 'hiredFreelancer'])->withCount('bids')
            ->when($request->filled('category'), fn ($query) => $query->where('category_id', $request->integer('category')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->query('status')))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('projects.index', [
            'projects' => $projects,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function createProject(Request $request): View|RedirectResponse
    {
        abort_unless($request->user()->isClient(), 403);
        $this->ensureDemoCategoriesExist();

        return view('projects.create', ['categories' => Category::orderBy('name')->get()]);
    }

    public function storeProject(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isClient(), 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'budget' => ['required', 'numeric', 'min:1'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'attachment' => ['nullable', 'file', 'max:5120', 'mimes:pdf,doc,docx,zip,jpg,jpeg,png'],
        ]);

        if ($request->hasFile('attachment')) {
            $data['attachment_name'] = $request->file('attachment')->getClientOriginalName();
            $data['attachment'] = $request->file('attachment')->store('projects', 'public');
        }

        $data['client_id'] = $request->user()->id;
        $project = Project::create($data);
        $createdBids = $this->createDemoBidsForProject($project);

        User::where('role', 'freelancer')->where('status', 'active')->each(function (User $freelancer) use ($project) {
            $this->notify($freelancer->id, 'project', 'New Project Posted', '"' . $project->title . '" is available for bidding.', route('projects.show', $project, false));
        });

        if ($createdBids > 0) {
            $this->notify($project->client_id, 'bid', 'Bids Received', $createdBids . ' freelancers placed bids on "' . $project->title . '".', route('projects.show', $project, false));
        }

        return redirect()->route('projects.show', $project)->with('success', 'Project posted successfully. Freelancer bids are ready to review.');
    }

    public function showProject(Project $project): View
    {
        if ($project->status === 'open' && $project->bids()->count() === 0) {
            $createdBids = $this->createDemoBidsForProject($project);

            if ($createdBids > 0) {
                $this->notify($project->client_id, 'bid', 'Bids Received', $createdBids . ' freelancers placed bids on "' . $project->title . '".', route('projects.show', $project, false));
            }
        }

        $project->load(['client', 'category', 'hiredFreelancer', 'bids.freelancer']);

        return view('projects.show', ['project' => $project]);
    }

    public function clientProjects(Request $request): View
    {
        abort_unless($request->user()->isClient(), 403);

        return view('projects.mine', [
            'projects' => Project::with(['bids.freelancer'])->withCount('bids')->where('client_id', $request->user()->id)->latest()->get(),
        ]);
    }

    public function bidForm(Request $request, Project $project): View
    {
        abort_unless($request->user()->isFreelancer() && $project->status === 'open', 403);

        return view('bids.create', ['project' => $project]);
    }

    public function storeBid(Request $request, Project $project): RedirectResponse
    {
        abort_unless($request->user()->isFreelancer() && $project->status === 'open', 403);

        $data = $request->validate([
            'bid_amount' => ['required', 'numeric', 'min:1'],
            'proposal' => ['required', 'string'],
        ]);

        Bid::firstOrCreate(
            ['project_id' => $project->id, 'freelancer_id' => $request->user()->id],
            ['bid_amount' => $data['bid_amount'], 'proposal' => $data['proposal']]
        );

        $this->notify($project->client_id, 'bid', 'New Bid Received', $request->user()->name . ' placed a bid on "' . $project->title . '".', route('projects.bids', $project, false));

        return redirect()->route('my.bids')->with('success', 'Bid submitted successfully.');
    }

    public function myBids(Request $request): View
    {
        abort_unless($request->user()->isFreelancer(), 403);

        return view('bids.mine', [
            'bids' => Bid::with('project')->where('freelancer_id', $request->user()->id)->latest()->get(),
        ]);
    }

    public function projectBids(Request $request, Project $project): View
    {
        abort_unless($request->user()->isClient() && $project->client_id === $request->user()->id, 403);

        return view('bids.index', ['project' => $project->load('bids.freelancer')]);
    }

    public function hire(Request $request, Bid $bid): RedirectResponse
    {
        $bid->load('project');
        abort_unless($request->user()->isClient() && $bid->project->client_id === $request->user()->id && $bid->project->status === 'open', 403);

        DB::transaction(function () use ($bid) {
            Bid::where('project_id', $bid->project_id)->where('id', '!=', $bid->id)->update(['status' => 'rejected']);
            $bid->update(['status' => 'accepted']);
            $bid->project->update(['status' => 'in_progress', 'hired_freelancer_id' => $bid->freelancer_id]);
        });

        $this->notify($bid->freelancer_id, 'hire', 'You Have Been Hired', 'You were hired for "' . $bid->project->title . '".', route('projects.show', $bid->project, false));

        return back()->with('success', 'Freelancer hired successfully.');
    }

    public function completeForm(Request $request, Project $project): View
    {
        abort_unless($request->user()->isClient() && $project->client_id === $request->user()->id && $project->status === 'in_progress', 403);

        return view('projects.complete', ['project' => $project->load('hiredFreelancer')]);
    }

    public function complete(Request $request, Project $project): RedirectResponse
    {
        abort_unless($request->user()->isClient() && $project->client_id === $request->user()->id && $project->status === 'in_progress', 403);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($project, $request, $data) {
            $project->update(['status' => 'completed', 'completed_at' => now()]);
            Review::create([
                'project_id' => $project->id,
                'reviewer_id' => $request->user()->id,
                'reviewee_id' => $project->hired_freelancer_id,
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
            ]);
            $amount = Bid::where('project_id', $project->id)->where('status', 'accepted')->value('bid_amount') ?? $project->budget;
            Earning::firstOrCreate(['project_id' => $project->id], ['freelancer_id' => $project->hired_freelancer_id, 'amount' => $amount]);
        });

        $this->notify($project->hired_freelancer_id, 'complete', 'Project Completed', '"' . $project->title . '" was marked completed.', route('projects.show', $project, false));

        return redirect()->route('client.projects')->with('success', 'Project completed and reviewed.');
    }

    public function gigs(): View
    {
        $this->ensureDemoGigsExist();

        return view('gigs.index', ['gigs' => Gig::with('freelancer')->where('status', 'active')->latest()->paginate(12)]);
    }

    public function createGig(Request $request): View
    {
        abort_unless($request->user()->isFreelancer(), 403);

        return view('gigs.create');
    }

    public function storeGig(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isFreelancer(), 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:1'],
            'delivery_days' => ['required', 'integer', 'min:1'],
        ]);

        $data['freelancer_id'] = $request->user()->id;
        Gig::create($data);

        return redirect()->route('my.gigs')->with('success', 'Gig created successfully.');
    }

    public function myGigs(Request $request): View
    {
        abort_unless($request->user()->isFreelancer(), 403);

        return view('gigs.mine', ['gigs' => Gig::where('freelancer_id', $request->user()->id)->latest()->get()]);
    }

    public function addToCart(Request $request, Gig $gig): RedirectResponse
    {
        abort_unless($request->user()->isClient(), 403);

        $gig->load('freelancer');
        $item = CartItem::firstOrNew(['user_id' => $request->user()->id, 'gig_id' => $gig->id]);
        $item->quantity = ($item->exists ? $item->quantity : 0) + 1;
        $item->gig_title = $gig->title;
        $item->freelancer_name = $gig->freelancer->name;
        $item->unit_price = $gig->price;
        $item->line_total = $item->quantity * $gig->price;
        $item->save();

        return redirect()->route('cart')->with('success', 'Gig added to cart.');
    }

    public function cart(Request $request): View
    {
        $this->ensureDemoGigsExist();

        $items = CartItem::with('gig.freelancer')->where('user_id', $request->user()->id)->get();
        $items->whereNull('gig')->each->delete();
        $items = $items->filter->gig->values();

        return view('cart.index', [
            'items' => $items,
            'suggestedGigs' => Gig::with('freelancer')
                ->where('status', 'active')
                ->whereNotIn('id', $items->pluck('gig_id'))
                ->latest()
                ->limit(6)
                ->get(),
        ]);
    }

    public function removeCart(Request $request, CartItem $item): RedirectResponse
    {
        abort_unless($item->user_id === $request->user()->id, 403);
        $item->delete();

        return back()->with('success', 'Item removed.');
    }

    public function checkout(Request $request): View
    {
        $items = CartItem::with('gig')->where('user_id', $request->user()->id)->get();
        abort_if($items->isEmpty(), 404);

        return view('cart.checkout', ['items' => $items]);
    }

    public function pay(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isClient(), 403);

        $items = CartItem::with('gig.freelancer')->where('user_id', $request->user()->id)->get();
        abort_if($items->isEmpty(), 404);
        $data = $request->validate(['payment_method' => ['required', 'in:card,paypal']]);

        $order = DB::transaction(function () use ($items, $request, $data) {
            $order = Order::create([
                'user_id' => $request->user()->id,
                'order_number' => 'ORD-' . now()->format('YmdHis') . '-' . $request->user()->id,
                'customer_name' => $request->user()->name,
                'customer_email' => $request->user()->email,
                'order_summary' => $items->map(fn ($item) => $item->gig_title ?: $item->gig->title)->join(', '),
                'total_amount' => $items->sum(fn ($item) => $item->quantity * $item->gig->price),
                'status' => 'completed',
                'payment_method' => $data['payment_method'],
                'payment_status' => 'paid',
                'transaction_id' => 'TXN' . strtoupper(bin2hex(random_bytes(5))),
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'gig_id' => $item->gig_id,
                    'gig_title' => $item->gig_title ?: $item->gig->title,
                    'freelancer_name' => $item->freelancer_name ?: $item->gig->freelancer->name,
                    'quantity' => $item->quantity,
                    'price' => $item->unit_price ?: $item->gig->price,
                    'line_total' => $item->line_total ?: $item->quantity * $item->gig->price,
                ]);
            }

            CartItem::where('user_id', $request->user()->id)->delete();

            return $order;
        });

        return redirect()->route('orders.show', $order)->with('success', 'Payment successful.');
    }

    public function orders(Request $request): View
    {
        return view('orders.index', ['orders' => Order::where('user_id', $request->user()->id)->latest()->get()]);
    }

    public function order(Order $order, Request $request): View
    {
        abort_unless($order->user_id === $request->user()->id || $request->user()->isAdmin(), 403);

        return view('orders.show', ['order' => $order->load('items.gig.freelancer')]);
    }

    public function messages(Request $request): View
    {
        $with = User::find($request->integer('with'));
        $messages = collect();
        if ($with) {
            Message::where('sender_id', $with->id)->where('receiver_id', $request->user()->id)->update(['is_read' => true]);
            $messages = Message::where(function ($query) use ($request, $with) {
                $query->where('sender_id', $request->user()->id)->where('receiver_id', $with->id);
            })->orWhere(function ($query) use ($request, $with) {
                $query->where('sender_id', $with->id)->where('receiver_id', $request->user()->id);
            })->oldest()->get();
        }

        $contacts = User::where('id', '!=', $request->user()->id)->whereIn('id', function ($query) use ($request) {
            $query->select('sender_id')->from('messages')->where('receiver_id', $request->user()->id)
                ->union(Message::select('receiver_id')->where('sender_id', $request->user()->id));
        })->get();

        $availableUsers = User::where('id', '!=', $request->user()->id)
            ->when($request->user()->isClient(), fn ($query) => $query->where('role', 'freelancer'))
            ->when($request->user()->isFreelancer(), fn ($query) => $query->where('role', 'client'))
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('messages.index', compact('with', 'messages', 'contacts', 'availableUsers'));
    }

    public function sendMessage(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'receiver_id' => ['required', 'exists:users,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'message' => ['required', 'string'],
        ]);

        $data['sender_id'] = $request->user()->id;
        $message = Message::create($data);
        $receiver = User::find($data['receiver_id']);

        $this->notify($data['receiver_id'], 'message', 'New Message', 'You have a new message from ' . $request->user()->name . '.', route('messages', ['with' => $request->user()->id], false));

        if ($receiver && !$receiver->isAdmin()) {
            $replyText = $this->automaticReplyText($receiver, $request->user(), $data['message'], $message->project);
            Message::create([
                'sender_id' => $receiver->id,
                'receiver_id' => $request->user()->id,
                'project_id' => $message->project_id,
                'message' => $replyText,
            ]);
            $this->notify($request->user()->id, 'message', 'Reply Received', $receiver->name . ' replied to your message.', route('messages', ['with' => $receiver->id], false));
        }

        return redirect()->route('messages', ['with' => $data['receiver_id']]);
    }

    public function notifications(Request $request): View
    {
        $notifications = MarketplaceNotification::where('user_id', $request->user()->id)->latest()->get();
        MarketplaceNotification::where('user_id', $request->user()->id)->update(['is_read' => true]);

        return view('notifications.index', compact('notifications'));
    }

    public function profile(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'bio' => ['nullable', 'string'],
            'skills' => ['nullable', 'string', 'max:500'],
            'location' => ['nullable', 'string', 'max:100'],
            'avatar' => ['nullable', 'image', 'max:5120'],
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $request->user()->update($data);

        return back()->with('success', 'Profile updated.');
    }

    public function earnings(Request $request): View
    {
        abort_unless($request->user()->isFreelancer(), 403);

        return view('earnings.index', ['earnings' => Earning::where('freelancer_id', $request->user()->id)->latest()->get()]);
    }

    private function notify(int $userId, string $type, string $title, string $message, ?string $link = null): void
    {
        MarketplaceNotification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
        ]);
    }

    private function createDemoBidsForProject(Project $project): int
    {
        $this->ensureDemoFreelancersExist();

        $freelancers = User::where('role', 'freelancer')
            ->where('status', 'active')
            ->where('id', '!=', $project->client_id)
            ->limit(3)
            ->get();

        $proposals = [
            'I can complete this with a clean responsive design, database setup, and proper testing.',
            'I have experience with similar work and can deliver a professional result on time.',
            'I can build this with clear communication, quality code, and post-delivery support.',
        ];

        $created = 0;
        foreach ($freelancers as $index => $freelancer) {
            $amount = max(1, (float) $project->budget - (($index + 1) * 25));
            $bid = Bid::firstOrCreate(
                ['project_id' => $project->id, 'freelancer_id' => $freelancer->id],
                [
                    'bid_amount' => $amount,
                    'proposal' => $proposals[$index] ?? $proposals[0],
                    'status' => 'pending',
                ]
            );

            if ($bid->wasRecentlyCreated) {
                $created++;
                $this->notify($freelancer->id, 'bid', 'Bid Submitted', 'Your bid was submitted for "' . $project->title . '".', route('projects.show', $project, false));
            }
        }

        return $created;
    }

    private function ensureDemoFreelancersExist(): void
    {
        if (User::where('role', 'freelancer')->where('status', 'active')->count() >= 3) {
            return;
        }

        $freelancers = [
            [
                'name' => 'Ali Raza',
                'email' => 'ali.freelancer@demo.com',
                'bio' => 'Full-stack web and app developer.',
                'skills' => 'Laravel, PHP, MySQL, Mobile Apps',
                'location' => 'Karachi',
            ],
            [
                'name' => 'Sara Khan',
                'email' => 'sara.freelancer@demo.com',
                'bio' => 'UI designer and frontend specialist.',
                'skills' => 'UI Design, React, HTML, CSS',
                'location' => 'Lahore',
            ],
            [
                'name' => 'Hamza Ahmed',
                'email' => 'hamza.freelancer@demo.com',
                'bio' => 'Backend developer with marketplace experience.',
                'skills' => 'APIs, Laravel, Database, Testing',
                'location' => 'Islamabad',
            ],
        ];

        foreach ($freelancers as $freelancer) {
            User::updateOrCreate(
                ['email' => $freelancer['email']],
                $freelancer + [
                    'password' => Hash::make('password'),
                    'role' => 'freelancer',
                    'status' => 'active',
                ]
            );
        }
    }

    private function ensureDemoCategoriesExist(): void
    {
        $categories = [
            ['name' => 'Web Development', 'icon' => 'Code'],
            ['name' => 'Graphic Design', 'icon' => 'Design'],
            ['name' => 'Writing & Content', 'icon' => 'Write'],
            ['name' => 'Digital Marketing', 'icon' => 'Market'],
            ['name' => 'Mobile Apps', 'icon' => 'App'],
            ['name' => 'Data & Analytics', 'icon' => 'Data'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['name' => $category['name']], $category);
        }
    }

    private function ensureDemoProjectsExist(): void
    {
        $this->ensureDemoCategoriesExist();
        $this->ensureDemoFreelancersExist();

        $client = User::updateOrCreate(
            ['email' => 'client@demo.com'],
            [
                'name' => 'Demo Client',
                'password' => Hash::make('password'),
                'role' => 'client',
                'status' => 'active',
                'location' => 'Lahore',
            ]
        );

        $projects = [
            'Web Development' => [
                'title' => 'Need a Website for My Business',
                'description' => 'Build a responsive business website with services, contact form, and admin-friendly content sections.',
                'budget' => 500,
            ],
            'Graphic Design' => [
                'title' => 'Design a Brand Logo and Social Kit',
                'description' => 'Create a modern logo, color palette, and social media profile graphics for a new local business.',
                'budget' => 180,
            ],
            'Writing & Content' => [
                'title' => 'Write Website Content for Service Pages',
                'description' => 'Prepare clear homepage, about, services, and FAQ content with professional tone and SEO-friendly headings.',
                'budget' => 120,
            ],
            'Digital Marketing' => [
                'title' => 'Run a Social Media Marketing Campaign',
                'description' => 'Plan and manage posts, captions, hashtags, and basic ad copy for a one-month promotion campaign.',
                'budget' => 250,
            ],
            'Mobile Apps' => [
                'title' => 'Build a Simple Booking Mobile App',
                'description' => 'Create screens and backend flow for customers to book appointments, view services, and receive updates.',
                'budget' => 700,
            ],
            'Data & Analytics' => [
                'title' => 'Create Sales Dashboard and Reports',
                'description' => 'Clean sales data and build a dashboard with charts for monthly revenue, customers, and product performance.',
                'budget' => 320,
            ],
        ];

        foreach ($projects as $categoryName => $projectData) {
            $category = Category::where('name', $categoryName)->first();

            if (!$category) {
                continue;
            }

            $project = Project::updateOrCreate(
                ['client_id' => $client->id, 'title' => $projectData['title']],
                $projectData + [
                    'category_id' => $category->id,
                    'status' => 'open',
                ]
            );

            if ($project->bids()->count() === 0) {
                $this->createDemoBidsForProject($project);
            }
        }
    }

    private function ensureDemoGigsExist(): void
    {
        $this->ensureDemoFreelancersExist();

        if (Gig::where('status', 'active')->count() >= 3) {
            return;
        }

        $gigs = [
            'ali.freelancer@demo.com' => [
                'title' => 'I will build a responsive Laravel website',
                'description' => 'Complete Laravel website with database, login, dashboard, responsive pages, and clean code.',
                'price' => 300,
                'delivery_days' => 7,
            ],
            'sara.freelancer@demo.com' => [
                'title' => 'I will design modern UI screens',
                'description' => 'Professional UI design for web or mobile app pages with clean layout and user-friendly flow.',
                'price' => 150,
                'delivery_days' => 4,
            ],
            'hamza.freelancer@demo.com' => [
                'title' => 'I will create APIs and backend features',
                'description' => 'Backend APIs, database setup, authentication, testing, and bug fixing for Laravel projects.',
                'price' => 220,
                'delivery_days' => 5,
            ],
        ];

        foreach ($gigs as $email => $gigData) {
            $freelancer = User::where('email', $email)->first();

            if (!$freelancer) {
                continue;
            }

            Gig::updateOrCreate(
                ['freelancer_id' => $freelancer->id, 'title' => $gigData['title']],
                $gigData + ['status' => 'active']
            );
        }
    }

    private function createDemoBidsForOpenProjects(): void
    {
        $this->ensureDemoFreelancersExist();

        Project::where('status', 'open')
            ->whereDoesntHave('bids')
            ->limit(10)
            ->get()
            ->each(function (Project $project) {
                $createdBids = $this->createDemoBidsForProject($project);

                if ($createdBids > 0) {
                    $this->notify($project->client_id, 'bid', 'Bids Ready To Review', $createdBids . ' freelancers placed bids on "' . $project->title . '".', route('projects.show', $project, false));
                }
            });
    }

    private function automaticReplyText(User $sender, User $receiver, string $incomingMessage, ?Project $project = null): string
    {
        $text = strtolower($incomingMessage);
        $projectTitle = $project?->title ? ' "' . $project->title . '"' : '';

        if ($sender->isFreelancer()) {
            if (str_contains($text, 'hello') || str_contains($text, 'hi') || str_contains($text, 'salam')) {
                return 'Hello ' . $receiver->name . ', yes I am available. Please tell me what you need in this project and I will guide you properly.';
            }

            if (str_contains($text, 'when') || str_contains($text, 'time') || str_contains($text, 'timeline') || str_contains($text, 'complete') || str_contains($text, 'delivery')) {
                return 'I can complete your project' . $projectTitle . ' in about 5 to 7 days. First I will confirm your full requirements, then I will build the main work, test it, and share updates with you.';
            }

            if (str_contains($text, 'requirement') || str_contains($text, 'detail') || str_contains($text, 'proper') || str_contains($text, 'need')) {
                return 'Yes, I will work according to your proper requirements. Please share the pages/features you want, design idea, login/payment needs, and any examples. After that I will start with a clear plan.';
            }

            if (str_contains($text, 'price') || str_contains($text, 'budget') || str_contains($text, 'cost') || str_contains($text, 'amount')) {
                return 'My bid amount is shown on the project bid card. If your requirements increase, I will discuss the cost with you first before doing extra work.';
            }

            if (str_contains($text, 'hire') || str_contains($text, 'start')) {
                return 'You can hire me from the project bids page. Once you hire me, I can start the work and keep you updated through messages.';
            }

            if (str_contains($text, 'ok') || str_contains($text, 'thanks') || str_contains($text, 'thank')) {
                return 'You are welcome. I am ready to continue whenever you share the next details.';
            }

            return 'I understand your message. I can help with this project and will follow your instructions. Please share the exact details you want me to handle.';
        }

        if ($sender->isClient()) {
            if (str_contains($text, 'bid') || str_contains($text, 'proposal')) {
                return 'Thanks for your interest. Please send your bid amount, timeline, and a short proposal so I can compare and hire the best freelancer.';
            }

            if (str_contains($text, 'message') || str_contains($text, 'hello') || str_contains($text, 'hi')) {
                return 'Hello ' . $receiver->name . ', thanks for reaching out. Please tell me your experience, expected timeline, and how you will complete this work.';
            }

            return 'Thanks for your message. Please share the details clearly so I can review and reply according to the project needs.';
        }

        return 'Hello, thanks for your message. I will check and reply soon.';
    }
}
