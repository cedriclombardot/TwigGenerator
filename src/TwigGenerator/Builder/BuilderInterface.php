<?php

/**
 * This file is part of the TwigGenerator package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace TwigGenerator\Builder;

/**
 * This interface defines the structure of builders.
 *
 * @author Cédric Lombardot
 */
interface BuilderInterface
{
    /**
     * Set the generator.
     *
     * @param \TwigGenerator\Builder\Generator $generator A generator.
     */
    public function setGenerator(Generator $generator);

    /**
     * Return the generator.
     *
     * @return \TwigGenerator\Builder\Generator The generator.
     */
    public function getGenerator();

    /**
     * Add a template directory.
     *
     * @param string $templateDir A template directory.
     */
    public function addTemplateDir($templateDir);

    /**
     * Set a list of template directories.
     *
     * @param array $templateDirs
     */
    public function setTemplateDirs(array $templateDirs);

    /**
     * Return an array of template directories.
     *
     * @return array An array of template directories.
     */
    public function getTemplateDirs();

    /**
     * Return all default template directories.
     *
     * @return array An array of default directories.
     */
    public function getDefaultTemplateDirs();

    /**
     * Set the template name.
     *
     * @param string $templateName A template name.
     */
    public function setTemplateName($templateName);

    /**
     * Return the template name.
     *
     * @return string The template name.
     */
    public function getTemplateName();

    /**
     * Return the default template name.
     *
     * @return string The default template name.
     */
    public function getDefaultTemplateName();

    /**
     * Return the simple classname.
     *
     * @param string    A classname.
     *
     * @return string The short classname.
     */
    public function getSimpleClassName($class = null);

    /**
     * Set the output filename.
     *
     * @param string $outputName The output filename.
     */
    public function setOutputName($outputName);

    /**
     * Return the output name.
     *
     * @return string The output name.
     */
    public function getOutputName();

    /**
     * Return whether the builder must overwrite the file or not.
     *
     * @return Boolean true if the builder must overwrite the file, false otherwise.
     */
    public function mustOverwriteIfExists();

    /**
     * Change the overwrite status.
     *
     * @param Boolean $status The status.
     */
    public function setMustOverwriteIfExists($status = true);

    /**
     * Set an array of variables.
     *
     * @param array $variables An array of variables.
     */
    public function setVariables(array $variables);

    /**
     * Return an array of variables.
     *
     * @return array An array of variables.
     */
    public function getVariables();

    /**
     * Return whether the builder contains a variable or not.
     *
     * @return Boolean true if the builder contains the variable, false otherwise.
     */
    public function hasVariable($key);

    /**
     * Get a variable identified by its key.
     *
     * @param string $path    The key.
     * @param mixed  $default The default value.
     *
     * @return mixed The variable.
     */
    public function getVariable($path, $default = null);

    /**
     * Set a new key/value.
     *
     * @param string $key   The key.
     * @param mixed  $value The value.
     */
    public function setVariable($key, $value);

    /**
     * Write files to disk.
     *
     * @param string $outputDirectory The output directory.
     */
    public function writeOnDisk($outputDirectory);

    /**
     * Return the parsed code to insert into the file.
     *
     * @return string The parsed code to insert into the file.
     */
    public function getCode();

    /**
     * Add Twig filters from a given Twig environment.
     *
     * @param \Twig_Environment $twig A Twig environment.
     */
    public function addTwigFilters(\Twig_Environment $twig);

    /**
     * Add Twig extensions.
     *
     * @param \Twig_Environment     $twig   A Twig environment.
     * @param \Twig_LoaderInterface $loader A Twig loader.
     */
    public function addTwigExtensions(\Twig_Environment $twig, \Twig_LoaderInterface $loader);
}
