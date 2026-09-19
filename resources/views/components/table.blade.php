@props([
    'responsive' => false,
])

<div @class(['overflow-x-auto' => ! $responsive, 'md:overflow-x-auto' => $responsive])>
    <table
        {{ $attributes->merge(['class' => 'min-w-full divide-y divide-gray-200 ' . ($responsive ? 'block md:table' : '')]) }}
    >
        <thead @class(['bg-gray-50 text-left', 'hidden md:table-header-group' => $responsive])>
            {{ $head ?? '' }}
        </thead>
        <tbody @class(['divide-y divide-gray-200 bg-white', 'block md:table-row-group' => $responsive])>
            {{ $slot }}
        </tbody>
    </table>
</div>

<style>
    /* Mobile card fallback for responsive tables: show data-label as a caption */
    @media (max-width: 767px) {
        .table-responsive-row {
            display: block !important;
            border: 1px solid rgb(229 231 235);
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            margin-bottom: 0.75rem;
        }
        .table-responsive-row > td {
            display: flex !important;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 0.25rem 0 !important;
            border: 0 !important;
        }
        .table-responsive-row > td[data-label]::before {
            content: attr(data-label);
            font-size: 0.75rem;
            font-weight: 500;
            color: rgb(107 114 128);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
    }
</style>