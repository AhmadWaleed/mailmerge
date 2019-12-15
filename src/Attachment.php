<?php

namespace MailMerge;

use Illuminate\Support\Str;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class Attachment
{
    private TemporaryDirectory $temporaryDirectory;

    private string $url;

    private bool $usePrefixAsName = false;

    private string $prefix = '';

    public function __construct()
    {
        $this->temporaryDirectory = (new TemporaryDirectory())->create();
    }

    public function fromUrl(string $url): self
    {
        list($status) = get_headers($url);

        if (strpos($status, '200') === false) {
            throw new \InvalidArgumentException("Inaccessible attachment url!");
        }

        $this->url = $url;

        return $this;
    }

    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function usePrefixAsName(): self
    {
        $this->usePrefixAsName = true;

        return $this;
    }

    public function save(): string
    {
        $filename = $this->usePrefixAsName ? $this->prefix : $this->prefix . Str::random();

        $path = sprintf("%s%s%s.%s", $this->temporaryDirectory->path('attachments'), DIRECTORY_SEPARATOR, $filename, Str::afterLast(basename($this->url), '.'));

        if (! copy($this->url, $path)) {
            throw new \RuntimeException("Failed to resolve attachment!");
        }

        return $path;
    }

    public function getDirectory(): TemporaryDirectory
    {
        return $this->temporaryDirectory;
    }
}