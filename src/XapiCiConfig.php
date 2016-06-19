<?php
namespace es\eucm\xapi;

/**
 *
 * .xapi-ci.json
 * <pre>
 * {
 *     "profilesPaths" : ["miProfile.jsonld"]
 * }
 * </pre>
 */
class XapiCiConfig
{
    const CONFIG_FILE_NAME = '.xapi-ci.json';

    private $config;

    public function __construct($rootRepoPath)
    {
        if ($rootRepoPath === null || ($len = mb_strlen($rootRepoPath)) === 0) {
            throw new \Exception('Non empty $rootRepoPath expected');
        }
        
        $len = mb_strlen($rootRepoPath);
        $lastChar = StringUtils::str_split_unicode($rootRepoPath)[$len-1];
        if ( mb_strrpos($rootRepoPath, '/') !== ($len-1) ) {
            $rootRepoPath .= '/';
        }
        $configFile = $rootRepoPath.self::CONFIG_FILE_NAME;
        $this->config = json_decode(file_get_contents($configFile));
        if ($this->config === NULL) {
            throw new \Exception("Can not parse $configFile");
        }
    }
    
    public function getProfilesPaths() 
    {
        return $this->config->profilesPaths;
    }
}
