@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>

    <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg bg-white p-6 shadow">
            <p class="text-sm font-medium text-gray-500">Projects</p>
            <p class="mt-2 text-3xl font-semibold">0</p>
        </div>
        <div class="rounded-lg bg-white p-6 shadow">
            <p class="text-sm font-medium text-gray-500">Invoices</p>
            <p class="mt-2 text-3xl font-semibold">0</p>
        </div>
        <div class="rounded-lg bg-white p-6 shadow">
            <p class="text-sm font-medium text-gray-500">Unpaid</p>
            <p class="mt-2 text-3xl font-semibold">0</p>
        </div>
        <div class="rounded-lg bg-white p-6 shadow">
            <p class="text-sm font-medium text-gray-500">Paid</p>
            <p class="mt-2 text-3xl font-semibold">0</p>
        </div>
    </div>

    <div class="mt-8 rounded-lg bg-white p-6 shadow">
        <h2 class="text-lg font-semibold">Recent Invoices</h2>
        <p class="mt-2 text-sm text-gray-500">No invoices yet.</p>
    </div>
@endsection