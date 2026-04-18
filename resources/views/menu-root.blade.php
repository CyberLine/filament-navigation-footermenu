@php
    /** @var \Cyberline\FilamentNavigationFootermenu\FooterMenuPlugin $plugin */
    $triggerLabel = $plugin->getResolvedTriggerLabel();
    $triggerIcon = $plugin->getResolvedTriggerIcon();
    $heading = $plugin->getResolvedHeading();
    $items = $plugin->getResolvedVisibleItemRows();
    $customView = $plugin->getCustomView();
    $extraAttributes = $plugin->getResolvedExtraAttributes();
    $topbarAfterUserMenu = $topbarAfterUserMenu ?? false;

    $rootAttributes = (new \Illuminate\View\ComponentAttributeBag($extraAttributes))
        ->class([
            'fi-nav-footermenu-root',
            "fi-nav-footermenu-root--{$layout}",
        ]);
@endphp

<div wire:ignore x-data="{ open: false }" x-on:keydown.escape.window="if (open) open = false" {{ $rootAttributes }}>
    @if ($layout === 'sidebar')
        <div class="fi-nav-footermenu-sidebar px-6 pb-3">
            <ul class="fi-sidebar-nav-groups -mx-2">
                <li
                    x-bind:class="{ 'fi-collapsed': ! open }"
                    class="fi-sidebar-group fi-collapsible"
                >
                    {{-- Items FIRST in DOM -> expand upwards visually --}}
                    <ul
                        x-show="open"
                        x-collapse.duration.200ms
                        x-cloak
                        style="display: none;"
                        class="fi-sidebar-group-items"
                    >
                        @if ($customView)
                            @include($customView, [
                                'layout' => $layout,
                                'heading' => $heading,
                                'items' => $items,
                                'plugin' => $plugin,
                                'triggerLabel' => $triggerLabel,
                                'triggerIcon' => $triggerIcon,
                            ])
                        @else
                            @foreach ($items as $row)
                                <li @class([
                                    'fi-sidebar-item',
                                    'fi-sidebar-item-has-url' => filled($row['url'] ?? null),
                                ])>
                                    <a
                                        href="{{ $row['url'] ?? '#' }}"
                                        @if (! empty($row['open_in_new_tab'])) target="_blank" rel="noopener noreferrer" @endif
                                        class="fi-sidebar-item-btn"
                                    >
                                        @if (! empty($row['icon']))
                                            {{ \Filament\Support\generate_icon_html($row['icon'], size: \Filament\Support\Enums\IconSize::Large) }}
                                        @endif

                                        <span
                                            x-show="$store.sidebar.isOpen"
                                            x-transition:enter="fi-transition-enter"
                                            x-transition:enter-start="fi-transition-enter-start"
                                            x-transition:enter-end="fi-transition-enter-end"
                                            class="fi-sidebar-item-label"
                                        >
                                            {{ $row['label'] ?? '' }}
                                        </span>

                                        @if (($row['badge'] ?? null) !== null && ($row['badge'] ?? '') !== '')
                                            <span
                                                x-show="$store.sidebar.isOpen"
                                                x-transition:enter="fi-transition-enter"
                                                x-transition:enter-start="fi-transition-enter-start"
                                                x-transition:enter-end="fi-transition-enter-end"
                                                @if (filled($row['badge_tooltip'] ?? null))
                                                    x-tooltip="{
                                                        content: @js($row['badge_tooltip']),
                                                        theme: $store.theme,
                                                    }"
                                                @endif
                                                class="fi-sidebar-item-badge-ctn"
                                            >
                                                <x-filament::badge :color="($row['badge_color'] ?? null) ?? 'gray'">
                                                    {{ $row['badge'] }}
                                                </x-filament::badge>
                                            </span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>

                    <div
                        x-on:click="open = ! open"
                        class="fi-sidebar-group-btn"
                        role="button"
                        x-bind:aria-expanded="open"
                    >
                        @if ($triggerIcon)
                            {{ \Filament\Support\generate_icon_html($triggerIcon, size: \Filament\Support\Enums\IconSize::Large) }}
                        @endif

                        <span
                            x-show="$store.sidebar.isOpen"
                            x-transition:enter="fi-transition-enter"
                            x-transition:enter-start="fi-transition-enter-start"
                            x-transition:enter-end="fi-transition-enter-end"
                            class="fi-sidebar-group-label"
                        >
                            {{ $triggerLabel }}
                        </span>

                        <x-filament::icon-button
                            color="gray"
                            icon="heroicon-m-chevron-down"
                            :label="$triggerLabel"
                            x-show="$store.sidebar.isOpen"
                            x-on:click.stop="open = ! open"
                            x-bind:class="{ '-rotate-180': open }"
                            class="fi-sidebar-group-collapse-btn transition-transform"
                        />
                    </div>
                </li>
            </ul>
        </div>
    @else
        {{--
            Nach User-Menü (Hook panels::user-menu.after): innerhalb .fi-topbar-end → gap-x-4 greift.
            Fallback TOPBAR_END: außerhalb .fi-topbar-end → extra margin + kein negatives Icon-Margin.
        --}}
        <div
            @class([
                'fi-nav-footermenu-topbar flex shrink-0 items-center pe-2',
                'ms-5' => ! $topbarAfterUserMenu,
            ])
        >
            <x-filament::dropdown placement="bottom-end" :teleport="true" class="shrink-0">
                <x-slot name="trigger">
                    <x-filament::icon-button
                        color="gray"
                        :icon="$triggerIcon ?? 'heroicon-o-ellipsis-horizontal'"
                        icon-size="lg"
                        :label="$triggerLabel"
                        class="!m-0 !rounded-full hover:bg-gray-100 focus-visible:bg-gray-100 dark:hover:bg-white/5 dark:focus-visible:bg-white/5"
                    />
                </x-slot>

                @if (filled($heading))
                    <x-filament::dropdown.header>
                        {{ $heading }}
                    </x-filament::dropdown.header>
                @endif

                @if ($customView)
                    <div class="p-2">
                        @include($customView, [
                            'layout' => $layout,
                            'heading' => $heading,
                            'items' => $items,
                            'plugin' => $plugin,
                            'triggerLabel' => $triggerLabel,
                            'triggerIcon' => $triggerIcon,
                        ])
                    </div>
                @else
                    <x-filament::dropdown.list>
                        @foreach ($items as $row)
                            <x-filament::dropdown.list.item
                                :badge="($row['badge'] ?? null) !== null && ($row['badge'] ?? '') !== '' ? $row['badge'] : null"
                                :badge-color="($row['badge_color'] ?? null) ?? 'primary'"
                                :badge-tooltip="$row['badge_tooltip'] ?? null"
                                :href="$row['url'] ?? '#'"
                                tag="a"
                                :icon="$row['icon'] ?? null"
                                :target="! empty($row['open_in_new_tab']) ? '_blank' : null"
                            >
                                {{ $row['label'] ?? '' }}
                            </x-filament::dropdown.list.item>
                        @endforeach
                    </x-filament::dropdown.list>
                @endif
            </x-filament::dropdown>
        </div>
    @endif
</div>
