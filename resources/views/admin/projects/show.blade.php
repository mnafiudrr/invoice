@extends('layouts.admin')

@section('title', $project->name)

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">{{ $project->name }}</h1>
            <p class="mt-1 text-sm text-gray-600">
                {{ $project->client_company ?? $project->client_name }} &middot; {{ $project->client_name }} &middot; {{ $project->client_email }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.projects.edit', $project) }}"
               class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Edit
            </a>
            <form method="POST" action="{{ route('admin.projects.destroy', $project) }}"
                  onsubmit="return confirm('Delete this project?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-md border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50">
                    Delete
                </button>
            </form>
        </div>
    </div>

    @if (session('new_password'))
        <div class="mt-6 rounded-lg border border-amber-200 bg-amber-50 p-6"
             x-data="{ copied: false }">
            <h2 class="text-sm font-semibold text-amber-800">Project Access Password</h2>
            <p class="mt-1 text-sm text-amber-700">Share this with your client. It is shown only once.</p>
            <div class="mt-3 flex items-center gap-3">
                <code class="rounded bg-white px-3 py-1.5 text-sm font-mono text-amber-900">{{ session('new_password') }}</code>
                <button type="button" @click="navigator.clipboard.writeText('{{ session('new_password') }}'); copied = true"
                        class="rounded-md bg-amber-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-amber-700"
                        x-text="copied ? 'Copied!' : 'Copy Password'"></button>
            </div>
        </div>
    @endif

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-1">
            <div class="rounded-lg bg-white p-6 shadow" x-data="{ linkCopied: false }">
                <h2 class="text-sm font-semibold text-gray-900">Client Share</h2>
                <p class="mt-1 text-sm text-gray-600">URL</p>
                <code class="mt-1 block break-all rounded bg-gray-50 px-3 py-1.5 text-xs text-gray-700">{{ $shareUrl }}</code>
                <button type="button" @click="navigator.clipboard.writeText('{{ $shareUrl }}'); linkCopied = true"
                        class="mt-3 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                        x-text="linkCopied ? 'Copied!' : 'Copy Link'"></button>

                <form method="POST" action="{{ route('admin.projects.regenerate-password', $project) }}" class="mt-6">
                    @csrf
                    <button type="submit"
                            class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Regenerate Password
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="rounded-lg bg-white p-6 shadow">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold">Invoices</h2>
                    <a href="{{ route('admin.invoices.create', ['project_id' => $project->id]) }}" class="text-sm text-indigo-600 hover:underline">New Invoice</a>
                </div>

                @forelse ($project->invoices as $invoice)
                    <div class="mt-4 flex items-center justify-between rounded border p-4">
                        <div>
                            <p class="font-medium">{{ $invoice->invoice_number }}</p>
                            <p class="text-sm text-gray-500">{{ $invoice->issued_at?->format('d M Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium">{{ $invoice->total }}</p>
                            <p class="text-sm text-gray-500">{{ $invoice->status }}</p>
                        </div>
                    </div>
                @empty
                    <p class="mt-4 text-sm text-gray-500">No invoices yet.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection