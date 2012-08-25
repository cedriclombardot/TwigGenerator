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
 * @author Cédric Lombardot
 */
class Generator
{
    /**
     */
    const TEMP_DIR_PREFIX = 'TwigGenerator';

    /**
     * @var string  The temporary dir.
     */
    protected $tempDir;

    /**
     * @var array   List of builders.
     */
    protected $builders = array();

    /**
     * @var Boolean
     */
    protected $mustOverwriteIfExists = false;

    /**
     * @var array
     */
    protected $templateDirectories = array();

    /**
     * @var array   Variables to pass to the builder.
     */
    protected $variables = array();

    /**
     * @var boolean Activate remove temp dir after generation
     */
    protected $autoRemoveTempDir = true;

    /**
     * Init a new generator and automatically define the base of temp directory.
     * 
     * @param string $baseTempDir    Existing base directory for temporary template files
     */
    public function __construct($baseTempDir = null)
    {
        if (null === $baseTempDir) {
            $baseTempDir = sys_get_temp_dir();
        }

        $this->tempDir = realpath($baseTempDir).DIRECTORY_SEPARATOR.self::TEMP_DIR_PREFIX;

        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0777, true);
        }
    }

    public function setAutoRemoveTempDir($autoRemoveTempDir = true)
    {
        $this->autoRemoveTempDir = $autoRemoveTempDir;
    }

    public function setMustOverwriteIfExists($status = true)
    {
        $this->mustOverwriteIfExists = $status;
    }

    public function setTemplateDirs(array $templateDirs)
    {
        $this->templateDirectories = $templateDirs;
    }

    /**
     * Ensure to remove the temp directory.
     */
    public function __destruct()
    {
        if ($this->tempDir && is_dir($this->tempDir) && $this->autoRemoveTempDir) {
            $this->removeDir($this->tempDir);
        }
    }

    /**
     * @param string The temporary directory path
     */
    public function setTempDir($tempDir)
    {
        $this->tempDir = $tempDir;
    }

    /**
     * @return string   The temporary directory.
     */
    public function getTempDir()
    {
        return $this->tempDir;
    }

    /**
     * @return array    The list of builders.
     */
    public function getBuilders()
    {
        return $this->builders;
    }

    /**
     * Add a builder.
     *
     * @param \TwigGenerator\Builder\BuilderInterface $builder  A builder.
     *
     * @return \TwigGenerator\Builder\BuilderInterface  The builder
     */
    public function addBuilder(BuilderInterface $builder)
    {
        $builder->setGenerator($this);
        $builder->setTemplateDirs($this->templateDirectories);
        $builder->setMustOverwriteIfExists($this->mustOverwriteIfExists);
        $builder->setVariables(array_merge($this->variables, $builder->getVariables()));   

        $this->builders[$builder->getSimpleClassName()] = $builder;

        return $builder;
    }

    /**
     * Add an array of variables to pass to builders.
     *
     * @param array $variables  A set of variables.
     */
    public function setVariables(array $variables = array())
    {
        $this->variables = $variables;
    }

    /**
     * Generate and write classes to disk.
     *
     * @param string $outputDirectory   An output directory.
     */
    public function writeOnDisk($outputDirectory)
    {
        foreach ($this->getBuilders() as $builder) {
            $builder->writeOnDisk($outputDirectory);
        }
    }

    /**
     * Remove a directory.
     *
     * @param string $target    A directory.
     */
    private function removeDir($target)
    {
        $fp = opendir($target);
        while (false !== $file = readdir($fp)) {
            if (in_array($file, array('.', '..'))) {
                continue;
            }

            if (is_dir($target.'/'.$file)) {
                self::removeDir($target.'/'.$file);
            } else {
                unlink($target.'/'.$file);
            }
        }
        closedir($fp);
        rmdir($target);
    }
}
