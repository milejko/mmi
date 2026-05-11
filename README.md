# MMi Framework — Developer Guidebook

MMi is a PHP full-stack web framework built for speed and flexibility. It supports
everything from personal blogs to high-traffic applications and follows an MVC
architecture with a DI-container core, a fluent ORM, a form builder, and a rich set
of standalone utilities.

- **GitHub**: <https://github.com/milejko/mmi>
- **License**: New BSD License
- **PHP**: ≥ 8.1

---

## Table of Contents

1. [Installation](#installation)
2. [Project Structure](#project-structure)
3. [Configuration (.env)](#configuration-env)
4. [Dependency Injection & Application Bootstrap](#dependency-injection--application-bootstrap)
5. [MVC — Controllers, Views & Routing](#mvc--controllers-views--routing)
6. [ORM](#orm)
7. [Database Layer (Db)](#database-layer-db)
8. [Forms](#forms)
9. [Validators](#validators)
10. [Filters](#filters)
11. [Cache](#cache)
12. [Session](#session)
13. [Security — Auth & ACL](#security--auth--acl)
14. [Translate / i18n](#translate--i18n)
15. [Event Manager](#event-manager)
16. [HTTP — Request & Response](#http--request--response)
17. [Image Processing](#image-processing)
18. [Paginator](#paginator)
19. [Navigation](#navigation)
20. [LDAP](#ldap)
21. [Logging](#logging)
22. [CLI Commands](#cli-commands)
23. [Core Utilities](#core-utilities)
24. [Quality Tooling](#quality-tooling)

---

## Installation

### Via Composer

```bash
composer require mmi/mmi
```

Or use the ready-made starter:

```bash
composer create-project mmi/mmi-standard my-project
```

### Development environment with Docker

A `Dockerfile` is included in the repository root for containerised development.

---

## Project Structure

```
├── src/
│   └── Mmi/               # Framework source
│       ├── App/           # Bootstrap & DI wiring
│       ├── Cache/         # Cache subsystem
│       ├── Command/       # CLI commands
│       ├── Db/            # PDO database adapter
│       ├── EventManager/  # Event dispatcher
│       ├── Filter/        # Value filters
│       ├── Form/          # Form builder
│       ├── Http/          # Request / Response
│       ├── Image/         # GD image helpers
│       ├── Ldap/          # LDAP client
│       ├── Log/           # Logger config
│       ├── Mvc/           # Controller, View, Router
│       ├── Navigation/    # Tree navigation & breadcrumbs
│       ├── Orm/           # Active-record ORM
│       ├── Paginator/     # Pagination helper
│       ├── Resource/      # Templates, i18n files, SQL incrementals
│       ├── Security/      # Auth & ACL
│       ├── Session/       # Session wrapper
│       ├── Translate/     # i18n translator
│       └── Validator/     # Value validators
├── tests/                 # PHPUnit test suite
├── var/cache/             # Compiled DI container & view templates
├── web/                   # Public document root
└── .env                   # Local configuration (gitignored)
```

---

## Configuration (.env)

Copy `.env.sample` to `.env` (and optionally `.env.local` for machine-local
overrides) and fill in the values. Both files are loaded automatically at bootstrap.

```ini
# Application
APP_DEBUG_ENABLED=true
APP_VIEW_CDN=https://cdn.example.com
APP_COMPILE_PATH=/var/www/var/cache
APP_TIME_ZONE=Europe/Warsaw

# Cache
CACHE_SYSTEM_ENABLED=true
CACHE_PUBLIC_ENABLED=true
CACHE_PUBLIC_HANDLER=file          # file | redis
CACHE_PUBLIC_PATH=/tmp/mmi-cache
CACHE_PUBLIC_DISTRIBUTED=false

# Database
DB_HOST=127.0.0.1
DB_USER=app
DB_NAME=app
DB_PASSWORD=secret

# Logging
LOG_HANDLER=syslog                 # syslog | stream | console | gelf | slack

# Session
SESSION_COOKIE_HTTP=true
SESSION_COOKIE_SECURE=true
SESSION_HANDLER=files              # files | redis
SESSION_NAME=mmi
SESSION_PATH=/tmp/mmi-sessions
```

---

## Dependency Injection & Application Bootstrap

MMi uses **PHP-DI** as its DI container. The container is compiled and cached
for production performance. APCu is used as an additional definition cache when
available.

### Web application

```php
define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/vendor/autoload.php';

(new \Mmi\App\App())->run();
```

### CLI application

```php
define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/vendor/autoload.php';

(new \Mmi\App\AppCli())->run();
```

### DI configuration files

Each module (including your own) can expose a `di.*.php` file returning a PHP-DI
definition array. The framework auto-discovers all such files from the application
structure and merges them (application definitions take precedence over vendor ones).

```php
// src/MyModule/di.mymodule.php
use function DI\autowire;
use function DI\get;

return [
    MyService::class => autowire()
        ->constructorParameter('config', get('my.config.key')),
    'my.config.key'  => 'some-value',
];
```

### Accessing the container

```php
// Inside a controller or service that receives ContainerInterface via constructor injection
$service = $container->get(MyService::class);

// Legacy static access (deprecated, avoid in new code)
$service = \Mmi\App\AppAbstract::$di->get(MyService::class);
```

---

## MVC — Controllers, Views & Routing

### Controllers

Controllers extend `\Mmi\Mvc\Controller`. Action methods are plain public methods
whose names end with `Action`.

```php
namespace MyModule\Controller;

use Mmi\Mvc\Controller;

class BlogController extends Controller
{
    // Called after __construct — use instead of overriding constructor
    public function init(): void
    {
        // runs before every action in this controller
    }

    public function indexAction(): void
    {
        // assign variables to the view
        $this->view->posts = $this->fetchPosts();
    }

    public function showAction(): void
    {
        $id = (int) $this->getRequest()->id;
        $this->view->post = (new PostQuery())->whereId()->equals($id)->findOne();
    }

    public function ajaxAction(): void
    {
        // disable the layout wrapper — useful for AJAX / API actions
        $this->view->setLayoutDisabled();
        $this->getResponse()->setTypeJson();
        $this->view->data = ['ok' => true];
    }
}
```

### Views (templates)

Templates are `.tpl` files stored under `src/<Module>/Resource/template/<module>/<controller>/<action>.tpl`.
They are compiled to plain PHP and cached for performance.

```php
<!-- src/MyModule/Resource/template/blog/index/index.tpl -->
<h1><?= $this->_('blog.title') ?></h1>

<?php foreach ($this->posts as $post): ?>
    <article>
        <h2><?= $this->escape($post->title) ?></h2>
        <p><?= $this->escape($post->body) ?></p>
        <a href="<?= $this->url(['module' => 'blog', 'controller' => 'blog', 'action' => 'show', 'id' => $post->id]) ?>">
            <?= $this->_('read.more') ?>
        </a>
    </article>
<?php endforeach ?>

<?= $this->widget('comment', 'index', 'list', ['postId' => 3]) ?>
```

Inside templates `$this` is the `View` object. Useful methods:

| Method | Description |
|---|---|
| `$this->_($key, $params)` | Translate a key |
| `$this->escape($value)` | HTML-escape a string |
| `$this->url($params)` | Build a URL from route params |
| `$this->widget($module, $controller, $action, $params)` | Render a sub-widget inline |
| `$this->navigation()` | Access the navigation helper |
| `$this->setPlaceholder($name, $content)` | Store content for the layout |
| `$this->getPlaceholder($name)` | Read a placeholder in the layout |
| `$this->setLayoutDisabled()` | Skip the layout wrapper |

### Layouts

The framework looks for a layout template in this order:
1. `<module>/<controller>/layout.tpl`
2. `<module>/layout.tpl`
3. `app/layout.tpl`

### Routing

Routes are defined in a `RouterConfig` object and registered via DI:

```php
// di.mymodule.php
use Mmi\Mvc\RouterConfig;
use Mmi\Mvc\RouterConfigRoute;

return [
    RouterConfig::class => \DI\decorate(function (RouterConfig $config) {
        $config->addRoute(
            (new RouterConfigRoute())
                ->setPattern('blog/[id:\d+]')
                ->setDefault(['module' => 'blog', 'controller' => 'blog', 'action' => 'show'])
        );
        return $config;
    }),
];
```

Generating URLs:

```php
// In a controller
$url = $this->view->url(['module' => 'blog', 'controller' => 'blog', 'action' => 'show', 'id' => 42]);
// → /blog/42

// Directly via the router
$router = $container->get(\Mmi\Mvc\Router::class);
$url = $router->encodeUrl(['module' => 'blog', 'action' => 'show', 'id' => 42]);
```

### Messenger (flash messages)

```php
// In a controller
$this->getMessenger()->addMessage('Record saved.', 'success');
$this->getResponse()->redirectToUrl($this->view->url([...]));

// In a template
<?php foreach ($this->getMessenger()->getMessages() as $msg): ?>
    <div class="alert alert-<?= $msg->type ?>"><?= $msg->text ?></div>
<?php endforeach ?>
```

---

## ORM

The ORM is an **active-record** implementation. Each table maps to a *Record* class
and a *Query* class (generated via the `dao:render` CLI command).

### Generating DAO classes

```bash
php bin/console mmi:dao-render
```

This inspects the database schema and creates `*Record.php` and `*Query.php` pairs
in the appropriate module directories.

### Record

```php
// Create
$post = new PostRecord();
$post->title  = 'Hello World';
$post->body   = 'First post!';
$post->active = 1;
$post->save();          // INSERT — $post->id is set after save

// Read
$post = (new PostRecord())->find(42);    // by primary key

// Update
$post->title = 'Updated title';
$post->save();          // UPDATE

// Delete
$post->delete();
```

### Query — fluent interface

```php
// Simple equality
$records = (new PostQuery())
    ->whereActive()->equals(1)
    ->orderDesc('dateAdd')
    ->limit(10)
    ->offset(20)
    ->find();           // returns RecordCollection

// Multiple conditions
$records = (new PostQuery())
    ->whereActive()->equals(1)
    ->andField('categoryId')->equals(3)
    ->orField('featured')->equals(1)
    ->find();

// Find one record
$post = (new PostQuery())
    ->whereSlug()->equals('hello-world')
    ->findOne();        // returns PostRecord or null

// Count
$total = (new PostQuery())->whereActive()->equals(1)->count();

// Join
$records = (new PostQuery())
    ->join('category', 'post')         // INNER JOIN
    ->on('categoryId', 'id')
    ->find();

$records = (new PostQuery())
    ->joinLeft('category', 'post')     // LEFT JOIN
    ->on('categoryId', 'id')
    ->find();

// Sub-queries
$sub = (new PostQuery())->whereCategoryId()->equals(5);
$all = (new PostQuery())->whereQuery($sub)->orQuery(
    (new PostQuery())->whereFeatured()->equals(1)
)->find();

// Group by
$results = (new PostQuery())->groupBy('categoryId')->find();
```

### Read-only records

Extend `RecordRo` to create lightweight records for SELECT-only use cases.

### Special record types

| Class | Purpose |
|---|---|
| `CacheRecord` / `CacheQuery` | Persist arbitrary data into a DB-backed cache table |
| `ChangelogRecord` / `ChangelogQuery` | Audit log entries |
| `SessionRecord` / `SessionQuery` | DB-stored sessions |

---

## Database Layer (Db)

The Db layer wraps PDO with schema introspection and query helpers.

```php
$db = $container->get(\Mmi\Db\DbInterface::class);

// Raw queries
$rows = $db->fetchAll('SELECT * FROM post WHERE active = :active', [':active' => 1]);
$row  = $db->fetchRow('SELECT * FROM post WHERE id = :id', [':id' => 42]);
$db->query('DELETE FROM post WHERE id = :id', [':id' => 99]);

// Helper methods
$id = $db->insert('post', ['title' => 'Hello', 'active' => 1]);
$db->insertAll('post', [
    ['title' => 'Post A', 'active' => 1],
    ['title' => 'Post B', 'active' => 1],
]);
$db->update('post', ['title' => 'New title'], ['id' => 42]);
$db->delete('post', ['id' => 99]);

// Transactions
$db->beginTransaction();
try {
    $db->insert('post', [...]);
    $db->update('category', [...], [...]);
    $db->commit();
} catch (\Exception $e) {
    $db->rollBack();
}
```

### Schema introspection

```php
$info = $container->get(\Mmi\Db\DbInformationInterface::class);
$structure = $info->getTableStructure('post');
// Returns an array of column definitions (name, type, null, default, …)
```

### Incremental migrations

Place numbered `.sql` files in `src/<Module>/Resource/incremental/mysql/` and run:

```bash
php bin/console mmi:db-deploy
```

Files are executed in numeric order; already-applied files are skipped.

---

## Forms

Forms are defined by extending `\Mmi\Form\Form` and implementing `init()`.

```php
namespace MyModule\Form;

use Mmi\Form\Form;
use Mmi\Form\Element;

class PostForm extends Form
{
    public function init(): void
    {
        // Text input
        $this->addElement(
            (new Element\Text('title'))
                ->setLabel('Title')
                ->setRequired(true)
                ->addValidator(new \Mmi\Validator\StringLength(['min' => 3, 'max' => 255]))
        );

        // Textarea
        $this->addElement(
            (new Element\Textarea('body'))
                ->setLabel('Content')
                ->setRequired(true)
        );

        // Select / dropdown
        $this->addElement(
            (new Element\Select('categoryId'))
                ->setLabel('Category')
                ->setMultiOptions(['1' => 'Tech', '2' => 'News'])
        );

        // Checkbox
        $this->addElement(
            (new Element\Checkbox('active'))
                ->setLabel('Published')
        );

        // File upload
        $this->addElement(
            (new Element\File('image'))
                ->setLabel('Cover image')
        );

        // Hidden
        $this->addElement((new Element\Hidden('id')));

        // CSRF protection (recommended)
        $this->addElement(new Element\Csrf('csrf'));

        // Submit button
        $this->addElement(
            (new Element\Submit('submit'))
                ->setLabel('Save')
        );
    }
}
```

### Using the form in a controller

```php
public function editAction(): void
{
    $record = (new PostRecord())->find((int) $this->getRequest()->id);

    $form = new PostForm($record);   // hydrates from record
    $form->setFromPost($this->getRequest()->getPost()->toArray());

    if ($form->isSubmitted() && $form->isValid()) {
        $form->save();               // saves record in a DB transaction
        $this->getMessenger()->addMessage('Saved!', 'success');
        $this->getResponse()->redirectToUrl($this->view->url([...]));
        return;
    }

    $this->view->form = $form;
}
```

### Rendering a form

```php
<!-- template -->
<?= $this->form->render() ?>
```

Or render field by field for full control:

```php
<?= $this->form->getElement('title')->renderLabel() ?>
<?= $this->form->getElement('title')->renderField() ?>
<?= $this->form->getElement('title')->renderErrors() ?>
```

### Available element types

`Text`, `Textarea`, `Password`, `Email`, `Select`, `Radio`, `Checkbox`,
`MultiCheckbox`, `File`, `Hidden`, `Label`, `Button`, `Submit`, `Csrf`

---

## Validators

Validators are used standalone or attached to form elements.

```php
$validator = new \Mmi\Validator\NotEmpty();
if (!$validator->isValid('')) {
    echo $validator->getError(); // "Value is empty"
}
```

Attach to a form element:

```php
$element->addValidator(new \Mmi\Validator\StringLength(['min' => 2, 'max' => 100]))
        ->addValidator(new \Mmi\Validator\Alnum())
        ->addValidator(new \Mmi\Validator\NotEmpty());
```

### Built-in validators

| Validator | Description |
|---|---|
| `Alnum` | Alphanumeric characters only |
| `Checked` | Checkbox must be checked |
| `Csrf` | CSRF token validation |
| `Date` | Valid date string |
| `EmailAddress` | Single e-mail address |
| `EmailAddressList` | Comma-separated e-mail list |
| `Equal` | Value equals a reference value |
| `Iban` | IBAN bank account number |
| `Integer` | Integer value |
| `Ip4` / `Ip6` | IPv4 / IPv6 address |
| `Json` | Valid JSON string |
| `NotEmpty` | Non-empty value |
| `NumberBetween` | Numeric value within a range |
| `Numeric` | Any numeric value |
| `Phone` | Phone number format |
| `Postal` | Postal code |
| `RecordUnique` | Value is unique in a DB table |
| `Regex` | Matches a regular expression |
| `Sequence` | Value is in a sequence/list |
| `StringLength` | String length within min/max |
| `Url` | Valid URL |

---

## Filters

Filters transform a value. They are used standalone, in form elements, or in view
templates via `$this->getFilter('escape')`.

```php
$filter = new \Mmi\Filter\StringTrim();
echo $filter->filter('  hello  '); // "hello"

$filter = new \Mmi\Filter\Truncate();
$filter->setOptions(['length' => 100, 'ending' => '…']);
echo $filter->filter($longText);
```

### Built-in filters

| Filter | Description |
|---|---|
| `Alnum` | Remove non-alphanumeric characters |
| `Ascii` | Strip non-ASCII |
| `Capitalize` | Capitalise first letter of each word |
| `Ceil` | Ceiling of a numeric value |
| `Count` | Count array elements |
| `DateFormat` | Format a date string |
| `Dump` | `var_dump` wrapper for debugging |
| `EmptyStringToNull` / `EmptyToNull` | Convert empty values to `null` |
| `Escape` | HTML-escape (XSS protection) |
| `Floatval` / `Intval` | Cast to float / integer |
| `Input` | Sanitise for HTML input |
| `IsEmpty` | Returns bool |
| `Length` | String length |
| `Lowercase` / `Uppercase` | Case conversion |
| `MarkupProperty` | Sanitise HTML attribute values |
| `Nl2br` | Newlines to `<br>` |
| `NumberFormat` | Locale-aware number formatting |
| `Pad` | Pad a string |
| `Replace` | String replace |
| `Round` | Round a numeric value |
| `StringTrim` | Trim whitespace |
| `StripTags` | Remove HTML tags |
| `TinyMce` | Sanitise rich-text (TinyMCE) output |
| `Truncate` | Truncate to length |
| `Url` / `Urlencode` | URL normalisation / encoding |

---

## Cache

The framework exposes two logical caches wired via DI:

| Interface | Purpose |
|---|---|
| `SystemCacheInterface` | Internal framework use (schema info, compiled container) |
| `CacheInterface` | Application-level (public) cache |

Both share the same `Cache` implementation; the difference is configuration.

```php
// Inject via constructor
public function __construct(private \Mmi\Cache\CacheInterface $cache) {}

public function getPosts(): array
{
    $key = 'homepage.posts';
    if (null !== $data = $this->cache->load($key)) {
        return $data;
    }
    $data = (new PostQuery())->whereActive()->equals(1)->find()->toArray();
    $this->cache->save($data, $key, 3600); // TTL in seconds
    return $data;
}
```

### Cache API

```php
$cache->load(string $key): mixed        // null on miss or inactive
$cache->save($data, string $key, ?int $lifetime = null): bool
$cache->remove(string $key): bool
$cache->flush(): bool                   // clear all entries
$cache->isActive(): bool
```

### Backends

| `CACHE_PUBLIC_HANDLER` | Description |
|---|---|
| `file` | Stores serialised data in the filesystem (default) |
| `redis` | Stores in Redis (requires `CACHE_PUBLIC_PATH` = Redis DSN) |

---

## Session

```php
use Mmi\Session\Session;
use Mmi\Session\SessionSpace;

// Start session (done automatically by the framework)
$session = $container->get(Session::class);
$session->start();

// Namespaced session storage
$space = new SessionSpace('MyModule');
$space->userId  = 42;
$space->cart    = ['item1', 'item2'];

// Read
$userId = $space->userId;

// Destroy
$session->destroy();
$session->regenerateId();   // regenerate ID (call after privilege change)
```

### Configuration

| Variable | Default | Description |
|---|---|---|
| `SESSION_HANDLER` | `files` | Storage backend (`files` \| `redis`) |
| `SESSION_NAME` | `mmi` | Cookie name |
| `SESSION_PATH` | `/tmp` | Storage path |
| `SESSION_COOKIE_HTTP` | `true` | `HttpOnly` flag |
| `SESSION_COOKIE_SECURE` | `false` | `Secure` flag (enable on HTTPS) |

---

## Security — Auth & ACL

### Authentication

Implement `AuthProviderInterface` to plug in your own user source:

```php
class MyAuthProvider implements \Mmi\Security\AuthProviderInterface
{
    public function authenticate(string $identity, string $credential): ?\Mmi\Security\AuthRecord
    {
        $user = (new UserQuery())->whereEmail()->equals($identity)->findOne();
        if (!$user || !password_verify($credential, $user->password)) {
            return null;
        }
        return (new \Mmi\Security\AuthRecord())
            ->setId($user->id)
            ->setUsername($user->email)
            ->setName($user->name)
            ->setRoles($user->roles);
    }
}
```

Using `Auth`:

```php
$auth = $container->get(\Mmi\Security\AuthInterface::class);

// Login
if ($auth->authenticate($login, $password)) {
    // identity stored in session automatically
}

// Check
if ($auth->hasIdentity()) {
    echo $auth->getName();       // full name
    echo $auth->getUsername();   // login / email
    $roles = $auth->getRoles();  // e.g. ['admin', 'editor']
}

// Role check
if ($auth->hasRole('admin')) { ... }

// Logout
$auth->clearIdentity();

// HTTP Basic Auth (for API / protected areas)
$auth->httpAuth('Realm Name');
```

### Access Control List (ACL)

```php
$acl = $container->get(\Mmi\Security\AclInterface::class);

// Define resources (module:controller:action)
$acl->add('blog:post:edit');
$acl->add('blog:post:delete');

// Define roles
$acl->addRole('guest');
$acl->addRole('editor', 'guest');   // editor inherits guest
$acl->addRole('admin', 'editor');

// Grant / revoke
$acl->allow('editor', 'blog:post:edit');
$acl->allow('admin');               // admin allowed everywhere
$acl->deny('editor', 'blog:post:delete');

// Check
if ($acl->isAllowed('editor', 'blog:post:edit')) { ... }
if ($acl->isRoleAllowed($auth->getRoles(), 'blog:post:delete')) { ... }
```

---

## Translate / i18n

Translation files are `.ini` files with `key = value` pairs:

```ini
; src/MyModule/Resource/i18n/en.ini
blog.title      = My Blog
read.more       = Read more
greeting        = Hello, %s!
```

```php
$translate = $container->get(\Mmi\Translate\TranslateInterface::class);

$translate->addTranslationFile('path/to/en.ini', 'en');
$translate->setLocale('en');

echo $translate->translate('blog.title');          // "My Blog"
echo $translate->translate('greeting', ['Alice']);  // "Hello, Alice!"
```

In templates, use the shortcut:

```php
<?= $this->_('blog.title') ?>
<?= $this->_('greeting', ['Alice']) ?>
```

The framework ships English (`en.ini`) and Polish (`pl.ini`) base files for its own
components. Add your own locale files in each module's `Resource/i18n/` directory.

---

## Event Manager

A lightweight event dispatcher with priorities, wildcard patterns and propagation
control.

```php
$events = $container->get(\Mmi\EventManager\EventManager::class);

// Attach a listener (default priority 1)
$events->attach('post.save', function (\Mmi\EventManager\Event $event) {
    $post = $event->getParam('record');
    // index post in search engine, send notification, etc.
}, priority: 10);

// Wildcard listener
$events->attach('post.*', function ($event) {
    error_log('Post event: ' . $event->getName());
});

// Trigger
$responses = $events->trigger('post.save', $this, ['record' => $post]);

// Stop propagation inside a listener
$events->attach('post.delete', function ($event) {
    if (!$this->canDelete($event->getTarget())) {
        $event->stopPropagation();
    }
});

// Detach
$events->detach('post.save', $listenerCallable);
$events->clearListeners('post.save');
```

### Event object

```php
$event->getName()              // event name string
$event->getTarget()            // the object that triggered the event
$event->getParam(string $key)  // named parameter
$event->getParams()            // all parameters
$event->stopPropagation()      // prevent further listeners from running
```

---

## HTTP — Request & Response

### Request

```php
$request = $container->get(\Mmi\Http\Request::class);

// Route params (set by the router)
$request->getModuleName();
$request->getControllerName();
$request->getActionName();

// Input data
$request->getQuery()->get('page', 1);      // GET param with default
$request->getPost()->get('email');         // POST param
$request->getCookie()->get('token');
$request->getFiles()->get('upload');       // UploadedFile object
$request->getServer()->get('REMOTE_ADDR');

// Magic shorthand (reads from all sources)
$id = $request->id;
```

### Response

```php
$response = $container->get(\Mmi\Http\Response::class);

// Content types
$response->setTypeJson();
$response->setTypeXml();
$response->setTypeHtml();    // default
$response->setTypeCss();
$response->setTypeJs();

// Status codes
$response->setCode200Ok();
$response->setCode201Created();
$response->setCode400BadRequest();
$response->setCode401Unauthorized();
$response->setCode403Forbidden();
$response->setCode404NotFound();
$response->setCode500InternalServerError();

// Custom header
$response->setHeader('X-Custom', 'value');

// Body
$response->setContent(json_encode(['ok' => true]));

// Redirect
$response->redirectToUrl('/login');
$response->redirectToUrl($this->view->url([...]), 301);
```

---

## Image Processing

GD-based image manipulation. Accepts a file path, raw binary string, or existing
GD resource.

```php
use Mmi\Image\Image;

$image = new Image();

// Scale to fit within a bounding box (maintains aspect ratio)
$resource = $image->scaleMax('/path/to/photo.jpg', 800, 600);

// Scale to exact width (height auto)
$resource = $image->scalex('/path/to/photo.jpg', 400);

// Scale to exact height (width auto)
$resource = $image->scaley('/path/to/photo.jpg', 300);

// Scale to exact dimensions (may distort)
$resource = $image->scale('/path/to/photo.jpg', 200, 200);

// Crop to exact dimensions (centred)
$resource = $image->scaleCrop('/path/to/photo.jpg', 400, 300);

// Manual crop — x, y, width, height
$resource = $image->crop('/path/to/photo.jpg', 10, 10, 200, 150);

// Save result
imagejpeg($resource, '/path/to/output.jpg', 85);
imagedestroy($resource);
```

---

## Paginator

```php
use Mmi\Paginator\Paginator;

$total = (new PostQuery())->whereActive()->equals(1)->count();
$page  = (int) $this->getRequest()->getQuery()->get('p', 1);

$paginator = (new Paginator())
    ->setRowsCount($total)
    ->setPage($page)
    ->setRowsPerPage(15)
    ->setPagesInRange(10)
    ->setPageParamName('p');

// Query slice
$posts = (new PostQuery())
    ->whereActive()->equals(1)
    ->limit($paginator->getLimit())
    ->offset($paginator->getOffset())
    ->find();

$this->view->posts     = $posts;
$this->view->paginator = $paginator;
```

In the template:

```php
<?= $this->paginator ?>   <!-- auto-renders pagination links -->
```

---

## Navigation

Navigation is built from a tree configuration and automatically marks the active
branch based on the current request.

```php
// In di.mymodule.php – build the navigation tree
use Mmi\Navigation\NavigationConfig;

return [
    NavigationConfig::class => \DI\decorate(function (NavigationConfig $config) {
        $config->addItem('home',  ['label' => 'Home',  'module' => 'app',  'controller' => 'index', 'action' => 'index'])
               ->addItem('blog', ['label' => 'Blog',  'module' => 'blog', 'controller' => 'index', 'action' => 'index'])
               ->addItem('about',['label' => 'About', 'module' => 'app',  'controller' => 'about', 'action' => 'index']);
        return $config;
    }),
];
```

In a template:

```php
<!-- Render the menu tree -->
<?= $this->navigation() ?>

<!-- Render breadcrumbs -->
<?php foreach ($this->navigation()->getBreadcrumbs() as $crumb): ?>
    <a href="<?= $crumb->url ?>"><?= $crumb->label ?></a>
<?php endforeach ?>
```

---

## LDAP

```php
use Mmi\Ldap\LdapClient;
use Mmi\Ldap\LdapConfig;
use Mmi\Ldap\LdapServerAddress;

$config = (new LdapConfig())
    ->addServer((new LdapServerAddress())->setHost('ldap.example.com')->setPort(389))
    ->setBaseDn('dc=example,dc=com')
    ->setBindDn('cn=reader,dc=example,dc=com')
    ->setBindPassword('secret');

$client = new LdapClient($config);

// Authenticate user
if ($client->authenticate('jdoe', 'userpassword')) {
    // login ok
}

// Search directory
$users = $client->findUser(
    filter: '(department=Engineering)',
    limit: 50,
    searchFields: ['cn', 'mail', 'sn'],
    dn: 'ou=people,dc=example,dc=com'
);
```

LDAP supports server failover: if the first server fails a socket ping, the next one
in the list is tried automatically.

---

## Logging

MMi uses [Monolog](https://github.com/Seldaek/monolog) internally. Log behaviour is
controlled via `LogConfig` and `LogConfigInstance`.

```php
use Mmi\Log\LogConfig;
use Mmi\Log\LogConfigInstance;

$config = (new LogConfig())
    ->setName('myapp')
    ->addInstance(
        (new LogConfigInstance())
            ->setLevelInfo()
            ->setHandlerStream('/var/log/myapp.log')
    )
    ->addInstance(
        (new LogConfigInstance())
            ->setLevelError()
            ->setHandlerSyslog()
    );
```

Available handlers: `setHandlerSyslog()`, `setHandlerStream($path)`,
`setHandlerConsole()`, `setHandlerGelf($host, $port)`, `setHandlerSlack($token, $channel)`.

Available levels: `setLevelDebug()`, `setLevelInfo()`, `setLevelNotice()`,
`setLevelWarning()`, `setLevelError()`, `setLevelCritical()`.

In application code, inject a PSR-3 `\Psr\Log\LoggerInterface`:

```php
public function __construct(private \Psr\Log\LoggerInterface $logger) {}

public function doSomething(): void
{
    $this->logger->info('Processing started', ['userId' => 42]);
    try {
        // ...
    } catch (\Exception $e) {
        $this->logger->error('Processing failed', ['exception' => $e]);
    }
}
```

---

## CLI Commands

The CLI application is a Symfony Console wrapper. Commands are auto-discovered from
any class matching `*\\Command\\*Command` in the application structure.

### Built-in commands

```bash
# Flush system + public cache
php bin/console mmi:flush-cache

# Run pending SQL incremental files
php bin/console mmi:db-deploy

# Re-generate ORM DAO classes from DB schema
php bin/console mmi:dao-render
```

### Writing a custom command

```php
namespace MyModule\Command;

use Mmi\Command\CommandAbstract;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncProductsCommand extends CommandAbstract
{
    protected function configure(): void
    {
        parent::configure();                     // sets command name from namespace
        $this->setDescription('Sync products from external API');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Syncing…');
        // do work
        $output->writeln('<info>Done!</info>');
        return self::SUCCESS;
    }
}
```

The command name is derived from the class namespace:
`MyModule\Command\SyncProductsCommand` → `my-module:sync-products`.

---

## Core Utilities

### DataObject

A generic mutable data container with `Iterator` support and magic property access.

```php
$obj = new \Mmi\DataObject();
$obj->name  = 'Alice';
$obj->score = 100;

foreach ($obj as $key => $value) { ... }

$arr = $obj->toArray();
$obj->setParams(['name' => 'Bob', 'score' => 200]);
```

### OptionObject

A key/value bag with fluent magic getters and setters.

```php
$opts = new \Mmi\OptionObject();
$opts->setFoo('bar')
     ->setCount(42);

echo $opts->getFoo();    // "bar"
echo $opts->getCount();  // 42

$opts->unsetFoo();
echo $opts->issetFoo();  // false
```

### FileSystem

```php
use Mmi\FileSystem;

// Recursive copy
FileSystem::recurse('/src', '/dest');

// Recursive delete
FileSystem::rmdirRecursive('/path/to/dir');

// MIME type
$mime = FileSystem::mimeType('/path/to/file.pdf'); // "application/pdf"
```

---

## Quality Tooling

All tools are invokable via Composer scripts:

```bash
# Fix code style
composer fix:phpcbf
composer fix:php-cs-fixer
composer fix:all

# Static analysis & tests
composer test:phpcs          # PHP_CodeSniffer
composer test:phpstan        # PHPStan (level 1)
composer test:phpmd          # PHP Mess Detector
composer test:phpunit        # PHPUnit (with coverage)
composer test:all            # run everything
```

Coverage reports are written to `web/build/phpunit/` (HTML) and
`.phpunit.coverage.clover.xml` (Clover XML for CI).

---

## Upgrade guides

If you are migrating from an older version see the dedicated guides:

- [`UPGRADE-3.6.md`](UPGRADE-3.6.md)
- [`UPGRADE-3.7.md`](UPGRADE-3.7.md)
- [`UPGRADE-3.10.md`](UPGRADE-3.10.md)
- [`UPGRADE-4.0.md`](UPGRADE-4.0.md)
- [`UPGRADE-5.0.md`](UPGRADE-5.0.md)

---

*MMi Framework © 2010-2026 Mariusz Miłejko — New BSD License*
