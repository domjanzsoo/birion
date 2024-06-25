<?php

namespace Tests\Livewire;

use Illuminate\Http\Testing\File;

class LivewireFileUpload extends File
{
    /**
     * Create a new file instance.
     *
     * @param  string  $name
     * @param  resource  $tempFile
     * @param int $size
     * @param string $mimeType
     * @return void
     */
    public function __construct($name, $tempFile, $size, $mimeType)
    {
        parent::__construct($name, $tempFile);

        $this->sizeToReport = $size;
        $this->mimeTypeToReport = $mimeType;
    }

    /**
     * get temp url
     *
     * @return string
     */
    public function temporaryUrl()
    {
        return 'http://some-signed-url.test';
    }

}