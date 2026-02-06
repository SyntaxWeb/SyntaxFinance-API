<?php

return [
    "paths" => ["api/*", "sanctum/csrf-cookie"],
    "allowed_methods" => ["*"],
    "allowed_origins" => [
        "https://financeiro.syntaxweb.com.br",
        "http://localhost:4001",
    ],
    "allowed_origins_patterns" => [],
    "allowed_headers" => ["*"],
    "exposed_headers" => [],
    "max_age" => 0,
    "supports_credentials" => false,
];
