<?php

namespace Tests\Feature;

use Tests\TestCase;

class SeoMetaTest extends TestCase
{
    public function test_default_og_image_file_exists_and_is_1200x630(): void
    {
        $path = public_path('images/og-default.jpg');
        $this->assertFileExists($path);

        [$width, $height, $type] = getimagesize($path);
        $this->assertSame([1200, 630, IMAGETYPE_JPEG], [$width, $height, $type]);
    }

    public function test_static_page_uses_default_og_image(): void
    {
        $this->get('/hakkimizda')
            ->assertOk()
            ->assertSee('property="og:image" content="' . asset('images/og-default.jpg') . '"', false)
            ->assertSee('name="twitter:image" content="' . asset('images/og-default.jpg') . '"', false)
            ->assertSee('property="og:image:width" content="1200"', false)
            ->assertSee('property="og:image:height" content="630"', false);
    }
}
