<?php

return [
    'status' => [
        'up' => 'funkční',
        'down' => 'nefunkční',
    ],
    'failures' => [
        'connection_failed' => 'K webu se nepodařilo připojit.',
        'credentials_not_allowed' => 'URL s přihlašovacími údaji nejsou povoleny.',
        'http_error' => 'Web vrátil chybovou odpověď.',
        'invalid_url' => 'URL webu není platná.',
        'local_target' => 'Místní cíle nejsou povoleny.',
        'non_public_address' => 'Cíl směřuje na neveřejnou adresu.',
        'timeout' => 'Web neodpověděl před vypršením časového limitu.',
        'tls_failed' => 'Ověření důvěryhodnosti TLS nebo názvu hostitele selhalo.',
        'too_many_redirects' => 'Web překročil limit pěti přesměrování.',
        'unresolved_host' => 'Název hostitele se nepodařilo přeložit.',
        'unsupported_port' => 'Podporovány jsou pouze porty 80 a 443.',
        'unsupported_scheme' => 'Podporovány jsou pouze adresy HTTP a HTTPS.',
    ],
];
