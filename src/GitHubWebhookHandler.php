<?php
namespace es\eucm\xapi;

/**
 * Based on:
 *  - https://gist.github.com/jadb/7cb2c0053a756eb80d58
 *  - https://developer.github.com/guides/delivering-deployments/
 */
class GitHubWebhookHandler
{

    private $hookSecret;

    public function __construct($hookSecret, $profileGenerationQueueFolder, $xapiProfileRepoPath)
    {
        $this->hookSecret = $hookSecret;
        $this->profileGenerationQueueFolder = $profileGenerationQueueFolder;
        $this->xapiProfileRepoPath = $xapiProfileRepoPath;
    }

    public function handle()
    {
        $rawPost = $this->checkSignature();
        $this->checkContentTypeHeader();
        $this->processEvent($rawPost);
    }

    private function checkSignature()
    {
        // Validates signed event
        $rawPost = false;
        if ($this->hookSecret) {
            if (!isset($_SERVER['HTTP_X_HUB_SIGNATURE'])) {
                throw new \Exception("HTTP header 'X-Hub-Signature' is missing.");
            } elseif (!extension_loaded('hash')) {
                throw new \Exception("Missing 'hash' extension to check the secret code validity.");
            }
            list($algo, $hash) = explode('=', $_SERVER['HTTP_X_HUB_SIGNATURE'], 2) + array('', '');
            if (!in_array($algo, hash_algos(), TRUE)) {
                throw new \Exception("Hash algorithm '$algo' is not supported.");
            }
            $rawPost = file_get_contents('php://input');
            $hashedPost = hash_hmac($algo, $rawPost, $this->hookSecret);
            if ((version_compare(PHP_VERSION, '5.6', '>=') && !hash_equals($hash, $hashedPost)) || $hash !== $hashedPost) {
                throw new \Exception('Hook secret does not match.');
            }
        }
	return $rawPost;
    }

    private function checkContentTypeHeader()
    {
        // Check event content-type
        if (!isset($_SERVER['CONTENT_TYPE'])) {
            throw new \Exception("Missing HTTP 'Content-Type' header.");
        } elseif (!isset($_SERVER['HTTP_X_GITHUB_EVENT'])) {
            throw new \Exception("Missing HTTP 'X-Github-Event' header.");
        }
    }

    private function processEvent($rawPost = NULL)
    {
        switch ($_SERVER['CONTENT_TYPE']) {
            case 'application/json':
                $json = $rawPost ?: file_get_contents('php://input');
            break;
            case 'application/x-www-form-urlencoded':
                $json = $_POST['payload'];
            break;
            default:
                throw new \Exception("Unsupported content type: $_SERVER[HTTP_CONTENT_TYPE]");
        }
        // Payload structure depends on triggered event https://developer.github.com/v3/activity/events/types/
        $payload = json_decode($json);
        switch (strtolower($_SERVER['HTTP_X_GITHUB_EVENT'])) {
            case 'ping':
                echo 'pong';
            break;
            case 'pull_request':
                $this->processPullRequest($payload);
            break;
            default:
                echo '^_^ nothing to do';
            break; 
        }
    }

    private function processPullRequest($payload)
    {
        if ($payload->action === 'closed' && $payload->pull_request->merged && $payload->pull_request->base->ref == 'master') {
            $this->generateProfile($payload->pull_request);
        }
    }

    function generateProfile($pullRequest)
    {
        $repoUrl = $pullRequest->base->repo->clone_url;
        $ref = $pullRequest->merge_commit_sha;

        $work= $this->profileGenerationQueueFolder.DIRECTORY_SEPARATOR.$ref.'.profile';

        $fp = fopen($work, 'wx+');
        fputs($fp, $repoUrl.PHP_EOL);
        fputs($fp, $this->xapiProfileRepoPath);
        fclose($fp);

        http_response_code(202);
        echo "Queuing HTML profile generation";
    }
}
