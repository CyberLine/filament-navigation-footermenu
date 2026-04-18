<?php

namespace Cyberline\FilamentNavigationFootermenu\Support;

use Closure;
use Cyberline\FilamentNavigationFootermenu\Data\FooterMenuItem;
use InvalidArgumentException;

class ItemNormalizer
{
    /**
     * @param array<int, array<string, mixed>|FooterMenuItem> $items
     *
     * @return array<int, FooterMenuItem>
     */
    public static function normalize(array $items): array
    {
        $out = [];

        foreach ($items as $item) {
            if ($item instanceof FooterMenuItem) {
                $out[] = $item;
            } elseif (is_array($item)) {
                $out[] = self::fromArray($item);
            } else {
                throw new InvalidArgumentException('Invalid footer menu item type.');
            }
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): FooterMenuItem
    {
        if (! isset($data['label']) || ! is_string($data['label'])) {
            throw new InvalidArgumentException('Footer menu item array requires a string "label" key.');
        }

        $item = FooterMenuItem::make($data['label']);

        if (array_key_exists('icon', $data)) {
            $icon = $data['icon'];
            if ($icon === null || is_string($icon) || $icon instanceof Closure) {
                $item->icon($icon);
            } else {
                throw new InvalidArgumentException('Invalid "icon" for footer menu item.');
            }
        }

        if (array_key_exists('url', $data)) {
            $url = $data['url'];
            if (is_string($url) || $url instanceof Closure || $url === null) {
                $item->url($url);
            } else {
                throw new InvalidArgumentException('Invalid "url" for footer menu item.');
            }
        }

        $newTab = $data['open_in_new_tab'] ?? $data['openUrlInNewTab'] ?? $data['new_tab'] ?? false;
        if (is_bool($newTab) || $newTab instanceof Closure) {
            $item->openUrlInNewTab($newTab);
        }

        if (array_key_exists('visible', $data)) {
            $visible = $data['visible'];
            if (is_bool($visible) || $visible instanceof Closure) {
                $item->visible($visible);
            }
        }

        if (array_key_exists('badge', $data)) {
            $badge = $data['badge'];
            $badgeColor = $data['badge_color'] ?? $data['badgeColor'] ?? null;
            if (is_string($badge) || is_int($badge) || $badge instanceof Closure || $badge === null) {
                if ($badgeColor === null || is_string($badgeColor) || $badgeColor instanceof Closure) {
                    $item->badge($badge, $badgeColor);
                }
            }
        }

        if (array_key_exists('badge_tooltip', $data) || array_key_exists('badgeTooltip', $data)) {
            $badgeTooltip = $data['badge_tooltip'] ?? $data['badgeTooltip'] ?? null;

            if ($badgeTooltip === null || is_string($badgeTooltip) || $badgeTooltip instanceof Closure) {
                $item->badgeTooltip($badgeTooltip);
            }
        }

        return $item;
    }
}
