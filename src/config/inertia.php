<?php

return [

    'ssr' => [
        'enabled' => false,
    ],

    'testing' => [

        // ディレクトリ型 (Pages/Web/Top/index.tsx) を採用しているため
        // Inertia 標準の存在チェック（Pages/Web/Top.tsx を想定）が常に失敗する。
        // テストでは false にして、コンポーネント名の型一致は assertInertia で検証する。
        'ensure_pages_exist' => false,

        'page_paths' => [
            resource_path('js/Pages'),
        ],

        'page_extensions' => [
            'js', 'jsx', 'ts', 'tsx',
        ],

    ],

];
