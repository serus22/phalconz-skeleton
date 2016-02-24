<?php
return [
    'route' => [
        '/' => [
            'module' => 'App',
            'controller' => 'index',
            'action' => 'index',
        ],
        '/foo' => [
            'module' => 'App',
            'controller' => 'another',
            'action' => 'foo',
        ],
    ],
];