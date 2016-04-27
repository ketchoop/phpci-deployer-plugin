#PHPCI-Deployer-Plugin

Simple plugin for [Deployer](http://deployer.org)

#How to use

Keyword of this plugin is simple. It means that you just need to define branch 
for configuration task name(if there is no task, plugin takes 
default value that is "deploy"), stage name(it would be just server name or defined stage)
and verbosity level(for default is normal)

##Plugin options

* stage(*required*) - Stage or server name
* task(*optional*) - Task name (*default value is deploy*) 
* verbosity(*optional*) - Add verbose mode to plugin execution
  * normal
  * verbose
  * very verbose
  * debug
  * quiet 

#Sample configuration
```
\Ket4yii\PHPCI\Deployer\Plugin:
  master: 
    task: sample-task #optional, default task is deploy 
    stage: production #required, name of stage or server
    verbose: debug #optional, default is normal(no verbosity)
  development:
    task: sample-task #optional, default task is deploy 
    stage: development #required, name of stage or server

```
