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
