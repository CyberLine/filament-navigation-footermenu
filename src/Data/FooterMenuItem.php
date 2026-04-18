<?php

namespace Cyberline\FilamentNavigationFootermenu\Data;

use Closure;

class FooterMenuItem
{
    protected string $label;

    protected string|Closure|null $icon = null;

    protected string|Closure|null $url = null;

    protected bool|Closure $openInNewTab = false;

    protected bool|Closure $visible = true;

    protected string|int|Closure|null $badge = null;

    protected string|Closure|null $badgeColor = null;

    protected string|Closure|null $badgeTooltip = null;

    public static function make(string $label): static
    {
        return new self($label);
    }

    public function __construct(string $label)
    {
        $this->label = $label;
    }

    public function icon(string|Closure|null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function url(string|Closure|null $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function openUrlInNewTab(bool|Closure $open = true): static
    {
        $this->openInNewTab = $open;

        return $this;
    }

    public function visible(bool|Closure $visible): static
    {
        $this->visible = $visible;

        return $this;
    }

    public function badge(string|int|Closure|null $badge, string|Closure|null $color = null): static
    {
        $this->badge = $badge;
        $this->badgeColor = $color;

        return $this;
    }

    public function badgeTooltip(string|Closure|null $tooltip): static
    {
        $this->badgeTooltip = $tooltip;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    protected function evaluate(mixed $value): mixed
    {
        if ($value instanceof Closure) {
            return $value();
        }

        return $value;
    }

    public function isVisible(): bool
    {
        return (bool) $this->evaluate($this->visible);
    }

    /**
     * @return array{
     *     label: string,
     *     icon: ?string,
     *     url: string,
     *     open_in_new_tab: bool,
     *     badge: string|int|null,
     *     badge_color: ?string,
     *     badge_tooltip: ?string
     * }
     */
    public function toResolvedArray(): array
    {
        $url = $this->evaluate($this->url);

        return [
            'label' => $this->label,
            'icon' => $this->evaluate($this->icon),
            'url' => $url === null ? '#' : (string) $url,
            'open_in_new_tab' => (bool) $this->evaluate($this->openInNewTab),
            'badge' => $this->evaluate($this->badge),
            'badge_color' => $this->badgeColor === null ? null : (string) $this->evaluate($this->badgeColor),
            'badge_tooltip' => $this->badgeTooltip === null ? null : (string) $this->evaluate($this->badgeTooltip),
        ];
    }
}
