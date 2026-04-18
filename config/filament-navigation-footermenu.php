<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Trigger (Button in Sidebar / Topbar)
    |--------------------------------------------------------------------------
    */
    'trigger' => [
        'label' => null,
        'icon' => 'heroicon-o-information-circle',
    ],

    /*
    |--------------------------------------------------------------------------
    | Sheet heading (optional)
    |--------------------------------------------------------------------------
    */
    'heading' => null,

    /*
    |--------------------------------------------------------------------------
    | Menu items (arrays; use FooterMenuItem in Panel for fluent API)
    |--------------------------------------------------------------------------
    |
    | Keys per item:
    | - label (string, required)
    | - icon (string|null)
    | - url (string|\Closure)
    | - new_tab / open_in_new_tab (bool)
    | - visible (bool|\Closure, default true)
    | - badge (string|int|\Closure|null)
    | - badge_color (string|null)
    | - badge_tooltip (string|null)
    |
    */
    'items' => [
        // [
        //     'label' => 'Impressum',
        //     'icon' => 'heroicon-o-identification',
        //     'url' => fn () => route('public.impressum'),
        //     'new_tab' => true,
        // ],
    ],

];
