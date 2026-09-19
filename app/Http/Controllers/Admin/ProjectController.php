<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(private ProjectService $projectService) {}

    public function index(): View
    {
        $projects = Project::withCount('invoices')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.projects.index', ['projects' => $projects]);
    }

    public function create(): View
    {
        return view('admin.projects.create');
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $project = $this->projectService->create($request->validated());

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('new_password', $request->validated()['password'])
            ->with('success', 'Project created.');
    }

    public function show(Project $project): View
    {
        return view('admin.projects.show', [
            'project' => $project->load('invoices'),
            'shareUrl' => route('projects.show', $project),
        ]);
    }

    public function edit(Project $project): View
    {
        return view('admin.projects.edit', ['project' => $project]);
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $this->projectService->update($project, $request->validated());

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('success', 'Project updated.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        if ($project->invoices()->exists()) {
            return redirect()
                ->route('admin.projects.show', $project)
                ->with('error', 'Cannot delete a project that has invoices.');
        }

        $this->projectService->delete($project);

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Project deleted.');
    }

    public function regeneratePassword(Request $request, Project $project): RedirectResponse
    {
        $password = Str::random(12);

        $this->projectService->regeneratePassword($project, $password);

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('new_password', $password)
            ->with('success', 'Project password regenerated.');
    }
}
