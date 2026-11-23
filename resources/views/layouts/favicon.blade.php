@if (config('brand.current.favicon_path'))
    <link rel="icon" href="{{ asset(config('brand.current.favicon_path')) }}" type="image/png" />
@endif
@if (config('brand.current.apple_touch_icon_path'))
    <link rel="apple-touch-icon" href="{{ asset(config('brand.current.apple_touch_icon_path')) }}" />
@endif
