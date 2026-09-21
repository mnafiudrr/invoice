@extends('layouts.admin')

@section('title', 'Edit Invoice')

@section('content')
    <x-page-header title="Edit Invoice" subtitle="{{ $invoice->invoice_number }}">
        <x-slot:actions>
            <x-button href="{{ route('admin.invoices.show', $invoice) }}" variant="secondary">View Invoice</x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('admin.invoices.update', $invoice) }}" class="mt-6 max-w-3xl space-y-6"
          x-data="{ submitting: false }" @submit="submitting = true">
        @csrf
        @method('PUT')
        @include('admin.invoices._form', ['invoice' => $invoice, 'project' => $project])

        <div class="flex items-center gap-3">
            <x-button type="submit" x-bind:disabled="submitting">
                <span x-show="submitting" x-cloak>Updating…</span>
                <span x-show="!submitting">Update Invoice</span>
            </x-button>
            <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
        </div>
    </form>
@endsection