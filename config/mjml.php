<?php

declare(strict_types=1);

return [
    /**
     * The procedure to be used to render the Mjml templates.
     * This can be 'api' or 'cli'
     */
    'procedure' => env('MJML_PROCEDURE', 'api'),

    /**
     * When using the API procedure, the API credentials must be specified
     * https://mjml.io/api
     */
    'credentials' => [
        'application_id' => env('MJML_APP_ID'),
        'secret_key' => env('MJML_SECRET_KEY'),
    ],

    /**
     * The path to the mjml cli command, when the cli procedure is to be used
     * https://www.npmjs.com/package/mjml
     */
    'cli_path' => env('MJML_CLI_PATH', base_path('node_modules/.bin/mjml')),
];
