<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Support;

/**
 * Diseños completos del tema.
 *
 * Un diseño no es sólo una paleta: cambia la forma de las tarjetas, la
 * tipografía, los efectos, los bordes y las animaciones de fondo. Aplicar uno
 * deja la web con otra cara, y luego se puede seguir ajustando cada cosa a
 * mano desde el panel.
 */
class Designs
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            'cyberpunk' => [
                'label' => 'Cyberpunk',
                'description' => 'Neón, esquinas cortadas y efectos de pantalla. El diseño original.',
                'preview' => ['#3c83f6', '#ff3d9e', '#38bdf8', '#0a0f1e'],
                'settings' => [
                    'card_style' => 'clip',
                    'corner_style' => 'sharp',
                    'font_family' => 'orbitron',
                    'effect_neon' => true,
                    'effect_scanlines' => true,
                    'effect_grid' => true,
                    'effect_glitch' => true,
                    'effect_noise' => false,
                    'anim_dark' => ['stars', 'shooting'],
                    'anim_light' => ['clouds'],

                    'primary' => 'hsl(217, 91%, 50%)',
                    'secondary' => 'hsl(330, 90%, 55%)',
                    'accent' => 'hsl(199, 92%, 48%)',
                    'neutral' => 'hsl(214, 32%, 85%)',
                    'base' => 'hsl(222, 44%, 12%)',
                    'muted' => 'hsl(215, 16%, 42%)',
                    'inverted' => 'hsl(0, 0%, 100%)',
                    'background' => 'hsl(0, 0%, 100%)',
                    'background-secondary' => 'hsl(214, 45%, 97%)',

                    'dark-primary' => 'hsl(217, 91%, 60%)',
                    'dark-secondary' => 'hsl(330, 100%, 62%)',
                    'dark-accent' => 'hsl(199, 95%, 60%)',
                    'dark-neutral' => 'hsl(217, 33%, 24%)',
                    'dark-base' => 'hsl(0, 0%, 100%)',
                    'dark-muted' => 'hsl(215, 22%, 72%)',
                    'dark-inverted' => 'hsl(0, 0%, 100%)',
                    'dark-background' => 'hsl(222, 47%, 6%)',
                    'dark-background-secondary' => 'hsl(222, 40%, 10%)',
                ],
            ],

            'corporativo' => [
                'label' => 'Corporativo',
                'description' => 'Azul sobrio, tarjetas redondeadas y sombras suaves. Serio y de confianza, estilo empresa de toda la vida.',
                'preview' => ['#1d4ed8', '#0f766e', '#0284c7', '#f8fafc'],
                'settings' => [
                    'card_style' => 'soft',
                    'corner_style' => 'round',
                    'font_family' => 'system',
                    'effect_neon' => false,
                    'effect_scanlines' => false,
                    'effect_grid' => false,
                    'effect_glitch' => false,
                    'effect_noise' => false,
                    'anim_dark' => [],
                    'anim_light' => [],

                    'primary' => 'hsl(224, 76%, 40%)',
                    'secondary' => 'hsl(175, 77%, 26%)',
                    'accent' => 'hsl(199, 89%, 42%)',
                    'neutral' => 'hsl(215, 20%, 85%)',
                    'base' => 'hsl(222, 47%, 15%)',
                    'muted' => 'hsl(215, 16%, 45%)',
                    'inverted' => 'hsl(0, 0%, 100%)',
                    'background' => 'hsl(0, 0%, 100%)',
                    'background-secondary' => 'hsl(210, 40%, 98%)',

                    'dark-primary' => 'hsl(217, 85%, 62%)',
                    'dark-secondary' => 'hsl(175, 60%, 45%)',
                    'dark-accent' => 'hsl(199, 85%, 58%)',
                    'dark-neutral' => 'hsl(217, 25%, 27%)',
                    'dark-base' => 'hsl(210, 30%, 96%)',
                    'dark-muted' => 'hsl(215, 18%, 68%)',
                    'dark-inverted' => 'hsl(0, 0%, 100%)',
                    'dark-background' => 'hsl(222, 35%, 11%)',
                    'dark-background-secondary' => 'hsl(222, 30%, 15%)',
                ],
            ],

            'minimal' => [
                'label' => 'Minimalista',
                'description' => 'Mucho aire, líneas finas y nada de adornos. Limpio y muy rápido de leer.',
                'preview' => ['#111827', '#6b7280', '#2563eb', '#ffffff'],
                'settings' => [
                    'card_style' => 'flat',
                    'corner_style' => 'round',
                    'font_family' => 'system',
                    'effect_neon' => false,
                    'effect_scanlines' => false,
                    'effect_grid' => false,
                    'effect_glitch' => false,
                    'effect_noise' => false,
                    'anim_dark' => [],
                    'anim_light' => [],

                    'primary' => 'hsl(222, 47%, 11%)',
                    'secondary' => 'hsl(220, 9%, 46%)',
                    'accent' => 'hsl(221, 83%, 53%)',
                    'neutral' => 'hsl(220, 13%, 88%)',
                    'base' => 'hsl(224, 39%, 12%)',
                    'muted' => 'hsl(220, 9%, 46%)',
                    'inverted' => 'hsl(0, 0%, 100%)',
                    'background' => 'hsl(0, 0%, 100%)',
                    'background-secondary' => 'hsl(210, 20%, 98%)',

                    'dark-primary' => 'hsl(0, 0%, 98%)',
                    'dark-secondary' => 'hsl(220, 9%, 65%)',
                    'dark-accent' => 'hsl(217, 91%, 65%)',
                    'dark-neutral' => 'hsl(220, 10%, 26%)',
                    'dark-base' => 'hsl(0, 0%, 98%)',
                    'dark-muted' => 'hsl(220, 9%, 65%)',
                    'dark-inverted' => 'hsl(224, 39%, 10%)',
                    'dark-background' => 'hsl(224, 20%, 9%)',
                    'dark-background-secondary' => 'hsl(224, 16%, 13%)',
                ],
            ],

            'premium' => [
                'label' => 'Premium',
                'description' => 'Fondo oscuro elegante con dorado y títulos con serifa. Aire de marca cuidada.',
                'preview' => ['#c9a227', '#8b6f1f', '#e3c565', '#12100c'],
                'settings' => [
                    'card_style' => 'soft',
                    'corner_style' => 'round',
                    'font_family' => 'serif',
                    'effect_neon' => false,
                    'effect_scanlines' => false,
                    'effect_grid' => false,
                    'effect_glitch' => false,
                    'effect_noise' => false,
                    'anim_dark' => ['aurora'],
                    'anim_light' => [],

                    'primary' => 'hsl(43, 68%, 40%)',
                    'secondary' => 'hsl(30, 40%, 30%)',
                    'accent' => 'hsl(43, 74%, 52%)',
                    'neutral' => 'hsl(40, 18%, 84%)',
                    'base' => 'hsl(30, 25%, 14%)',
                    'muted' => 'hsl(35, 12%, 42%)',
                    'inverted' => 'hsl(45, 60%, 97%)',
                    'background' => 'hsl(45, 33%, 99%)',
                    'background-secondary' => 'hsl(42, 30%, 96%)',

                    'dark-primary' => 'hsl(43, 74%, 58%)',
                    'dark-secondary' => 'hsl(35, 45%, 48%)',
                    'dark-accent' => 'hsl(45, 85%, 68%)',
                    'dark-neutral' => 'hsl(38, 14%, 24%)',
                    'dark-base' => 'hsl(44, 30%, 94%)',
                    'dark-muted' => 'hsl(40, 12%, 68%)',
                    'dark-inverted' => 'hsl(30, 30%, 8%)',
                    'dark-background' => 'hsl(36, 22%, 6%)',
                    'dark-background-secondary' => 'hsl(36, 18%, 10%)',
                ],
            ],
        ];
    }

    /**
     * Ajustes de un diseño (vacío si no existe).
     *
     * @return array<string, mixed>
     */
    public static function settings(string $key): array
    {
        return self::all()[$key]['settings'] ?? [];
    }

    public static function exists(string $key): bool
    {
        return isset(self::all()[$key]);
    }

    /**
     * Opciones para un selector.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $out = [];

        foreach (self::all() as $key => $design) {
            $out[$key] = $design['label'];
        }

        return $out;
    }
}
