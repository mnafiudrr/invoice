@php
    $project = $project ?? null;
@endphp

<div class="space-y-6">
    <x-card title="Project">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <x-input name="name" :label="'Project name'" :required="true" :value="old('name', $project?->name)" />
            @if ($project)
                <x-input name="slug" :label="'Slug'" :required="true" :value="old('slug', $project->slug)"
                         hint="Used in the share URL." />
            @endif
            <div class="sm:col-span-2">
                <x-textarea name="description" :label="'Description'" rows="3"
                            :value="old('description', $project?->description)" />
            </div>
            <x-input name="password" type="password" :label="$project ? 'New access password' : 'Access password'" :required="! $project"
                     :hint="$project ? 'Leave blank to keep the current password.' : 'Shared with clients to open this project.'" />
        </div>
    </x-card>

    <x-card title="Client">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <x-input name="client_name" :label="'Client name'" :required="true" :value="old('client_name', $project?->client_name)" />
            <x-input name="client_email" type="email" :label="'Client email'" :required="true" :value="old('client_email', $project?->client_email)" />
            <x-input name="client_company" :label="'Client company'" :value="old('client_company', $project?->client_company)" />
        </div>
    </x-card>
</div>