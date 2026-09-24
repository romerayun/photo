<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test root redirection.
     */
    public function test_root_redirects_to_localized_home(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/ru');
    }
}
