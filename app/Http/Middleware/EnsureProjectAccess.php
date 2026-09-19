<?php

namespace App\Http\Middleware;

use App\Models\Project;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProjectAccess
{
    /**
     * Allow the request only when the client has unlocked this project in the session.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $project = $request->route('project');

        if (! $project instanceof Project) {
            abort(404);
        }

        if (session("project_access.{$project->id}") !== true) {
            return redirect()->route('projects.password', $project);
        }

        return $next($request);
    }
}
