<style>
    :root {
        @foreach (config('brand.current.theme') as $shade => $rgb)
            --brand-blue-{{ $shade }}: {{ $rgb }};
        @endforeach
    }
</style>
