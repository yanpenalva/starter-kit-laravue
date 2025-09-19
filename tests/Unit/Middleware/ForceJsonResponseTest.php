<?php
declare(strict_types = 1);

use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

describe('ForceJsonResponse Middleware', function () {
    it('sets accept header to json and modifies content-type in response', function () {
        $middleware = new ForceJsonResponse;
        $request = Request::create('/test-url', 'GET');

        $response = $middleware->handle($request, function ($req) {
            return new Response('Not JSON', 200, ['Content-Type' => 'text/plain']);
        });

        expect($request->header('Accept'))->toEqual('application/json');
        expect($response->headers->get('Content-Type'))->toEqual('application/json');
    });

    it('handles exceptions with json response', function () {
        $middleware = new ForceJsonResponse;
        $request = Request::create('/test-url', 'GET');

        $response = $middleware->handle($request, function ($req) {
            throw new Exception('Error occurred');
        });

        expect($response->getStatusCode())->toEqual(500);
        expect($response->headers->get('Content-Type'))->toEqual('application/json');
        expect($response->getContent())->toContain('Error occurred');
    });
})->group('middleware');
