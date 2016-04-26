<?php

namespace ket4yii\PHPCI\Deployer\Plugin;

use PHPCI\Builder;
use PHPCI\Model\Build;

class Deployer implements \PHPCI\Plugin {

  protected $phpci; 
  protected $build;
  protected $config;
  protected $dep;
  
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
  }

  public function execute() {
    $branch = $this->build->getBranch();
    $task = 'deploy'; //default task is deploy
    $verbosity = ''; //default verbosity is normal

    if (($validationResult = $this->validateConfig()) === NULL) {
      $this->phpci->log($validationResult['message']);

      return $validationResult['successful']; 
    }

    $branchConfig = $this->config[$branch];

    //TODO: delete redundant brackets
    if (!(empty($branchConfig['task']))) {
      $task = $branchConfig['task']; 
    }

    $stage = $branchConfig['stage'];
    $verbosity = $this->getVerbosityLevel($branchConfig['verbosity']);
    
    $deployerCmd = "$this->dep -$verbosity $task $stage"; 

    return $this->phpci->executeCommand($deployerCmd);
  }

  protected function validateConfig() {
    if (empty($this->config)) {
      return [
        'message' => 'Can\'t find configuration for plugin!',
        'successful' => false
      ];
    }

    if (empty($this->config[$branch])) {
      return [
        'message' => 'There is no specified config for this branch.',
        'successful' => true
      ];
    }

    return null;
  }

  protected function getVerbosityLevel($verbosity) {
    $LOG_LEVEL_ENUM = [
      'normal' => '',
      'verbose' =>'v',
      'very verbose' => 'vv',
      'debug' => 'vvv',
      'quiet' => 'q'
    ];

    $verbosity = strtolower(trim($verbosity));
 
    return $LOG_LEVEL_ENUM[$verbosity];
  }
}
