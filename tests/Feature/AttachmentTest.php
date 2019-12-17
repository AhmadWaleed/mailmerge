<?php

namespace Mailmerge\Tests\Feature;

use Illuminate\Support\Str;
use MailMerge\Attachment;
use PHPUnit\Framework\TestCase;

class AttachmentTest extends TestCase
{
    /** @test */
    public function it_saves_attachment_from_url()
    {
        $attachment = new Attachment();
        $attachment->fromUrl('http://www.africau.edu/images/default/sample.pdf');

        try {
            $filePath = $attachment->save();
            $this->assertTrue(file_exists($filePath));
            $this->assertSame('pdf', Str::afterLast($filePath, '.'));
        } finally {
            $attachment->getDirectory()->delete();
            $this->assertFalse(file_exists($filePath));
        }
    }
}