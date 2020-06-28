<?php

return [
  'resources' => [
    'users' => [
      'allowedSorts' => [
        'name',
        'email'
      ],
      'allowedFilters' => [
        Spatie\QueryBuilder\AllowedFilter::exact('role'),
      ],
      'allowedIncludes' => [],
      'validationRules' => [
        'create' => [
          'data.attributes.name' => 'required|string',
          'data.attributes.email' => 'required|email',
          'data.attributes.password' => 'required|string',
        ],
        'update' => [
          'data.attributes.name' => 'sometimes|required|string',
          'data.attributes.email' => 'sometimes|required|email',
          'data.attributes.password' => 'sometimes|required|string',
        ]
      ],
      'relationships' => []
    ],
    'authors' => [
      'allowedSorts' => [
        'first_name',
        'created_at',
        'updated_at',
      ],
      'allowedFilters' => [],
      'allowedIncludes' => [
        'books'
      ],
      'validationRules' => [
        'create' => [
          'data.attributes.first_name' => 'required|string',
          'data.attributes.last_name' => 'required|string',
          'data.attributes.other_name' => 'string',
        ],
        'update' => [
          'data.attributes.first_name' => 'sometimes|required|string',
          'data.attributes.last_name' => 'sometimes|required|string',
          'data.attributes.other_name' => 'sometimes|required|string',
        ]
      ],
      'relationships' => [
        [
          'type' => 'books',
          'method' => 'books',
        ]
      ]
    ],
    'books' => [
      'allowedSorts' => [
        'title',
        'publication_year',
        'created_at',
        'updated_at',
      ],
      'allowedFilters' => [],
      'allowedIncludes' => [
        'authors'
      ],
      'validationRules' => [
        'create' => [
          'data.attributes.title' => 'required|string',
          'data.attributes.description' => 'required|string',
          'data.attributes.publication_year' => 'required|string',
        ],
        'update' => [
          'data.attributes.title' => 'sometimes|required|string',
          'data.attributes.description' => 'sometimes|required|string',
          'data.attributes.publication_year' => 'sometimes|required|string',
        ]
      ],
      'relationships' => [
        [
          'type' => 'authors',
          'method' => 'authors',
        ]
      ]
    ],
  ]
];
