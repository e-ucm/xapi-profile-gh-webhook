<?php

// Github webhook secret (see: https://developer.github.com/webhooks/creating/)
define('GITHUB_WEBHOOK_SECRET', 'XXXXXYYYYYZZZZZ');

// Folder where tasks will be added
define('XAPI_PROFILE_GENERATOR_QUEUE_FOLDER', __DIR__.'/cron');

// Public folder to serve the profile
define('XAPI_PROFILE_PUBLIC_SITE', '/path/to/public/web/site');

// Relative path within profile's GitHub repository that points to the JSON-LD profile definition
define('XAPI_REPO_PROFILE_PATH', 'myProfile.jsonld');

