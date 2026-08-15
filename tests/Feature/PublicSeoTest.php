<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_returns_xml_with_public_urls(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee('<urlset', false)
            ->assertSee(route('home'))
            ->assertSee(route('patient.register'))
            ->assertSee(route('doctor.login'))
            ->assertSee(route('center.register'))
            ->assertSee(route('legal.privacy'))
            ->assertSee(route('legal.mentions'));
    }

    public function test_robots_txt_disallows_private_areas_and_links_sitemap(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertOk()
            ->assertHeaderContains('Content-Type', 'text/plain')
            ->assertSee('Disallow: /admin')
            ->assertSee('Disallow: /profile')
            ->assertSee('Sitemap: '.url('/sitemap.xml'));
    }

    public function test_unknown_route_returns_custom_404_page(): void
    {
        $response = $this->get('/this-route-does-not-exist');

        $response->assertNotFound()
            ->assertSee('404')
            ->assertSee(__('errors.404_title'))
            ->assertSee(__('errors.back_home'))
            ->assertSee('noindex');
    }

    public function test_custom_403_page_renders(): void
    {
        $html = view('errors.403')->render();

        $this->assertStringContainsString('403', $html);
        $this->assertStringContainsString(__('errors.403_title'), $html);
        $this->assertStringContainsString('noindex', $html);
    }

    public function test_landing_page_renders_with_meta_description(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('<meta name="description"', false)
            ->assertSee(__('landing.hero_title_1'))
            ->assertSee(__('landing.cta_band_button'))
            ->assertSee(route('legal.privacy'));
    }

    public function test_portal_has_meta_description_and_primary_cta(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee('<meta name="description"', false)
            ->assertSee(__('seo.portal_description'))
            ->assertSee(route('patient.register'))
            ->assertSee(route('doctor.register'))
            ->assertSee(route('center.register'));
    }

    public function test_public_pages_expose_a_meta_description(): void
    {
        $pages = [
            route('home') => __('seo.portal_description'),
            route('patient.login') => __('seo.patient_login_description'),
            route('doctor.login') => __('seo.doctor_login_description'),
            route('center.login') => __('seo.center_login_description'),
            route('patient.register') => __('seo.patient_register_description'),
            route('doctor.register') => __('seo.doctor_register_description'),
            route('center.register') => __('seo.center_register_description'),
            route('legal.privacy') => __('seo.legal_description'),
            route('legal.mentions') => __('seo.legal_description'),
        ];

        foreach ($pages as $url => $description) {
            $this->get($url)->assertOk()
                ->assertSee('<meta name="description"', false)
                ->assertSee($description);
        }
    }
}
