@props(['name' => ''])

@php
    $clean = trim(preg_replace('/\s+/', ' ', (string) $name));
    $parts = $clean === '' ? [] : explode(' ', $clean);
    $first = $parts[0] ?? '';
    $last = count($parts) > 1 ? end($parts) : '';
    $initials = mb_strtoupper(mb_substr($first, 0, 1) . mb_substr($last, 0, 1));
    if ($initials === '') { $initials = '—'; }

    $palette = [
        ['bg' => '#FDECC5', 'fg' => '#8A5B06'],
        ['bg' => '#D9F0EC', 'fg' => '#0F6A5C'],
        ['bg' => '#DCE8FB', 'fg' => '#2451A4'],
        ['bg' => '#FBE1EA', 'fg' => '#A83368'],
        ['bg' => '#EAE1FB', 'fg' => '#5B3AA8'],
    ];
    $sum = 0;
    foreach (str_split($clean === '' ? '?' : $clean) as $ch) { $sum += ord($ch); }
    $c = $palette[$sum % count($palette)];
@endphp

<span {{ $attributes->merge(['class' => 'a-avatar']) }}
      style="background: {{ $c['bg'] }}; color: {{ $c['fg'] }};">{{ $initials }}</span>
