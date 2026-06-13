@props(['name' => '', 'size' => 32])

@php
    $clean = trim((string) $name);
    $parts = preg_split('/\s+/', $clean, -1, PREG_SPLIT_NO_EMPTY);
    $first = $parts[0] ?? '';
    $last  = count($parts) > 1 ? $parts[count($parts) - 1] : '';
    $initials = strtoupper(mb_substr($first, 0, 1) . mb_substr($last, 0, 1));
    $palette = ['#6366f1','#8b5cf6','#ec4899','#f43f5e','#f59e0b','#10b981','#06b6d4','#3b82f6','#14b8a6','#a855f7'];
    $idx = $clean === '' ? 0 : (hexdec(substr(md5($clean), 0, 2)) % count($palette));
    $color = $palette[$idx];
@endphp

<span class="ui-avatar" style="--av-size: {{ $size }}px; --av-color: {{ $color }};" title="{{ $clean }}">{{ $initials !== '' ? $initials : '?' }}</span>
