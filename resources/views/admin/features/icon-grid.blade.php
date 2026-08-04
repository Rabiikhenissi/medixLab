@foreach ($icons as $icon)
    <button type="button" class="icon-option" data-name="{{ $icon }}"
        onclick="selectIcon(this,'{{ $icon }}')">
        <x-dynamic-component :component="'heroicon-o-' . $icon" class="icon-svg" />
        <span>{{ $icon }}</span>
    </button>
@endforeach
