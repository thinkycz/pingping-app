<?php

return [
    'status' => [
        'up' => 'up',
        'down' => 'down',
    ],
    'failures' => [
        'connection_failed' => 'The website could not be reached.',
        'credentials_not_allowed' => 'URLs containing credentials are not allowed.',
        'http_error' => 'The website returned an error response.',
        'invalid_url' => 'The website URL is invalid.',
        'local_target' => 'Local targets are not allowed.',
        'non_public_address' => 'The target resolves to a non-public address.',
        'timeout' => 'The website did not respond before the timeout.',
        'tls_failed' => 'TLS trust or hostname verification failed.',
        'too_many_redirects' => 'The website exceeded five redirects.',
        'unresolved_host' => 'The website hostname could not be resolved.',
        'unsupported_port' => 'Only ports 80 and 443 are supported.',
        'unsupported_scheme' => 'Only HTTP and HTTPS URLs are supported.',
    ],
];
