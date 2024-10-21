# kynx/mezzio-openapi

Middleware and helpers for OpenAPI-based Mezzio applications.

## Pipeline

Create pipeline [delegator factory]. This includes standard middleware (ProblemDetails, validation, modeller).

### Considerations

* Pipeline delegator factory should be opt-in from command line - too much stuff a user might want to customise later.

## Routing

Create pipeline and route [delegator factory]. This parses OpenAPI spec and add routes to Mezzio application.

Routes have the original OpenApi path set as an option so it is available to middleware for validation etc.  

### Considerations

* Authentication middleware may need to be added per-route, not in the pipeline

### Authentication

Add `mezzio/authentication` implementations. OAuth2 is a bitch.

### Validator

Use [openapi-psr7-validator]?

### Considerations

* Do we validate before or after authentication? Probably after: [Google Cloud Endpoints] could replace the authentication, but
doesn't really validate requests.

### RequestParser

Adds OpenApiRequestInterface attribute to request. This contains path, query, header and cookie parameters along with
parsed request body. Everything is strongly typed - ie query params converted to ints / model objects / etc.

TOOD: handling XML request bodies. Add / find an XML strategy to add to BodyParamsMiddleware. The schema includes extra
stuff on how the XML is formatted that we will need to figure out...

## Handlers

Handler per operation with `get` / `patch` / `post` / etc methods. Uses `operationId` for naming. Will need
to construct a default name (path + method?) if no `operationId` given.

## TODO

[ ] Sanitize hydration errors - ie `Cannot hydrate My\Api\Operation\Services\Search\Get\QueryParams: My\Api\Operation\Services\Search\Get\QueryParams::__construct(): Argument #2 ($search) must be of type ?string, array given, called in /Users/matt/www/lifecycle-calculator/src/Api/Operation/Services/Search/Get/QueryParamsHydrator.php on line 37`

## Resources

* https://cloud.google.com/endpoints/docs/openapi/deploy-endpoints-config#validating_openapijson_syntax
* https://github.com/GoogleCloudPlatform/endpoints-samples/blob/master/k8s/openapi.yaml
* https://github.com/lcobucci/jwt/issues/32
* https://github.com/steverhoades/oauth2-openid-connect-client

[delegator factory]: https://docs.mezzio.dev/mezzio/v3/cookbook/autowiring-routes-and-pipelines/#custom-delegator-factories
[openapi-psr7-validator]: https://github.com/thephpleague/openapi-psr7-validator
[Google Cloud Endpoints]: https://cloud.google.com/endpoints/docs/openapi/deploy-endpoints-config#validating_openapijson_syntax
