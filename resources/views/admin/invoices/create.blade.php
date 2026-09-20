@extends('layouts.admin')

@section('title', 'New Invoice')

@section('content')
    <x-page-header title="New Invoice" subtitle="Create an invoice for a project" />

    <form method="POST" action="{{ route('admin.invoices.store') }}" class="mt-6 max-w-3xl space-y-6"
          x-data="{ submitting: false }" @submit="submitting = true">
        @csrf
        @include('admin.invoices._form')

        <div class="flex items-center gap-3">
            <x-button type="submit" x-bind:disabled="submitting">
                <span x-show="submitting" x-cloak>Creating…</span>
                <span x-show="!submitting">Create Invoice</span>
            </x-button>
            <a href="{{ route('admin.invoices.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
        </div>
    </form>
@endsection