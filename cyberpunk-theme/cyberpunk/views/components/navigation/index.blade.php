@php
// Cómo se comporta la barra: alineación de los enlaces, si van a la vista o
// dentro de un panel lateral, y por qué lado se abre ese panel.
$navAlign = cyber_cfg('nav_align', 'left');
$navStyle = cyber_cfg('nav_style', 'bar');
$navSide = cyber_cfg('nav_drawer_side', 'right') === 'left' ? 'left' : 'right';
$enBarra = $navStyle !== 'drawer';
@endphp

<nav class="w-full px-4 lg:px-8 bg-background-secondary/85 backdrop-blur-md border-b border-neutral md:h-16 flex md:flex-row flex-col justify-between fixed top-0 z-20">
    <div class="absolute inset-x-0 bottom-0 h-px cyber-divider pointer-events-none"></div>
    <div
        x-data="{
            slideOverOpen: false,
            hasAside: !!document.getElementById('main-aside')
        }"
        x-init="$watch('slideOverOpen', value => { document.documentElement.style.overflow = value ? 'hidden' : '' })"
        class="relative z-50 w-full h-auto">
        <div
            class="flex flex-row items-center justify-between h-16"
            :class="hasAside ? 'w-full' : 'container'">

            <div class="flex flex-row items-center {{ $navAlign === 'center' ? 'flex-1' : '' }}">
                <a href="{{ route('home') }}" class="flex flex-row items-center h-10 gap-2 group" wire:navigate>
                    <x-logo class="h-8 transition group-hover:drop-shadow-[0_0_10px_hsl(var(--color-primary))]" />
                    @if(theme('logo_display', 'logo-and-name') != 'logo-only')
                    <span class="text-xl font-black leading-none flex items-center cyber-neon-text">{{ config('app.name') }}</span>
                    @endif
                </a>
                @if($enBarra)
                <div class="md:flex hidden flex-row
                    {{ $navAlign === 'center' ? 'flex-1 justify-center' : ($navAlign === 'right' ? 'flex-1 justify-end me-4' : 'ml-6') }}">
                    @foreach (\App\Classes\Navigation::getLinks() as $nav)
                    @if (isset($nav['children']) && count($nav['children']) > 0)
                    <div class="relative">
                        <x-dropdown>
                            <x-slot:trigger>
                                <div class="flex flex-col">
                                    <span class="flex flex-row items-center p-3 text-sm font-semibold whitespace-nowrap text-base hover:text-primary transition">
                                        {{ $nav['name'] }}
                                    </span>
                                </div>
                            </x-slot:trigger>
                            <x-slot:content>
                                @foreach ($nav['children'] as $child)
                                <x-navigation.link
                                    :href="$child['url']"
                                    :spa="isset($child['spa']) ? $nav['spa'] : true">
                                    {{ $child['name'] }}
                                </x-navigation.link>
                                @endforeach
                            </x-slot:content>
                        </x-dropdown>
                    </div>
                    @else
                    <x-navigation.link
                        :href="$nav['url']"
                        :spa="isset($nav['spa']) ? $nav['spa'] : true"
                        class="flex items-center p-3">
                        {{ $nav['name'] }}
                    </x-navigation.link>
                    @endif
                    @endforeach

                </div>
                @endif
            </div>

            <div class="flex flex-row items-center">
                <livewire:components.cart />

                <div class="items-center hidden md:flex mr-1">
                    <livewire:components.locale-switch />
                    <x-theme-toggle />
                </div>

                @if(auth()->check())
                <livewire:components.notifications />
                <div class="hidden lg:flex">
                    <x-dropdown :showArrow="false">
                        <x-slot:trigger>
                            <img src="{{ cyber_avatar(auth()->user()) }}" class="size-9 rounded-full border-2 border-primary/60 bg-background object-cover cyber-neon" alt="avatar" />
                        </x-slot:trigger>
                        <x-slot:content>
                            <div class="flex flex-col p-3 border-b border-neutral mb-1">
                                <span class="text-sm font-bold text-base break-words">{{ auth()->user()->name }}</span>
                                <span class="text-xs text-base/60 break-words">{{ auth()->user()->email }}</span>
                            </div>
                            @foreach (\App\Classes\Navigation::getAccountDropdownLinks() as $nav)
                            <x-navigation.link :href="$nav['url']" :spa="isset($nav['spa']) ? $nav['spa'] : true">
                                {{ $nav['name'] }}
                            </x-navigation.link>
                            @endforeach
                            <livewire:auth.logout />
                        </x-slot:content>
                    </x-dropdown>
                </div>
                @else
                <div class="hidden lg:flex flex-row gap-3">
                    <a href="{{ route('login') }}" wire:navigate>
                        <x-button.secondary>
                            {{ __('navigation.login') }}
                        </x-button.secondary>
                    </a>
                    @if(!config('settings.registration_disabled', false))
                    <a href="{{ route('register') }}" wire:navigate>
                        <x-button.primary class="cyber-sweep">
                            {{ __('navigation.register') }}
                        </x-button.primary>
                    </a>
                    @endif
                </div>
                @endif
                <button
                    @click="slideOverOpen = !slideOverOpen"
                    class="relative w-10 h-10 flex items-center justify-center rounded-lg hover:bg-primary/10 transition {{ $enBarra ? 'lg:hidden' : '' }}"
                    aria-label="Toggle Menu">

                    <span
                        x-show="!slideOverOpen"
                        x-transition:enter="transition duration-300"
                        x-transition:enter-start="opacity-0 -rotate-90 scale-75"
                        x-transition:enter-end="opacity-100 rotate-0 scale-100"
                        x-transition:leave="transition duration-150"
                        x-transition:leave-start="opacity-100 rotate-0 scale-100"
                        x-transition:leave-end="opacity-0 rotate-90 scale-75"
                        class="absolute inset-0 flex items-center justify-center"
                        aria-hidden="true">
                        <x-ri-menu-fill class="size-5" />
                    </span>

                    <span
                        x-show="slideOverOpen"
                        x-transition:enter="transition duration-300"
                        x-transition:enter-start="opacity-0 rotate-90 scale-75"
                        x-transition:enter-end="opacity-100 rotate-0 scale-100"
                        x-transition:leave="transition duration-150"
                        x-transition:leave-start="opacity-100 rotate-0 scale-100"
                        x-transition:leave-end="opacity-0 -rotate-90 scale-75"
                        class="absolute inset-0 flex items-center justify-center"
                        aria-hidden="true">
                        <x-ri-close-fill class="size-5" />
                    </span>

                </button>
            </div>
        </div>
        {{-- Panel lateral: enlaces del sitio + todo lo de la cuenta a la vista,
             sin tener que dar un segundo clic en el avatar. --}}
        <template x-teleport="body">
            <div x-show="slideOverOpen" x-cloak
                @keydown.window.escape="slideOverOpen = false"
                class="fixed inset-0 z-[99]" aria-modal="true" tabindex="-1">

                {{-- Fondo oscurecido --}}
                <div x-show="slideOverOpen"
                    x-transition.opacity.duration.250ms
                    @click="slideOverOpen = false"
                    class="absolute inset-0 bg-background/70 backdrop-blur-sm"></div>

                {{-- El panel --}}
                <div x-show="slideOverOpen"
                    x-transition:enter="transition transform ease-out duration-300"
                    x-transition:enter-start="{{ $navSide === 'left' ? '-translate-x-full' : 'translate-x-full' }}"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition transform ease-in duration-200"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="{{ $navSide === 'left' ? '-translate-x-full' : 'translate-x-full' }}"
                    class="absolute top-0 bottom-0 {{ $navSide === 'left' ? 'left-0 border-e' : 'right-0 border-s' }}
                        w-full sm:w-[380px] bg-background-secondary border-neutral shadow-2xl flex flex-col">

                    <div class="flex items-center justify-between gap-3 p-4 border-b border-neutral">
                        <span class="font-black text-lg cyber-neon-text">{{ config('app.name') }}</span>
                        <button @click="slideOverOpen = false" aria-label="Cerrar menú"
                            class="p-2 rounded-lg border border-neutral text-base/60 hover:text-error hover:border-error/40 transition cursor-pointer">
                            <x-ri-close-fill class="size-5" />
                        </button>
                    </div>

                    <div class="flex-1 min-h-0 overflow-y-auto p-4">
                        <x-navigation.sidebar-links />

                        @auth
                        <div class="cyber-divider my-5"></div>

                        <div class="flex items-center gap-3 px-1 mb-3">
                            <img src="{{ cyber_avatar(auth()->user()) }}" alt="avatar"
                                class="size-11 rounded-full border-2 border-primary/60 bg-background object-cover shrink-0">
                            <div class="min-w-0">
                                <p class="font-bold truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-base/60 truncate">{{ auth()->user()->email }}</p>
                            </div>
                        </div>

                        {{-- Cuenta, panel, tickets y (si tiene permiso) administración.
                             La lista la da Paymenter y ya filtra el acceso al panel de
                             administración según el rol, así que aquí no hay fugas. --}}
                        <div class="flex flex-col gap-1">
                            @foreach (\App\Classes\Navigation::getAccountDropdownLinks() as $nav)
                            <x-navigation.link :href="$nav['url']" :spa="isset($nav['spa']) ? $nav['spa'] : true">
                                {{ $nav['name'] }}
                            </x-navigation.link>
                            @endforeach
                            <livewire:auth.logout />
                        </div>
                        @else
                        <div class="cyber-divider my-5"></div>
                        <div class="flex flex-col gap-3">
                            @if(!config('settings.registration_disabled', false))
                            <a href="{{ route('register') }}" wire:navigate>
                                <x-button.primary>{{ __('navigation.register') }}</x-button.primary>
                            </a>
                            @endif
                            <a href="{{ route('login') }}" wire:navigate>
                                <x-button.secondary>{{ __('navigation.login') }}</x-button.secondary>
                            </a>
                        </div>
                        @endauth
                    </div>

                    <div class="p-4 border-t border-neutral flex items-center justify-between gap-2">
                        <livewire:components.locale-switch />
                        <x-theme-toggle />
                    </div>
                </div>
            </div>
        </template>
    </div>
</nav>
