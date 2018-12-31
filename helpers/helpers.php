<?php

if (!function_exists('getJson')) {
    function getJson(\Psr\Http\Message\ResponseInterface $response, $assoc = false)
    {
        return json_decode($response->getBody()->getContents(), $assoc);
    }
}
