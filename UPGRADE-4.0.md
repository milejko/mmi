Added Doctrine implementation
### 1. Entities
* Entities should be placed in `Entity` directory on each module
* Mapping driver is Annotation-based preconfigured
* Each entity **must** follow Doctrine's standards

### 2. Configuration
In order to get Doctrine working you have to set up parameters as follows:
```
\App\Registry::$config->doctrine->host
\App\Registry::$config->doctrine->port
\App\Registry::$config->doctrine->dbName
\App\Registry::$config->doctrine->username
\App\Registry::$config->doctrine->password
```

### 3. Useful links:
* [Annotations reference](https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/annotations-reference.html)
* [Relations](https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/association-mapping.html#association-mapping)
* [QueryBuilder](https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/query-builder.html#the-querybuilder)
* [Working with repositories](https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/tutorials/getting-started.html#entity-repositories)
* **[Complete documentation](https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/tutorials/getting-started.html#getting-started-with-doctrine)**
