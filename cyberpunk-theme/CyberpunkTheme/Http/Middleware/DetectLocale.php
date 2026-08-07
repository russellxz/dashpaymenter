<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pone la web en el idioma del visitante la primera vez que entra.
 *
 * Paymenter trae 24 idiomas, pero su middleware SetLocale sólo mira la sesión:
 * si el visitante no ha elegido idioma a mano, siempre ve el idioma por defecto
 * de la tienda. Aquí se mira lo que pide su navegador (cabecera Accept-Language,
 * que es justo el idioma que la persona tiene configurado) y se elige el mejor
 * de entre los que el administrador haya activado en Paymenter.
 *
 * No se usa la IP a propósito: la IP dice el país, no el idioma. Un
 * hispanohablante en Alemania acabaría leyendo alemán, y con una VPN el país
 * deja de tener sentido. El navegador sí sabe qué idioma quiere su dueño.
 *
 * En cuanto el visitante toca el selector de idioma, su elección manda y esto
 * ya no vuelve a tocar nada.
 */
class DetectLocale
{
    /** Marca en sesión para no repetir la detección en cada petición. */
    public const SESSION_KEY = 'cyberpunk_locale_detected';

    public function handle(Request $request, Closure $next): Response
    {
        try {
            $locale = $this->detect($request);

            if ($locale !== null) {
                App::setLocale($locale);

                if ($request->hasSession()) {
                    // Se guarda como si lo hubiera elegido el visitante, para
                    // que el selector de idioma lo muestre marcado y para que
                    // el resto de peticiones no tengan que volver a calcularlo.
                    $request->session()->put('locale', $locale);
                    $request->session()->put(self::SESSION_KEY, true);
                }
            }
        } catch (\Throwable $e) {
            // Elegir idioma nunca debe tumbar el sitio.
        }

        return $next($request);
    }

    private function detect(Request $request): ?string
    {
        if (!Config::themeBool('auto_locale', true)) {
            return null;
        }

        // Si ya hay idioma en la sesión, o lo eligió el visitante o ya lo
        // detectamos antes: en ambos casos no se toca.
        if ($request->hasSession() && $request->session()->has('locale')) {
            return null;
        }

        if ($request->is('admin', 'admin/*', 'api/*')) {
            return null;
        }

        $permitidos = $this->allowed();

        if (count($permitidos) === 0) {
            return null;
        }

        $preferido = $this->fromHeader($request, $permitidos);

        if ($preferido !== null) {
            return $preferido;
        }

        // Ningún idioma del navegador está disponible: se usa el de respaldo
        // (inglés salvo que el administrador ponga otro).
        $respaldo = (string) Config::theme('default_locale', 'en');

        return in_array($respaldo, $permitidos, true) ? $respaldo : null;
    }

    /**
     * Idiomas que el administrador ha activado en Paymenter.
     *
     * @return array<int, string>
     */
    private function allowed(): array
    {
        $permitidos = config('settings.allowed_languages', []);

        if (!is_array($permitidos)) {
            $permitidos = [];
        }

        $disponibles = array_keys(config('app.available_locales', []));

        return array_values(array_filter(
            $permitidos,
            fn ($code) => is_string($code) && (count($disponibles) === 0 || in_array($code, $disponibles, true))
        ));
    }

    /**
     * Mejor idioma de la cabecera Accept-Language de entre los permitidos.
     *
     * Se recorre en el orden de preferencia que manda el navegador
     * ("es-ES,es;q=0.9,en;q=0.8") y se acepta tanto el idioma exacto (pt-BR)
     * como el genérico (pt).
     *
     * @param  array<int, string>  $permitidos
     */
    private function fromHeader(Request $request, array $permitidos): ?string
    {
        $cabecera = (string) $request->header('Accept-Language', '');

        if (trim($cabecera) === '') {
            return null;
        }

        // Índice en minúsculas para comparar sin importar mayúsculas.
        $indice = [];
        foreach ($permitidos as $code) {
            $indice[strtolower($code)] = $code;
        }

        foreach ($this->parse($cabecera) as $idioma) {
            if (isset($indice[$idioma])) {
                return $indice[$idioma];
            }

            // "pt-br" → "pt"
            $corto = explode('-', $idioma)[0];

            if (isset($indice[$corto])) {
                return $indice[$corto];
            }
        }

        return null;
    }

    /**
     * Idiomas de la cabecera ordenados por preferencia (mayor q primero).
     *
     * @return array<int, string>
     */
    private function parse(string $cabecera): array
    {
        $pesos = [];

        foreach (explode(',', $cabecera) as $posicion => $trozo) {
            $partes = explode(';', trim($trozo));
            $idioma = strtolower(trim($partes[0]));

            if ($idioma === '' || $idioma === '*') {
                continue;
            }

            $q = 1.0;

            foreach (array_slice($partes, 1) as $parametro) {
                if (str_starts_with(trim($parametro), 'q=')) {
                    $q = (float) substr(trim($parametro), 2);
                }
            }

            // La posición desempata cuando dos idiomas tienen el mismo peso,
            // que es lo que hacen los navegadores.
            $pesos[$idioma] = max($pesos[$idioma] ?? 0, $q * 1000 - $posicion);
        }

        arsort($pesos);

        return array_keys($pesos);
    }
}
