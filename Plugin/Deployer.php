<?php
/**
 * Deployer plugin for PHPCI
 *
 * @author  Alexey Boyko <ket4yiit@gmail.com>
 * @license MIT 
 *   https://github.com/ket4yii/phpci-deployer-plugin/blob/master/LICENSE
 *
 * @link https://github.com/ket4yii/phpci-deployer-plugin
 * @see  http://deployer.org
 */

namespace Ket4yii\PHPCI\Deployer\Plugin;

use PHPCI\Builder;
use PHPCI\Model\Build;

class Deployer implements \PHPCI\Plugin
{

    protected $phpci; 
    protected $build;
    protected $config;
    protected $dep;
    protected $branch;
  
    /**
   * Standard Constructor
   *
   * $options['directory'] Output Directory. Default: %BUILDPATH%
   * $options['filename']  Phar Filename. Default: build.phar
   * $options['regexp']    Regular Expression Filename Capture. Default: /\.php$/
   * $options['stub']      Stub Content. No Default Value
   *
   * @param Builder $phpci   PHPCI instance 
   * @param Build   $build   Build instance 
   * @param array   $options Plugin options 
   */
    public function __construct(
        Builder $phpci,
        Build $build,
        array $options = array()
    ) {
        $this->phpci = $phpci;
        $this->build = $build; 
        $this->config = $options;

        $this->dep = $this->phpci->findBinary('dep');
        $this->branch = $this->build->getBranch();
    }

    /**
   * PHPCI plugin executor.
   *
   * @return bool Did plugin execute successfully 
   */
    public function execute() 
    {
        if (($validationResult = $this->validateConfig()) !== null) {
            $this->phpci->log($validationResult['message']);

            return $validationResult['successful']; 
        }

        $branchConfig = $this->config[$this->branch];
        $options = $this->getOptions($branchConfig);
    
        $deployerCmd = "$this->dep $options";

        return $this->phpci->executeCommand($deployerCmd);
    }

    /**
   * Validate config.
   *
   * $validationRes['message'] Message to log
   * $validationRes['successful'] Plugin status that is connected with error
   *
   *  @return array validation result
   */
    protected function validateConfig() 
    {
        if (empty($this->config)) {
            return [
                'message' => 'Can\'t find configuration for plugin!',
                'successful' => false
            ];
        }

        if (empty($this->config[$this->branch])) {
            return [
                'message' => 'There is no specified config for this branch.',
                'successful' => true
            ];
        }

        $branchConf = $this->config[$this->branch];

        if (empty($branchConf['stage'])) {
            return [
                'message' => 'There is no stage for this branch',
                'successful' => false
            ];
        }

        return null;
    }

    /**
   * Get verbosity flag.
   * 
   * @param string $verbosity User defined verbosity level
   *
   * @return string Verbosity flag
   */
    protected function getVerbosityOption($verbosity) 
    {
        $LOG_LEVEL_ENUM = [
            'verbose' =>'v',
            'very verbose' => 'vv',
            'debug' => 'vvv',
            'quiet' => 'q'
        ];

        $verbosity = strtolower(trim($verbosity));

        if ($verbosity !== 'normal') {
            return '-' . $LOG_LEVEL_ENUM[$verbosity]; 
        } else {
            return '';
        }
    }

    /**
   * Make deployer options from config
   *
   * @param array $config Deployer configration array
   *
   * @return string Deployer options
   */
    protected function getOptions($config) 
    {
        $options = [];

        if ($config['task'] != null) {
            $options[] = $config['task']; 
        } else {
            $options[] = 'deploy';
        }

        if ($config['stage'] != null) {
            $options[] = $config['stage'];
        }

        if ($config['verbosity'] != null) {
            $options[] = $this->getVerbosityOption($config['verbosity']);
        }

        if ($config['file'] != null) {
            $options[] = '--filename= ' . $config['filename'];
        }

        return implode(' ', $options);
    }
}
