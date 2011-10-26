<?php

namespace TwigGenerator\Builder;

class Generator
{
    const TEMP_DIR_PREFIX = 'TwigGenerator';

    /**
     * @var string the temporary dir
     */
    protected $tempDir;

    /**
     * @var array List of builders
     */
    protected $builders = array();

    protected $mustOverwriteIfExists = false;

    protected $templateDirectories = array();

    /**
     * @var array variables to give to the builder when you add one
     */
    protected $variables = array();

    /**
     * Init a new generator and automatically define the base of tempDir
     */
    public function __construct()
    {
        $this->tempDir = realpath(sys_get_temp_dir()).DIRECTORY_SEPARATOR.self::TEMP_DIR_PREFIX;
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0777, true);
        }
    }

    public function setMustOverwriteIfExists($status = true)
    {
        $this->mustOverwriteIfExists = $status;
    }

    /**
     * (non-PHPdoc)
     */
    public function setTemplateDirs(array $templateDirs)
    {
        $this->templateDirectories = $templateDirs;
    }

    /**
     * Ensure to remove tempDir
     */
    public function __destruct()
    {
        if ($this->tempDir && is_dir($this->tempDir)) {
            $this->removeDir($this->tempDir);
        }
    }

    /**
     * @return string the $tempDir
     */
    public function getTempDir()
    {
        return $this->tempDir;
    }

    /**
     * @return array the list of builders
     */
    public function getBuilders()
    {
        return $this->builders;
    }

    /**
     * Add a builder
     * @param BuilderInterface $builder
     *
     * @return BuilderInterface $builder The builder
     */
    public function addBuilder(BuilderInterface $builder)
    {
        $builder->setGenerator($this);
        $builder->setTemplateDirs($this->templateDirectories);
        $builder->setMustOverwriteIfExists($this->mustOverwriteIfExists);
        $builder->setVariables($this->variables);

        $this->builders[$builder->getSimpleClassName()] = $builder;

        return $builder;
    }

    /**
     * Give an array of variables to give to the builders
     * @param array $variables
     */
    public function setVariables(array $variables = array())
    {
        $this->variables = $variables;
    }

    /**
     * Generated and write classes to disk
     *
     * @param string $outputDirectory
     * @param array  $variables
     */
    public function writeOnDisk($outputDirectory)
    {
        foreach ($this->builders as $builder) {
            $builder->writeOnDisk($outputDirectory);
        }
    }

    /**
     * Remove a directory
     * @param string $target
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
