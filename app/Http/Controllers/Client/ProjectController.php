<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function show(Project $project): View
    {
        if (session("project_access.{$project->id}") !== true) {
            return view('public.password', ['project' => $project]);
        }

        return view('public.project', [
            'project' => $project,
            'invoices' => $project->invoices()
                ->with('files', 'payments')
                ->orderByDesc('issued_at')
                ->get(),
        ]);
    }

    public function showPassword(Project $project): View
    {
        return view('public.password', ['project' => $project]);
    }

    public function checkPassword(Request $request, Project $project): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Hash::check($request->string('password'), $project->access_password_hash)) {
            return back()
                ->withErrors(['password' => 'Incorrect password.'])
                ->onlyInput('password');
        }

        session(["project_access.{$project->id}" => true]);

        return redirect()->route('projects.show', $project);
    }
}
