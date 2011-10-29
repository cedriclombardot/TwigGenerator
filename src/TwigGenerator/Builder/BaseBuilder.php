<?php

/**
 * Inteface to define structure of the builders
 *
 * @author cedric Lombardot
 *
 */

namespace TwigGenerator\Builder;

abstract class BaseBuilder implements BuilderInterface
{
    const TWIG_EXTENSION = '.php.twig';

    /**
     * @var Generator the generator element
     */
    protected $generator;

    /**
     * @var array a list of templates directories
     */
    protected $templateDirectories = array();

    /**
     * @var string
     */
    protected $templateName;

    /**
     * @var string
     */
    protected $outputName;

    /**
     * @var boolean
     */
    protected $mustOverwriteIfExists = false;

    /**
     * @var array
     */
    protected $twigFilters = array(
        'addslashes',
        'var_export',
        'is_numeric',
        'ucfirst',
        'substr',
    );

    protected $variables = array();

     /**
     * @var array
     */
    protected $twigExtensions = array(
    );

    /**
     * (non-PHPdoc)
     * @see BuilderInterface::__construct()
     */
    public function __construct()
    {
        $this->templateDirectories = $this->getDefaultTemplateDirs();
        $this->templateName = $this->getDefaultTemplateName();
    }

    /**
     * (non-PHPdoc)
     * @see BuilderInterface::setGenerator()
     */
    public function setGenerator(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * (non-PHPdoc)
     * @see BuilderInterface::getGenerator()
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * (non-PHPdoc)
     * @see BuilderInterface::addTemplateDir()
     */
    public function addTemplateDir($templateDir)
    {
        $this->templateDirectories[$templateDir] = $templateDir;
    }

    /**
     * (non-PHPdoc)
     * @see BuilderInterface::setTemplateDirs()
     */
    public function setTemplateDirs(array $templateDirs)
    {
        $this->templateDirectories = $templateDirs;
    }

    /**
     * (non-PHPdoc)
     * @see BuilderInterface::getTemplateDirs()
     */
    public function getTemplateDirs()
    {
        return $this->templateDirectories;
    }

    /**
     * (non-PHPdoc)
     * @see BuilderInterface::getDefaultTemplateDirs()
     */
    public function getDefaultTemplateDirs()
    {
        return array();
    }

    /**
     * (non-PHPdoc)
     * @see BuilderInterface::setTemplateName()
     */
    public function setTemplateName($templateName)
    {
        $this->templateName = $templateName;
    }

    /**
     * (non-PHPdoc)
     * @see BuilderInterface::getTemplateName()
     */
    public function getTemplateName()
    {
        return $this->templateName;
    }

    /**
     * (non-PHPdoc)
     * @see BuilderInterface::getDefaultTemplateName()
     */
    public function getDefaultTemplateName()
    {
        return $this->getSimpleClassName(). self::TWIG_EXTENSION;
    }

    /**
     * (non-PHPdoc)
     * @see BuilderInterface::getSimpleClassName()
     */
    public function getSimpleClassName($class = null)
    {
        if (null === $class) {
            $class = get_class($this);
        }

        $classParts = explode('\\', $class);
        $simpleClassName = array_pop($classParts);

        return $simpleClassName;
    }

    /**
     * (non-PHPdoc)
     * @see BuilderInterface::setOutputName()
     */
    public function setOutputName($outputName)
    {
        $this->outputName = $outputName;
    }

    /**
     * (non-PHPdoc)
     * @see BuilderInterface::getOutputName()
     */
    public function getOutputName()
    {
        return $this->outputName;
    }

    /**
     * (non-PHPdoc)
     * @see BuilderInterface::mustOverwriteIfExists()
     */
    public function mustOverwriteIfExists()
    {
        return $this->mustOverwriteIfExists;
    }

    /**
     * (non-PHPdoc)
     * @see BuilderInterface::setMustOverwriteIfExists()
     */
    public function setMustOverwriteIfExists($status = true)
    {
        $this->mustOverwriteIfExists = $status;
    }

    /**
     * (non-PHPdoc)
     * @see BuilderInterface::setVariables()
     */
    public function setVariables($variables)
    {
        $this->variables = $variables;
    }

    /**
     * (non-PHPdoc)
     * @see BuilderInterface::setVariable()
     */
    public function setVariable($key, $value)
    {
        $this->variables[$key] = $value;
    }

    /**
     * (non-PHPdoc)
     * @see BuilderInterface::getVariables()
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * (non-PHPdoc)
     * @see BuilderInterface::hasVariable()
     */
    public function hasVariable($key)
    {
        return isset($this->variables[$key]);
    }

    /**
     * (non-PHPdoc)
     * @see BuilderInterface::getVariable()
     */
    public function getVariable($key, $default = null)
    {
        return $this->hasVariable($key) ? $this->variables[$key] : $default;
    }

    /**
     * (non-PHPdoc)
     * @see BuilderInterface::writeOnDisk()
     */
    public function writeOnDisk($outputDirectory)
    {
        $path = $outputDirectory . DIRECTORY_SEPARATOR . $this->getOutputName();
        $dir = dirname($path);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if (!file_exists($path) || (file_exists($path) && $this->mustOverwriteIfExists)) {
            file_put_contents($path, $this->getCode());
        }
    }

    /**
     * (non-PHPdoc)
     * @see BuilderInterface::getCode()
     */
    public function getCode()
    {
        $loader = new \Twig_Loader_Filesystem($this->getTemplateDirs());
        $twig = new \Twig_Environment($loader, array(
            'autoescape' => false,
            'strict_variables' => true,
            'debug' => true,
            'cache' => $this->getGenerator()->getTempDir(),
        ));

        $this->addTwigExtensions($twig, $loader);
        $this->addTwigFilters($twig);
        $template = $twig->loadTemplate($this->getTemplateName());

        $variables = $this->getVariables();
        $variables['builder'] = $this;

        return $template->render($variables);
    }

    /**
     * (non-PHPdoc)
     * @see BuilderInterface::addTwigFilters()
     */
    public function addTwigFilters(\Twig_Environment $twig)
    {
        foreach ($this->twigFilters as $twigFilter) {
            if (($pos = strpos($twigFilter, ':')) !== false) {
                $twigFilterName = substr($twigFilter, $pos + 2);
            } else {
                $twigFilterName = $twigFilter;
            }
            $twig->addFilter($twigFilterName, new \Twig_Filter_Function($twigFilter));
        }
    }

    /**
     * (non-PHPdoc)
     * @see BuilderInterface::addTwigExtensions()
     */
    public function addTwigExtensions(\Twig_Environment $twig, \Twig_LoaderInterface $loader)
    {
        foreach ($this->twigExtensions as $twigExtensionName) {
            $twigExtension = new $twigExtensionName($loader);
            $twig->addExtension($twigExtension);
        }
    }
}
