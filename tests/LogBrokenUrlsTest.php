<?php

namespace Spatie\LinkChecker\Test;

use Illuminate\Contracts\Console\Kernel;

class LogBrokenUrlsTest extends IntegrationTest
{
    /**
     * @test
     */
    public function it_can_report_broken_urls_in_the_log()
    {
        $this->placeMarker();

        $this->app[Kernel::class]->call('link-checker:run', ['--url' => $this->appUrl]);

        $this->assertLogContainsTextAfterLastMarker('GET http://localhost:4020/400` resulted in a `400 Bad Request`');
        $this->assertLogContainsTextAfterLastMarker('GET http://localhost:4020/500` resulted in a `500 Internal Server Error`');
        $this->assertLogContainsTextAfterLastMarker('link checker summary');
        $this->assertLogContainsTextAfterLastMarker('Crawled 1 url(s) with statuscode 400');
        $this->assertLogContainsTextAfterLastMarker('Crawled 1 url(s) with statuscode 500');
    }

    /**
     * @test
     */
    public function it_does_not_report_excluded_status_codes()
    {
        $this->placeMarker();

        $this->app['config']->set('laravel-link-checker.reporters.exclude_status_codes', [500]);
        $this->app[Kernel::class]->call('link-checker:run', ['--url' => $this->appUrl]);

        $this->assertLogContainsTextAfterLastMarker('GET http://localhost:4020/400` resulted in a `400 Bad Request`');
        $this->assertLogNotContainsTextAfterLastMarker('GET http://localhost:4020/500` resulted in a `500 Internal Server Error`');
        $this->assertLogContainsTextAfterLastMarker('link checker summary');
        $this->assertLogContainsTextAfterLastMarker('Crawled 1 url(s) with statuscode 400');
        $this->assertLogNotContainsTextAfterLastMarker('Crawled 1 url(s) with statuscode 500');
    }
}
