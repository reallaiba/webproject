<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Gig;
use App\Models\MarketplaceNotification;
use App\Models\Message;
use App\Models\Order;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminController extends Controller
{
    private function guard(Request $request): ?RedirectResponse
    {
        $sessionAdminId = $request->session()->get('admin_user_id');
        $sessionAdmin = $sessionAdminId
            ? User::where('id', $sessionAdminId)->where('role', 'admin')->where('status', 'active')->exists()
            : false;

        if ($request->user()?->isAdmin()) {
            $request->session()->put('admin_user_id', $request->user()->id);

            return null;
        }

        if ($sessionAdmin) {
            return null;
        }

        Auth::logout();

        return redirect()->route('login', ['admin' => 1])->with('error', 'Please login as admin first.');
    }

    public function dashboard(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }

        return view('admin.dashboard', [
            'stats' => [
                'users' => User::where('role', '!=', 'admin')->count(),
                'projects' => Project::count(),
                'gigs' => Gig::count(),
                'bids' => Bid::count(),
                'orders' => Order::count(),
                'messages' => Message::count(),
                'alerts' => MarketplaceNotification::count(),
            ],
            'recentProjects' => Project::withCount('bids')->with('client')->latest()->limit(5)->get(),
            'recentBids' => Bid::with('project', 'freelancer')->latest()->limit(5)->get(),
            'recentUsers' => User::where('role', '!=', 'admin')->latest()->limit(5)->get(),
            'recentMessages' => Message::with(['sender', 'receiver', 'project'])->latest()->limit(5)->get(),
            'recentAlerts' => MarketplaceNotification::with('user')->latest()->limit(5)->get(),
        ]);
    }

    public function users(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }
        return view('admin.users', ['users' => User::latest()->get()]);
    }

    public function toggleUser(Request $request, User $user): RedirectResponse
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }
        abort_if($user->isAdmin(), 403);
        $user->update(['status' => $user->status === 'active' ? 'blocked' : 'active']);
        return back()->with('success', 'User status updated.');
    }

    public function projects(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }
        return view('admin.projects', ['projects' => Project::with('client', 'hiredFreelancer')->withCount('bids')->latest()->get()]);
    }

    public function gigs(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }
        return view('admin.gigs', ['gigs' => Gig::with('freelancer')->latest()->get()]);
    }

    public function toggleGig(Request $request, Gig $gig): RedirectResponse
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }
        $gig->update(['status' => $gig->status === 'active' ? 'inactive' : 'active']);
        return back()->with('success', 'Gig status updated.');
    }

    public function orders(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }
        return view('admin.orders', ['orders' => Order::with('user')->latest()->get()]);
    }

    public function messages(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }
        return view('admin.messages', [
            'messages' => Message::with(['sender', 'receiver', 'project'])->latest()->paginate(20),
        ]);
    }

    public function alerts(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }
        return view('admin.alerts', [
            'alerts' => MarketplaceNotification::with('user')->latest()->get(),
        ]);
    }

    public function reports(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }
        return view('admin.reports', [
            'revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'users' => User::where('role', '!=', 'admin')->count(),
            'projects' => Project::count(),
            'completed' => Project::where('status', 'completed')->count(),
            'bids' => Bid::count(),
            'messages' => Message::count(),
            'alerts' => MarketplaceNotification::count(),
        ]);
    }
}
