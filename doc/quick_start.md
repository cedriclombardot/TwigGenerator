# Quick Start

To start to generate your classes, you need to create a builder and 1 or more templates.

## The builder

It extends of `TwigGenerator\Builder\BaseBuilder` or implement the `TwigGenerator\Builder\BuilderInterface`

````
<?php

namespace MyProject\Builder;

use TwigGenerator\Builder\BaseBuilder;

class MyBuilder extends BaseBuilder
{

}
````

## A template

You have to create twig templates. For eg :

templates/MyBuilder.php.twig

````
{% extends "_base/common.php.twig" %}

{% block functions %}
public function tellMeHello()
{
	echo "Hello world";
}
{% endblock %}
````

And allways for sample :

templates/_base/common.php.twig

````
{{ namespace is defined ? "namespace " ~ namespace : "" }}

class {{ className }} {{ extends is defined ? "extends " ~ extends : "" }}
{
	{% block functions %}
	{% endblock %}
}
````

### And now generate

````
<?php

// Create the generator
$generator = new TwigGenerator\Builder\Generator();
$generator->setTemplateDirs(array(
	__DIR__.'/tempates',
));

// Allways regenerate -> no cache
$generator->setMustOverwriteIfExists(true);

// Set common variables
$generator->setVariables(array(
	'namespace' => 'MyProject\Generated',
));

// Init the builder
$builder = new MyProject\Builder\MyBuilder();
$builder->setOutputName('MyBuilder.php');

// Add the builder to the generator to give the generator configuration 
$generator->addBuilder($builder);

// You can add other builders here

// Add specific config for my builder
$builder->getVariables()->set('className', 'MyBuilder');

// Run generation for al builders
$generator->writeOnDisk(__DIR__.'/Generated');

````

The file will be generated in `MyProject\Generated\MyBuilder.php`
