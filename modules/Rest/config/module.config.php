<?php
return [
    'route' => [
        '/rest/{collection}/{id}' => [
            'module' => 'Rest',
            'controller' => 'rest',
            'action' => 'index',
        ],
        '/rest/{collection}' => [
            'module' => 'Rest',
            'controller' => 'rest',
            'action' => 'index',
        ],
        '/rest/{collection}/search/:params' => [
            'module' => 'Rest',
            'controller' => 'rest',
            'action' => 'search',
            'params' => 1,
        ],

    ],
];