<?php

namespace l24n\Twigen\Plugin\ServerRender;

use SplFileObject;
use Symfony\Component\Yaml\Yaml;

class FrontMatter
{
    private int $frontMatterLines = 0;
    private ?array $matter = null;

    public function __construct(private $filepath)
    {
        
    }

    public function matter(): array
    {
        $body = '';
        $file = fopen($this->filepath, 'r');

        if ($file) {
            $line = fgets($file);
            $this->frontMatterLines = 0;

            if (trim($line) === '---') {
                while (($line = fgets($file)) !== false) {
                    $this->frontMatterLines++;

                    if (trim($line) === '---') {
                        break;
                    }
                    
                    $body .= trim($line);
                }
            }

            fclose($file);
        }

        return $this->matter = Yaml::parse($body);
    }

    public function body(): string
    {
        if ($this->matter === null) {
            $this->matter(); // Parse the front matter if it hasn't been parsed yet
        }

        $body = '';

        $file = new SplFileObject($this->filepath, 'r');
        $file->seek($this->frontMatterLines + 1); 
        
        while (!$file->eof()) {
            $body .= $file->fgets();
        }
        $file = null; // Close the file

        return $body;
    }

    public function getFilePath(): string
    {
        return $this->filepath;
    }

    public function setFilePath(string $filepath): void
    {
        $this->filepath = $filepath;
    }
}