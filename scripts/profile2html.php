<?php

require_once dirname(__DIR__).'/bootstrap.php';

use es\eucm\xapi\Profile2Html;

define('MIME_TYPE_HTML', 'text/html');
define('MIME_TYPE_JSON_LD', 'application/ld+json');

$mimeType = MIME_TYPE_HTML;
/*
 * -c <content-type> (e.g. application/ld+json, text/html)
 */
$options = getopt("c:i:");
$mimeType = isset($options['c']) ? $options['c'] : MIME_TYPE_HTML;
$profilePath = isset($options['i']) ? $options['i'] : NULL;
if (is_null($profilePath) || ! file_exists($profilePath)) {
    echo "profile input file missing";
    exit(1);
}

if (strcmp($mimeType, MIME_TYPE_JSON_LD) == 0) {
    $fp = fopen($profilePath, 'rb');
    fpassthru($fp);
    exit;
}

$generator = new Profile2Html($profilePath);
echo $generator->generate();
