# TwigGenerator ![project status](http://stillmaintained.com/cedriclombardot/TwigGenerator.png)# ![build status](https://secure.travis-ci.org/cedriclombardot/TwigGenerator.png)#

TwigGenerator is a PHP code generator based on the [Twig](https://github.com/fabpot/Twig) template engine. It leverages the power of Twig templates to simplify the generation of PHP code, to make it more extensible, and more readable.

## Installation

Checkout this GitHub repository and the two submodules (Twig and Symfony ClassLoader):

```
git clone https://github.com/cedriclombardot/TwigGenerator.git
cd TwigGenerator
wget -nc http://getcomposer.org/composer.phar
php composer.phar install
```

## Usage

To generate PHP classes, you need to create a "Builder", and one or more Twig templates. Then, add the new Builder to a "Generator", and generate the result.

### Creating a Builder class

First, create a class extending `TwigGenerator\Builder\BaseBuilder` - no need for methods at start.

```php
<?php

namespace MyProject\Builder;

use TwigGenerator\Builder\BaseBuilder;

class MyBuilder extends BaseBuilder
{
}
```

**Tip**: Alternatively, a builder can implement the `TwigGenerator\Builder\BuilderInterface` if it has to extend a custom class.

### Creating Twig Templates

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

Use a `TwigGenerator\Builder\Generator` instance to generate the result. For instance:

```php
<?php
// initialize the autoload
require_once '/path/to/TwigGenerator/src/autoload.php';
// alternatively, use your favorite PSR-0 autoloader configured with TwigGenerator, Symfony and Twig

// initialize a builder
$builder = new MyProject\Builder\MyBuilder();
$builder->setOutputName('MyBuilder.php');

// add specific configuration for my builder
$builder->setVariable('className', 'MyBuilder');

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

// add the builder to the generator
$generator->addBuilder($builder);

// You can add other builders here

// Run generation for all builders
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

## Other Examples

You can see some basic code generation samples in the tests, and on some GitHub repositories like [fzaninotto/Doctrine2ActiveRecord](https://github.com/fzaninotto/Doctrine2ActiveRecord), or [cedriclombardot/AdmingeneratorGeneratorBundle](https://github.com/cedriclombardot/AdmingeneratorGeneratorBundle).


## Unit Tests

Then, just run:

    phpunit
