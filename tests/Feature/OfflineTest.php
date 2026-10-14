<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class OfflineTest extends TestCase
{
    use RefreshDatabase;

    public function test_offline_fallback_page_is_public_and_free_of_database_access(): void
    {
        $this->get(route('offline'))
            ->assertOk()
            ->assertSee(__('offline.title'));
    }

    public function test_service_worker_and_app_js_exist(): void
    {
        $this->assertFileExists(public_path('sw.js'));
        $this->assertFileExists(resource_path('js/app.js'));
        $this->assertStringContainsString(
            'serviceWorker',
            File::get(resource_path('js/app.js')),
        );
    }
}
