@props(['items' => []])

<nav aria-label="Breadcrumb" {{ $attributes->merge(['class' => 'flex items-center gap-2 text-sm text-gray-500']) }}>
    @foreach ($items as $label => $url)
        @if (! is_int($label))
            <a href="{{ $url }}" class="hover:text-gray-900">{{ $label }}</a>
            <span aria-hidden="true">/</span>
        @else
            <span class="font-medium text-gray-900">{{ $url }}</span>
        @endif
    @endforeach
</nav>