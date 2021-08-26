<?php

/*
 * This file is part of Flarum.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Flarum\Tests\integration\extenders;

use Flarum\Extend;
use Flarum\Foundation\Config;
use Flarum\Testing\integration\TestCase;

class FrontendPreloadTest extends TestCase
{
    private $customPreloadUrls = ['/my-preload', '/my-preload2'];

    /**
     * @test
     */
    public function default_preloads_are_present()
    {
        $response = $this->send(
            $this->request('GET', '/')
        );

        $assetsPath = $this->app()->getContainer()->make(Config::class)->url()->getPath().'/assets';

        $urls = [
            $assetsPath.'/fonts/fa-solid-900.woff2',
            $assetsPath.'/fonts/fa-regular-400.woff2',
        ];

        $body = $response->getBody()->getContents();

        foreach ($urls as $url) {
            $this->assertStringContainsString("<link rel=\"preload\" href=\"$url\" as=\"font\" type=\"font/woff2\" crossorigin=\"\">", $body);
        }
    }

    /**
     * @test
     */
    public function preloads_can_be_added()
    {
        $urls = $this->customPreloadUrls;

        $this->extend(
            (new Extend\Frontend('forum'))
                ->preloads(
                    array_map(function ($url) {
                        return ['href' => $url];
                    }, $urls)
                )
        );

        $response = $this->send(
            $this->request('GET', '/')
        );
        $body = $response->getBody()->getContents();

        foreach ($urls as $url) {
            $this->assertStringContainsString("<link rel=\"preload\" href=\"$url\">", $body);
        }
    }

    /**
     * @test
     */
    public function preloads_can_be_added_via_callable()
    {
        $urls = $this->customPreloadUrls;

        $this->extend(
            (new Extend\Frontend('forum'))
                ->preloads(function () use ($urls) {
                    return array_map(function ($url) {
                        return ['href' => $url];
                    }, $urls);
                })
        );

        $response = $this->send(
            $this->request('GET', '/')
        );
        $body = $response->getBody()->getContents();

        foreach ($urls as $url) {
            $this->assertStringContainsString("<link rel=\"preload\" href=\"$url\">", $body);
        }
    }
}