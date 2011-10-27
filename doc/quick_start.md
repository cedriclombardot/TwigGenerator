# Quick Start

To start to generate PHP classes, you need to create a builder, and one or more templates.

## Creating a Builder class

First, create a class extending `TwigGenerator\Builder\BaseBuilder` - no need to add methods for now.

```php
<?php

namespace MyProject\Builder;

use TwigGenerator\Builder\BaseBuilder;

class MyBuilder extends BaseBuilder
{

}
```

**Tip**: Alternatively, a builder can implement the `TwigGenerator\Builder\BuilderInterface` if it has to extend a custom class.

## Creating Twig Templates

Next, create a couple twig templates under the `templates/` directory. Usually, you need at least one template for the main structure, plus one template per feature added to the class. 

Here is an example main template (or layout) for creating a custom PHP class (to be stored in `templates/_base/common.php.twig`):

```php
<?php
{{ namespace is defined ? "namespace " ~ namespace ~ ";" : "" }}

class {{ className }} {{ extends is defined ? "extends " ~ extends : "" }}
{
{% block functions %}
{% endblock %}
}
```

And now, an example for adding a custom method (to be stored in `templates/MyBuilder.php.twig`):

```
{% extends "_base/common.php.twig" %}

{% block functions %}
	public function tellMeHello()
	{
		echo "Hello world";
	}
{% endblock %}
```

### Generating the code

And now, use `TwigGenerator\Builder\Generator` to generate the result. For instance:

```php
<?php
// initialize the autoload
require_once '/path/to/TwigGenerator/autoload.php';
// alternatively, use your favorite PSR-0 autoloader configured with TwigGenerator, Symfony and Twig


// create a generator
$generator = new TwigGenerator\Builder\Generator();
$generator->setTemplateDirs(array(
	__DIR__.'/templates',
));

// allways regenerate classes even if they exist -> no cache
$generator->setMustOverwriteIfExists(true);

// set common variables
$generator->setVariables(array(
	'namespace' => 'MyProject\Generated',
));

// initialize a builder
$builder = new MyProject\Builder\MyBuilder();
$builder->setOutputName('MyBuilder.php');

// add the builder to the generator
$generator->addBuilder($builder);

// You can add other builders here

// add specific configuration for my builder
$builder->setVariable('className', 'MyBuilder');

// Run generation for al builders
$generator->writeOnDisk(__DIR__.'/Generated');
```

The file will be generated in `MyProject\Generated\MyBuilder.php`, as follows:

```php
<?php
namespace MyProject\Generated;

class MyBuilder 
{
	public function tellMeHello()
	{
		echo "Hello world";
	}
}
```
