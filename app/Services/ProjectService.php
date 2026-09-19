<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProjectService
{
    public function create(array $data): Project
    {
        $project = Project::create([
            'user_id' => auth()->id(),
            'name' => $data['name'],
            'slug' => $this->uniqueSlug($data['name']),
            'client_name' => $data['client_name'],
            'client_email' => $data['client_email'],
            'client_company' => $data['client_company'] ?? null,
            'description' => $data['description'] ?? null,
            'access_password_hash' => Hash::make($data['password']),
        ]);

        return $project;
    }

    public function update(Project $project, array $data): Project
    {
        $project->update([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'client_name' => $data['client_name'],
            'client_email' => $data['client_email'],
            'client_company' => $data['client_company'] ?? null,
            'description' => $data['description'] ?? null,
        ]);

        if (! empty($data['password'])) {
            $project->update([
                'access_password_hash' => Hash::make($data['password']),
            ]);
        }

        return $project->refresh();
    }

    public function regeneratePassword(Project $project, string $password): Project
    {
        $project->update([
            'access_password_hash' => Hash::make($password),
        ]);

        return $project->refresh();
    }

    public function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $suffix = 2;

        while (Project::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    public function delete(Project $project): void
    {
        $project->delete();
    }
}
