@extends('layouts.admin')

@section('title', $project->name)

@section('content')
    <x-page-header
        :title="$project->name"
        :subtitle="$project->client_company ?: $project->client_name . ' · ' . $project->client_email">
        <x-slot:actions>
            <x-button href="{{ route('admin.projects.edit', $project) }}" variant="secondary">Edit</x-button>
            <x-modal
                :action="route('admin.projects.destroy', $project)"
                method="DELETE"
                title="Delete project?"
                message="This project and its data will be permanently deleted."
                confirm-label="Delete">
                <x-slot:trigger>
                    <x-button variant="danger" type="button">Delete</x-button>
                </x-slot:trigger>
            </x-modal>
        </x-slot:actions>
    </x-page-header>

    @if (session('new_password'))
        <x-alert type="warning" class="mt-6">
            <p class="font-medium">Project Access Password</p>
            <p class="mt-1">Share this with your client. It is shown only once.</p>
            <div class="mt-3"><x-copy-field :value="session('new_password')" monospace /></div>
        </x-alert>
    @endif

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6">
            <x-card title="Client Share">
                <x-copy-field :value="$shareUrl" :label="'URL'" class="mb-3" />
                <form method="POST" action="{{ route('admin.projects.regenerate-password', $project) }}" class="mt-4">
                    @csrf
                    <x-button variant="secondary" type="submit">Regenerate Password</x-button>
                </form>
            </x-card>
        </div>

        <div class="lg:col-span-2">
            <x-card title="Invoices">
                <x-slot:actions>
                    <a href="{{ route('admin.invoices.create', ['project_id' => $project->id]) }}" class="text-sm text-accent-600 hover:underline">
                        New Invoice
                    </a>
                </x-slot:actions>

                @if ($project->invoices->isEmpty())
                    <x-empty-state title="No invoices yet" body="Create an invoice for this project." />
                @else
                    <x-table responsive>
                        <x-slot:head>
                            <tr>
                                <th class="px-6 py-3 text-xs font-medium uppercase tracking-wider text-gray-500">Number</th>
                                <th class="px-6 py-3 text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Total</th>
                                <th class="px-6 py-3 text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            </tr>
                        </x-slot:head>
                        @foreach ($project->invoices as $invoice)
                            <tr class="table-responsive-row">
                                <td data-label="Number" class="px-6 py-3">
                                    <a href="{{ route('admin.invoices.show', $invoice) }}" class="font-medium text-accent-600 hover:underline">
                                        {{ $invoice->invoice_number }}
                                    </a>
                                </td>
                                <td data-label="Date" class="px-6 py-3 text-sm text-gray-600">{{ format_date($invoice->issued_at) }}</td>
                                <td data-label="Total" class="px-6 py-3 text-right text-sm text-gray-900">{{ format_money($invoice->total, $invoice->currency) }}</td>
                                <td data-label="Status" class="px-6 py-3"><x-status-badge :status="$invoice->status" /></td>
                            </tr>
                        @endforeach
                    </x-table>
                @endif
            </x-card>
        </div>
    </div>
@endsection