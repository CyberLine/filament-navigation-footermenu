<?php

namespace Cyberline\FilamentNavigationFootermenu;

use Closure;
use Cyberline\FilamentNavigationFootermenu\Data\FooterMenuItem;
use Cyberline\FilamentNavigationFootermenu\Support\ItemNormalizer;
use Filament\Contracts\Plugin;
use Filament\Enums\UserMenuPosition;
use Filament\Panel;
use Filament\View\PanelsRenderHook;

class FooterMenuPlugin implements Plugin
{
    protected string|Closure|null $triggerLabel = null;

    protected string|Closure|null $triggerIcon = null;

    protected string|Closure|null $heading = null;

    /**
     * @var array<string, mixed>|Closure
     */
    protected array|Closure $extraAttributes = [];

    /**
     * @var array<int, array<string, mixed>|FooterMenuItem>|Closure|null
     */
    protected array|Closure|null $items = null;

    protected ?string $customView = null;

    protected bool|Closure $visible = true;

    protected int $sort = 0;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-navigation-footermenu';
    }

    public function register(Panel $panel): void
    {
        $panel->renderHook(
            PanelsRenderHook::SIDEBAR_FOOTER,
            fn (): string => $this->renderSidebar(),
        );

        $panel->renderHook(
            PanelsRenderHook::USER_MENU_AFTER,
            fn (): string => $this->renderTopbarForHook('user-menu-after'),
        );

        $panel->renderHook(
            PanelsRenderHook::TOPBAR_END,
            fn (): string => $this->renderTopbarForHook('topbar-end'),
        );
    }

    public function boot(Panel $panel): void {}

    public function triggerLabel(string|Closure|null $label): static
    {
        $this->triggerLabel = $label;

        return $this;
    }

    public function triggerIcon(string|Closure|null $icon): static
    {
        $this->triggerIcon = $icon;

        return $this;
    }

    public function heading(string|Closure|null $heading): static
    {
        $this->heading = $heading;

        return $this;
    }

    /**
     * Extra HTML attributes for the menu root container.
     *
     * @param array<string, mixed>|Closure $attributes
     */
    public function extraAttributes(array|Closure $attributes): static
    {
        $this->extraAttributes = $attributes;

        return $this;
    }

    /**
     * @param array<int, array<string, mixed>|FooterMenuItem>|Closure $items
     */
    public function items(array|Closure $items): static
    {
        $this->items = $items;

        return $this;
    }

    public function view(?string $bladeView): static
    {
        $this->customView = $bladeView;

        return $this;
    }

    public function visible(bool|Closure $condition): static
    {
        $this->visible = $condition;

        return $this;
    }

    public function sort(int $sort): static
    {
        $this->sort = $sort;

        return $this;
    }

    public function getSort(): int
    {
        return $this->sort;
    }

    public function getCustomView(): ?string
    {
        return $this->customView;
    }

    public function getResolvedTriggerLabel(): string
    {
        if ($this->triggerLabel !== null) {
            return (string) $this->evaluate($this->triggerLabel);
        }

        $fromConfig = config('filament-navigation-footermenu.trigger.label');

        if (is_string($fromConfig) && $fromConfig !== '') {
            return $fromConfig;
        }

        return (string) __('filament-navigation-footermenu::footermenu.default_trigger_label');
    }

    public function getResolvedTriggerIcon(): ?string
    {
        if ($this->triggerIcon !== null) {
            $icon = $this->evaluate($this->triggerIcon);

            return $icon === null ? null : (string) $icon;
        }

        $fromConfig = config('filament-navigation-footermenu.trigger.icon');

        return is_string($fromConfig) ? $fromConfig : null;
    }

    public function getResolvedHeading(): ?string
    {
        if ($this->heading !== null) {
            $heading = $this->evaluate($this->heading);

            return $heading === null || $heading === '' ? null : (string) $heading;
        }

        $fromConfig = config('filament-navigation-footermenu.heading');

        return is_string($fromConfig) && $fromConfig !== '' ? $fromConfig : null;
    }

    /**
     * @return array<string, mixed>
     */
    public function getResolvedExtraAttributes(): array
    {
        $attributes = $this->evaluate($this->extraAttributes);

        return is_array($attributes) ? $attributes : [];
    }

    /**
     * @return array<int, FooterMenuItem>
     */
    public function getResolvedItemObjects(): array
    {
        $raw = $this->items;
        if ($raw instanceof Closure) {
            $raw = $raw();
        }
        if ($raw === null) {
            /** @var array<int, array<string, mixed>> $configItems */
            $configItems = config('filament-navigation-footermenu.items', []);

            return ItemNormalizer::normalize($configItems);
        }

        return ItemNormalizer::normalize($raw);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getResolvedVisibleItemRows(): array
    {
        $rows = [];
        foreach ($this->getResolvedItemObjects() as $item) {
            if (! $item->isVisible()) {
                continue;
            }
            $rows[] = $item->toResolvedArray();
        }

        return $rows;
    }

    protected function shouldRenderBase(): bool
    {
        if (! filament()->auth()->check()) {
            return false;
        }

        if (filament()->hasTenancy() && ! filament()->getTenant()) {
            return false;
        }

        return (bool) $this->evaluate($this->visible);
    }

    protected function renderSidebar(): string
    {
        if (! $this->shouldRenderBase()) {
            return '';
        }

        if (filament()->hasTopNavigation()) {
            return '';
        }

        if (! $this->hasRenderableContent()) {
            return '';
        }

        return view('filament-navigation-footermenu::trigger-sidebar', [
            'plugin' => $this,
        ])->render();
    }

    protected function renderTopbarForHook(string $hook): string
    {
        if (! $this->shouldRenderBase()) {
            return '';
        }

        if (! filament()->hasTopNavigation()) {
            return '';
        }

        if (! $this->hasRenderableContent()) {
            return '';
        }

        $hasTopbarUserMenu = filament()->hasUserMenu()
            && filament()->getUserMenuPosition() === UserMenuPosition::Topbar;

        if ($hook === 'user-menu-after') {
            if (! $hasTopbarUserMenu) {
                return '';
            }

            return view('filament-navigation-footermenu::trigger-topbar', [
                'plugin' => $this,
                'topbarAfterUserMenu' => true,
            ])->render();
        }

        if ($hook === 'topbar-end') {
            if ($hasTopbarUserMenu) {
                return '';
            }

            return view('filament-navigation-footermenu::trigger-topbar', [
                'plugin' => $this,
                'topbarAfterUserMenu' => false,
            ])->render();
        }

        return '';
    }

    protected function hasRenderableContent(): bool
    {
        if ($this->customView !== null) {
            return true;
        }

        return $this->getResolvedVisibleItemRows() !== [];
    }

    protected function evaluate(mixed $value): mixed
    {
        if ($value instanceof Closure) {
            return $value();
        }

        return $value;
    }
}
