<?php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'],  // saat dev, boleh wildcard
    // atau spesifik:
    // 'allowed_origins' => ['http://localhost:50854'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
<<<<<<< HEAD
];
=======


    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
    'allow_credentials' => true,

];
>>>>>>> 948bf3195e10a14806340403236962aafc92ec07
