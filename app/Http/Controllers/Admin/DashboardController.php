<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Project;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'projectCount' => Project::count(),
            'invoiceCount' => Invoice::count(),
            'paidCount' => Invoice::where('status', Invoice::STATUS_PAID)->count(),
            'unpaidCount' => Invoice::whereIn('status', [Invoice::STATUS_DRAFT, Invoice::STATUS_SENT])->count(),
            'recentInvoices' => Invoice::with('project')
                ->latest()
                ->take(10)
                ->get(),
        ]);
    }
}
