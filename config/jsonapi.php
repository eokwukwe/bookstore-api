<?php

return [
  'resources' => [
    'authors' => [
      'allowedSorts' => [
        'name',
        'created_at',
        'updated_at',
      ]
    ],
    'books' => [
      'relationships' => [
        [
          'type' => 'authors',
          'method' => 'authors',
        ]
      ]
    ],
  ]
];
