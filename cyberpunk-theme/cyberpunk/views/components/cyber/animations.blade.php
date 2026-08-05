@php
/**
 * Capas de animación de fondo. Cada modo (claro/oscuro) puede tener su propia
 * combinación, y varias animaciones se pueden mezclar entre sí.
 *
 * Los elementos se generan aquí con valores fijos (no aleatorios) para que el
 * HTML sea siempre el mismo y se pueda cachear. Cada uno lleva su posición,
 * duración y retardo en variables CSS, que es lo que hace que la lluvia
 * parezca lluvia y no un patrón moviéndose en bloque.
 */
$layers = [
    'light' => cyber_anims('light'),
    'dark' => cyber_anims('dark'),
];

// Cuántos elementos por animación. Menos en "ligero" para equipos lentos.
$factor = match (cyber_cfg('anim_intensity', 'normal')) {
    'low' => 0.45,
    'high' => 1.6,
    default => 1.0,
};

$n = fn (int $base) => max(3, (int) round($base * $factor));

/** Reparte posiciones a lo ancho sin que queden alineadas. */
$reparto = function (int $total, int $semilla = 7) {
    $out = [];
    for ($i = 0; $i < $total; $i++) {
        $out[] = round(fmod(($i * $semilla * 6.18) + ($i * $i * 0.7), 100), 2);
    }

    return $out;
};
@endphp

@foreach($layers as $mode => $anims)
    @foreach($anims as $anim)
    <div class="cyber-anim cyber-anim-{{ $anim }}" data-mode="{{ $mode }}" aria-hidden="true">

        @if($anim === 'rain')
            @foreach($reparto($n(44), 7) as $i => $x)
            @php
                $dur = 0.55 + (($i * 13) % 70) / 100;   // 0,55s – 1,25s
                $len = 45 + (($i * 29) % 60);            // 45px – 105px
                $op = 0.4 + (($i * 17) % 55) / 100;
            @endphp
            <span class="cyber-drop" style="--x: {{ $x }}%; --dur: {{ $dur }}s; --delay: -{{ round($i * 0.07, 2) }}s; --len: {{ $len }}px; --op: {{ round($op, 2) }}"></span>
            @endforeach

            @foreach($reparto($n(10), 11) as $i => $x)
            <span class="cyber-splash" style="--x: {{ $x }}%; --dur: {{ 1.8 + ($i % 5) * 0.4 }}s; --delay: -{{ round($i * 0.33, 2) }}s"></span>
            @endforeach

        @elseif($anim === 'storm')
            {{-- Tormenta = lluvia intensa + relámpagos --}}
            @foreach($reparto($n(50), 5) as $i => $x)
            @php
                $dur = 0.4 + (($i * 11) % 45) / 100;
                $len = 60 + (($i * 31) % 80);
            @endphp
            <span class="cyber-drop" style="--x: {{ $x }}%; --dur: {{ $dur }}s; --delay: -{{ round($i * 0.05, 2) }}s; --len: {{ $len }}px; --op: 0.75"></span>
            @endforeach

            <span class="cyber-flash" style="--fx: 35%; --dur: 11s; --delay: 0s"></span>
            <span class="cyber-flash" style="--fx: 72%; --dur: 17s; --delay: -6s"></span>
            @foreach([['x' => 28, 'w' => 110, 'len' => 48, 'dur' => 11, 'delay' => 0],
                      ['x' => 68, 'w' => 80,  'len' => 38, 'dur' => 17, 'delay' => -6]] as $rayo)
            <span class="cyber-bolt" style="--x: {{ $rayo['x'] }}%; --w: {{ $rayo['w'] }}px; --len: {{ $rayo['len'] }}vh; --dur: {{ $rayo['dur'] }}s; --delay: {{ $rayo['delay'] }}s">
                <svg viewBox="0 0 40 120" preserveAspectRatio="none" fill="none">
                    <path d="M24 0 L10 52 L22 52 L6 120 L34 46 L20 46 L32 0 Z"
                        fill="#fff" stroke="#bfe9ff" stroke-width="1.5" stroke-linejoin="round" />
                </svg>
            </span>
            @endforeach

        @elseif($anim === 'snow')
            @foreach($reparto($n(38), 9) as $i => $x)
            @php
                $size = 3 + (($i * 7) % 6);
                $dur = 9 + (($i * 19) % 11);
                $op = 0.45 + (($i * 23) % 50) / 100;
            @endphp
            <span class="cyber-flake" style="--x: {{ $x }}%; --size: {{ $size }}px; --dur: {{ $dur }}s; --delay: -{{ round($i * 0.5, 2) }}s; --op: {{ round($op, 2) }}"></span>
            @endforeach

        @elseif($anim === 'matrix')
            @foreach($reparto($n(30), 13) as $i => $x)
            @php
                $dur = 2.6 + (($i * 17) % 40) / 10;
                $op = 0.3 + (($i * 13) % 50) / 100;
            @endphp
            <span class="cyber-matrix-col" style="--x: {{ $x }}%; --dur: {{ $dur }}s; --delay: -{{ round($i * 0.31, 2) }}s; --op: {{ round($op, 2) }}"></span>
            @endforeach

        @elseif($anim === 'shooting')
            <span class="cyber-shooting-star" style="--sx: 4%;  --sy: 6%;  --sd: 7s;   --sdelay: 0s"></span>
            <span class="cyber-shooting-star" style="--sx: 34%; --sy: 2%;  --sd: 9.5s; --sdelay: 2.6s"></span>
            <span class="cyber-shooting-star" style="--sx: 62%; --sy: 12%; --sd: 11s;  --sdelay: 5.2s"></span>
            <span class="cyber-shooting-star" style="--sx: 12%; --sy: 34%; --sd: 13s;  --sdelay: 8s"></span>

        @elseif($anim === 'clouds')
            <span class="cyber-cloud" style="--cy: 6%;  --cw: 360px; --ch: 130px; --cd: 90s;  --cdelay: 0s;   --cop: 0.45"></span>
            <span class="cyber-cloud" style="--cy: 24%; --cw: 250px; --ch: 90px;  --cd: 120s; --cdelay: -40s; --cop: 0.32"></span>
            <span class="cyber-cloud" style="--cy: 48%; --cw: 440px; --ch: 150px; --cd: 150s; --cdelay: -90s; --cop: 0.28"></span>
            <span class="cyber-cloud" style="--cy: 70%; --cw: 300px; --ch: 105px; --cd: 105s; --cdelay: -20s; --cop: 0.22"></span>

        @elseif($anim === 'planets')
            <span class="cyber-planet" style="--px: 78%; --py: 14%; --ps: 150px; --pd: 46s; --pdelay: 0s"></span>
            <span class="cyber-planet" style="--px: 8%;  --py: 58%; --ps: 90px;  --pd: 62s; --pdelay: -12s"></span>
            <span class="cyber-planet" style="--px: 55%; --py: 78%; --ps: 60px;  --pd: 54s; --pdelay: -28s"></span>
        @endif

    </div>
    @endforeach
@endforeach
