#PHPCI-Deployer-Plugin

Simple plugin for [Deployer](http://deployer.org)
#How to use

Keyword of this plugin is simple. It means that you just need to define branch 
for configuration task name(if there is no task, plugin takes 
default value that is "deploy"), stage name(it would be just server name or defined stage)
and verbosity level(for default is normal)

#Sample configuration
```
  \ket4yii\PHPCI\Deployer\Plugin:
    master:
      task: "prod-dep"
      stage: "production" 
    development:
      task: "dev-dep"
      stage: "development"
```
