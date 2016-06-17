<?php
require_once dirname(__DIR__).'/bootstrap.php';

use es\eucm\xapi\GitHubWebhookHandler;

$handler = new GitHubWebhookHandler(GITHUB_WEBHOOK_SECRET, XAPI_PROFILE_GENERATOR_QUEUE_FOLDER, XAPI_REPO_PROFILE_PATH, GITHUB_PERSONAL_ACCESS_TOKEN);
$handler->handle();
