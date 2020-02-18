## 1.Added Doctrine implementation
### 1. Entities
* Entities should be placed in `Entity` directory on each module
* Mapping driver is Annotation-based preconfigured
* Each entity **must** follow Doctrine's standards

### 2. Configuration
In order to get Doctrine working you have to set up parameters like in any other library added earlier using [DoctrineConfig](/src/Mmi/Doctrine/DoctrineConfig.php) in `\App\Registry`

### 3. Usage
Because of how Doctrine is working, you won't be able to use `Active Record's` features anymore (->save()).
Read more on official docs [Querying data](https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/working-with-objects.html#querying) and [Persisting objects](https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/working-with-objects.html#persisting-entities), [Working with repositories](https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/tutorials/getting-started.html#entity-repositories)

### 4. EntityManager accessibility
EntityManager once configured is stored in application registry, accessible by `\App\Registry::$entityManager`

### Useful links:
* [Annotations reference](https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/annotations-reference.html)
* [Relations](https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/association-mapping.html#association-mapping)
* [QueryBuilder](https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/query-builder.html#the-querybuilder)
* **[Complete documentation](https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/tutorials/getting-started.html#getting-started-with-doctrine)**

## 2. Deprecations
Everything under *\Orm, *\Db got deprecated since 3.11 to be removed in 4.0


## 2. Added Twig support
### 1. Installation
Composer update is required to download corresponding twig packages, once done adding FrontController plugin is required.
Plugin is located under [PneFrontControllerTwigPathPlugin](/src/Mmi/App/PneFrontControllerTwigPathPlugin.php), and adding this into front controller is as simple as:
```php
<?php

namespace App;

class ConfigDEV extends Config {

    public function __construct() {

        parent::__construct();//konfiguracja hosta
        /// [...]
        $this->plugins = ['\Mmi\App\PneFrontControllerTwigPathPlugin', ...];
    }

}

```
### 2. Twig templates
* In order to use twig, you need to create copy of your existing template under the same path but with changed extension - .html.twig instead of .tpl
* Every template has access to helpers previously created in MMI, they do work the same way as before.
### 3. Overriding templates
* Every template should be placed in same directory as it's original .tpl, this may be taken as simple override of .tpl's.
* Every template that need's to be overridden can be placed inside /templates/{moduleName}/{directory}/{templateName}.html.twig
#### Example 1
You want to override template that exist's inside /src/Module/Resource/template/directory/template.tpl
```
/src/Module/Resource/template/directory/template.tpl // original
/src/Module/Resource/template/directory/template.html.twig // overridden
```

#### Example 2
You want to override previously overridden template, that do exist inside /src/Module/Resource/template/directory/template.html.twig
In this example, the directory structure is as simple as possible and is constructed by:
* /templates <- this is top bottom directory for every template replacement
* /module <- this is module name, structure based
* /directory <- this is directory taken from it's original source, it's name must be exact as in original one
* template.html.twig <- is the name of overridden file
```
/src/Module/Resource/template/directory/template.html.twig // original
/templates/module/directory/template.html.twig // overridden
```

#### Example 3
If you want to override template that's placed in vendor you must follow Example #2 in order to properly render template.
