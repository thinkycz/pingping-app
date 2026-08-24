<?php

namespace App\Monitoring;

use App\Monitoring\Contracts\TlsInspector;
use RuntimeException;

class OpenSslTlsInspector implements TlsInspector
{
    public function inspect(ResolvedTarget $target): CertificateInfo
    {
        $context = stream_context_create([
            'ssl' => [
                'allow_self_signed' => false,
                'capture_peer_cert' => true,
                'peer_name' => $target->host,
                'SNI_enabled' => true,
                'verify_peer' => true,
                'verify_peer_name' => true,
            ],
        ]);

        $errorNumber = 0;
        $errorMessage = '';
        $client = @stream_socket_client(
            'tls://'.$target->socketAddress(),
            $errorNumber,
            $errorMessage,
            5,
            STREAM_CLIENT_CONNECT,
            $context,
        );

        if ($client === false) {
            throw new RuntimeException('TLS verification failed.');
        }

        try {
            $parameters = stream_context_get_params($client);
            $certificate = $parameters['options']['ssl']['peer_certificate'] ?? null;
            $parsed = $certificate ? openssl_x509_parse($certificate) : false;

            if (! is_array($parsed) || ! isset($parsed['validTo_time_t'])) {
                throw new RuntimeException('TLS certificate details are unavailable.');
            }

            $expiresAt = now()->createFromTimestamp($parsed['validTo_time_t']);

            return new CertificateInfo($expiresAt->isFuture() ? 'Valid' : 'Invalid', $expiresAt);
        } finally {
            fclose($client);
        }
    }
}
