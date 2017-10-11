FORM ELEMENTS:

find:
->addElement([a-zA-Z]+)(\(\'[a-zA-Z0-9]+\'\))([\s]+?[^;]+)?;

replace:
->addElement((new Element\\$1$2)$3);

find:
namespace ([A-Z][a-zA-Z0-9]+Admin)\\Form([a-zA-Z0-9\\]+)?;

replace:
namespace $1\\Form$2;\n\nuse Cms\\Form\\Element;

search for:
\Form\Form
(check if "use Mmi\Form\Element;" exists)

VALIDATORS:

find:
->addValidator([a-zA-Z]+)\((.*?)\)

replace:
->addValidator(new \\Mmi\\Validator\\$1(\[$2\]))

FILTERS:

find:
->addFilter([a-zA-Z]+)\((.*?)\)

replace:
->addFilter(new \\Mmi\\Filter\\$1(\[$2\]))

GRIDS:

find:
->addColumn([a-zA-Z]+)\(\);

replace:
->addColumn(new Column\\$1Column);

find:
->addColumn([a-zA-Z]+)(\(\'[a-zA-Z0-9]+\'\))([\s]+?[^;]+)?;

replace:
->addColumn((new Column\\$1Column$2)$3);

find:
->addColumn([a-zA-Z]+)\(\)([\s]+?[^;]+)?;

replace:
->addColumn((new Column\\$1Column)$2);

find:
namespace ([A-Z][a-zA-Z0-9]+Admin)\\Plugin([a-zA-Z0-9\\]+)?;

replace:
namespace $1\\Plugin$2;\n\nuse CmsAdmin\\Grid\\Column;

search for:
addColumn[A-Z]
(check if use Cms\Admin\Grid\Column exists)
