<?php

declare(strict_types=1);

namespace SignWell\Sdk;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/** @internal */
final class RateLimitRetry
{
    /**
     * @param array<string, mixed> $options
     * @return PromiseInterface<ResponseInterface, mixed>
     */
    public static function sendAsync(ClientInterface $client, RequestInterface $request, array $options, int $maxRetries): PromiseInterface
    {
        // The SDK maps status errors after the retry policy sees the response.
        $options['http_errors'] = false;
        $retry = Middleware::retry(
            static function (int $attempt, RequestInterface $request, ?ResponseInterface $response) use ($maxRetries): bool {
                if ($response === null || $response->getStatusCode() !== 429 || $attempt >= $maxRetries
                    || self::delay($response, $attempt) > 60000 || !$request->getBody()->isSeekable()) {
                    return false;
                }
                $request->getBody()->rewind();
                $response->getBody()->close();
                return true;
            },
            static fn (int $attempt, ?ResponseInterface $response): int => self::delay($response, $attempt - 1),
        );
        $handler = $retry(static fn (RequestInterface $request, array $options): PromiseInterface => $client->sendAsync($request, $options));
        return $handler($request, $options);
    }

    private static function delay(?ResponseInterface $response, int $attempt): int
    {
        $header = trim($response?->getHeaderLine('Retry-After') ?? '');
        if (preg_match('/^\d+$/D', $header)) {
            return (int) min(60001, (float) $header * 1000);
        }
        $date = preg_match('/^\w{3},/', $header) ? strtotime($header) : false;
        return $date === false ? 1000 * 2 ** min($attempt, 5) : (int) min(60001, max(0, $date - time()) * 1000);
    }
}
