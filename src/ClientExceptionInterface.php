<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi;

/**
 * Client exceptions indicate a problem with the request
 *
 * Implementations MUST set an error code corresponding to an HTTP response code in the 400-499 range. The message MUST
 * be suitable for display to the client. They SHOULD provide getters for accessing any additional information that
 * would help applications localize their API error responses.
 */
interface ClientExceptionInterface extends ExceptionInterface
{
}
