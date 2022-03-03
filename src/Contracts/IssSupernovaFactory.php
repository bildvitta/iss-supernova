<?php

namespace Bildvitta\IssSupernova\Contracts;

interface IssSupernovaFactory
{
    public const DEFAULT_HEADERS = [
        'content-type' => 'application/json',
        'accept' => 'application/json',
        'User-Agent' => 'iss-supernova v0.0.1-alpha',
    ];

    public const DEFAULT_OPTIONS = ['allow_redirects' => false];
}
