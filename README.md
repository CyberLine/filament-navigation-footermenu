# Filament Navigation Footer Menu

A **Filament v4, v5** panel plugin that adds a **configurable footer navigation block** to your panel:

- **Sidebar mode** (default navigation): a **collapsible group** at the bottom of the sidebar, aligned with Filament’s native navigation styling. Items expand **upward** so the trigger stays at the bottom (useful above the user area).
- **Top navigation mode** (`$panel->topNavigation()`): a **compact icon trigger** with a **dropdown** menu, placed in the top bar with correct spacing next to the user menu.

Styling uses Filament’s own components and CSS tokens, so **light/dark mode** matches the rest of your panel.

---

## Requirements

| Requirement | Version                       |
|-------------|-------------------------------|
| PHP | `^8.2`                        |
| Laravel (illuminate components) | `^11.0` \| `^12.0` \| `^13.0` |
| Filament | `^4.0` \| `^5.0`              |

---

## Installation

### 1. Install the package

**From Packagist:**

```bash
composer require cyberline/filament-navigation-footermenu
```

**From a GitHub/VCS repository** (before Packagist, or for forks):

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/CyberLine/filament-navigation-footermenu.git"
        }
    ],
    "require": {
        "cyberline/filament-navigation-footermenu": "^1.0"
    }
}
```

**Local path repository** (monorepo or development):

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "packages/filament-navigation-footermenu",
            "options": { "symlink": true }
        }
    ],
    "require": {
        "cyberline/filament-navigation-footermenu": "^1.0"
    }
}
```

> **Stable installs:** this package declares a concrete `version` in `composer.json` so projects with `"minimum-stability": "stable"` can require it without treating the VCS checkout as `dev-main`.

### 2. Register the Laravel service provider

The package registers `FooterMenuServiceProvider` via Composer’s `extra.laravel.providers` for **package discovery**.

In fresh clones or CI, if `bootstrap/cache/packages.php` is missing or `package:discover` did not run, you may see:

`No hint path defined for [filament-navigation-footermenu].`

Alternatively, ensure `composer install` / `php artisan package:discover` completes successfully after every deploy.

### 3. Register the Filament plugin on your panel

In your `PanelProvider` (e.g. `app/Providers/Filament/AdminPanelProvider.php`), add the plugin to the panel’s `plugins` array:

```php
use Cyberline\FilamentNavigationFootermenu\Data\FooterMenuItem;
use Cyberline\FilamentNavigationFootermenu\FooterMenuPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugins([
            FooterMenuPlugin::make()
                ->triggerLabel('Legal')
                ->triggerIcon('heroicon-o-scale')
                ->items([
                    FooterMenuItem::make('Privacy policy')
                        ->icon('heroicon-o-shield-check')
                        ->url(fn () => route('legal.privacy'))
                        ->openUrlInNewTab(),
                ]),
        ]);
}
```

No `->register()` call on the plugin is required beyond Filament’s normal plugin registration on the panel.

---

## Publishing (optional)

Publish and customize config, views, or translations:

```bash
php artisan vendor:publish --tag=filament-navigation-footermenu-config
php artisan vendor:publish --tag=filament-navigation-footermenu-views
php artisan vendor:publish --tag=filament-navigation-footermenu-lang
```

After publishing views, your copies live under `resources/views/vendor/filament-navigation-footermenu/`.

---

## Configuration

Default values are merged from `config/filament-navigation-footermenu.php`.

| Key | Purpose |
|-----|---------|
| `trigger.label` | Default trigger label if you do not call `->triggerLabel()` on the plugin. |
| `trigger.icon` | Default Heroicon name for the trigger when you do not call `->triggerIcon()`. |
| `heading` | Optional heading shown above the **topbar dropdown** list (sidebar group does not use it for the default list UI). |
| `items` | Array of item definitions (see **Array-based items**). Used when you do **not** call `->items()` on the plugin. |

You can leave everything in config, override everything in the panel, or mix (plugin fluent API wins over config for the options you set).

---

## Usage

### Fluent API (`FooterMenuPlugin`)

All chainable methods return `static` for fluent configuration.

| Method | Description |
|--------|-------------|
| `triggerLabel(string\|Closure\|null $label)` | Label for the sidebar group / accessible name for the topbar icon. |
| `triggerIcon(string\|Closure\|null $icon)` | Heroicon (or Filament-supported icon) for the trigger. |
| `heading(string\|Closure\|null $heading)` | Optional heading in the **topbar** dropdown. |
| `items(array\|Closure $items)` | Menu entries: `FooterMenuItem` instances and/or arrays (see below). |
| `view(?string $bladeView)` | Custom Blade view for item **content** (sidebar list and/or dropdown body). |
| `visible(bool\|Closure $condition)` | Hide the entire footer menu when `false`. |
| `sort(int $sort)` | Reserved for ordering relative to other plugins (Filament plugin sort). |
| `extraAttributes(array\|Closure $attributes)` | Extra HTML attributes merged onto the **root** wrapper (e.g. `class` for a top border). |

**Example – separator from main nav (Tailwind classes):**

```php
FooterMenuPlugin::make()
    ->extraAttributes([
        'class' => 'border-t border-gray-200 pt-3 dark:border-white/10',
    ])
    ->triggerLabel('Legal')
    ->items([ /* ... */ ]);
```

### Fluent item API (`FooterMenuItem`)

```php
FooterMenuItem::make(string $label)
    ->icon(?string $icon)                         // Heroicon name, etc.
    ->url(string|Closure|null $url)             // Resolved to string for links
    ->openUrlInNewTab(bool|Closure $open = true)
    ->visible(bool|Closure $visible)
    ->badge(string|int|Closure|null $badge, string|Closure|null $color = null)
    ->badgeTooltip(string|Closure|null $tooltip)
```

**Resolved row shape** (useful inside custom Blade): `label`, `icon`, `url`, `open_in_new_tab`, `badge`, `badge_color`, `badge_tooltip`.

### Array-based items

Arrays are normalized to `FooterMenuItem` internally. Supported keys:

| Key | Aliases | Notes |
|-----|---------|--------|
| `label` | — | **Required**, string. |
| `icon` | — | Optional string or `Closure`. |
| `url` | — | String, `Closure`, or `null` (`#` when null). |
| `open_in_new_tab` | `openUrlInNewTab`, `new_tab` | Boolean or `Closure`. |
| `visible` | — | Boolean or `Closure`. |
| `badge` | — | String, int, `Closure`, or `null`. |
| `badge_color` | `badgeColor` | Optional string or `Closure`. |
| `badge_tooltip` | `badgeTooltip` | Optional string or `Closure`. |

**Example:**

```php
FooterMenuPlugin::make()->items([
    [
        'label' => 'Imprint',
        'icon' => 'heroicon-o-identification',
        'url' => fn () => route('legal.imprint'),
        'new_tab' => true,
        'badge' => '!',
        'badge_color' => 'danger',
        'badge_tooltip' => 'Action required',
    ],
]);
```

### Dynamic items with `Closure`

```php
FooterMenuPlugin::make()->items(fn () => [
    FooterMenuItem::make('Dashboard')
        ->url(fn () => url('/'))
        ->visible(fn () => auth()->user()?->isAdmin() ?? false),
]);
```

### Custom Blade view

Point the plugin at your own view:

```php
FooterMenuPlugin::make()
    ->view('components.my-footer-menu');
```

Your view is included with the following variables (non-exhaustive):

| Variable | Description |
|----------|-------------|
| `$plugin` | The `FooterMenuPlugin` instance. |
| `$layout` | `'sidebar'` or `'topbar'`. |
| `$heading` | Resolved heading string or null. |
| `$triggerLabel` | Resolved trigger label. |
| `$triggerIcon` | Resolved trigger icon or null. |
| `$items` | Array of **resolved** item rows (see shape above). |
| `$topbarAfterUserMenu` | `bool` – `true` when the topbar UI is rendered **after** the user menu (inside `.fi-topbar-end`). Useful if your custom markup needs different spacing. |

When a custom view is set, the plugin still renders the outer shell (sidebar group / dropdown); your view replaces the **default list of links** only.

---

## Behaviour & layout

### Sidebar (default navigation)

- Registered on `PanelsRenderHook::SIDEBAR_FOOTER`.
- Renders **below** other sidebar footer content (e.g. user menu when shown in the sidebar), matching Filament’s footer hook order.
- Uses Filament sidebar group / item classes so spacing matches the main navigation.

### Top navigation

- When the panel uses top navigation, the plugin does **not** render in the sidebar footer hook for that panel state.
- **Primary placement:** `PanelsRenderHook::USER_MENU_AFTER` — output sits **inside** `.fi-topbar-end`, so Filament’s `gap-x-4` applies between the user menu and your trigger (no overlap with the avatar).
- **Fallback:** if there is **no** user menu in the topbar (`userMenu(false)` or user menu only in the sidebar), the plugin renders on `PanelsRenderHook::TOPBAR_END` instead, with extra margin so the trigger does not collide with adjacent controls.

The topbar trigger is an `x-filament::icon-button` with a circular hit area and hover styles consistent with Filament.

### Visibility and tenancy

The plugin does not render when:

- the user is **not authenticated** in the current panel context, or  
- the panel has **tenancy** enabled and **no tenant** is resolved yet, or  
- `->visible(false)` (or a `Closure` resolving to `false`) is set on the plugin.

Use `->visible()` for feature flags, permissions, or environment-specific menus.

---

## Translations

The package ships with **English** and **German** language files under the `filament-navigation-footermenu` translation namespace.

Example:

```php
__('filament-navigation-footermenu::footermenu.default_trigger_label');
```

Publish if you need to override strings per app.

---

## Tailwind / Vite (Filament theme)

If you customize Filament’s Vite theme, ensure paths that scan Blade **include** this package’s views (or your published copies), so Tailwind does not purge rarely used classes.

Typical Filament theme `content` entries include `vendor/filament/**/*.blade.php`; confirm your setup includes vendor Blade paths you rely on.

---

## License

MIT — see [LICENSE.md](LICENSE.md).
