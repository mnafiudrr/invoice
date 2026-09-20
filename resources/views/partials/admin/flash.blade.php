<div class="space-y-4">
    @if (session('success'))
        <x-alert type="success" dismissible x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-cloak>
            {{ session('success') }}
        </x-alert>
    @endif

    @if (session('error'))
        <x-alert type="error" dismissible>
            {{ session('error') }}
        </x-alert>
    @endif

    @if ($errors->any())
        <x-alert type="error">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif
</div>