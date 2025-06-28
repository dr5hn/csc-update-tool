<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up Vite for testing environment
        if (app()->environment('testing')) {
            // Mock Vite manifest if it doesn't exist
            if (!file_exists(public_path('build/manifest.json'))) {
                $this->mockViteManifest();
            }
        }

        // Create geographical tables for testing
        $this->createGeographicalTablesForTesting();

        // Seed minimal geographical data for tests
        $this->seedMinimalGeographicalData();
    }

    /**
     * Create minimal geographical tables for testing
     */
    protected function createGeographicalTablesForTesting(): void
    {
        if (!Schema::hasTable('regions')) {
            Schema::create('regions', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->json('translations')->nullable();
                $table->string('wikiDataId')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('subregions')) {
            Schema::create('subregions', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->json('translations')->nullable();
                $table->unsignedBigInteger('region_id');
                $table->string('wikiDataId')->nullable();
                $table->timestamps();

                $table->foreign('region_id')->references('id')->on('regions');
            });
        }

        if (!Schema::hasTable('countries')) {
            Schema::create('countries', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('iso3', 3)->nullable();
                $table->string('numeric_code', 3)->nullable();
                $table->string('iso2', 2)->nullable();
                $table->string('phonecode')->nullable();
                $table->string('capital')->nullable();
                $table->string('currency', 3)->nullable();
                $table->string('currency_name')->nullable();
                $table->string('currency_symbol')->nullable();
                $table->string('tld')->nullable();
                $table->string('native')->nullable();
                $table->string('region')->nullable();
                $table->unsignedBigInteger('region_id')->nullable();
                $table->string('subregion')->nullable();
                $table->unsignedBigInteger('subregion_id')->nullable();
                $table->string('nationality')->nullable();
                $table->json('timezones')->nullable();
                $table->json('translations')->nullable();
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->string('emoji')->nullable();
                $table->string('emojiU')->nullable();
                $table->string('wikiDataId')->nullable();
                $table->timestamps();

                $table->foreign('subregion_id')->references('id')->on('subregions');
            });
        }

        if (!Schema::hasTable('states')) {
            Schema::create('states', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->unsignedBigInteger('country_id');
                $table->string('country_code', 2);
                $table->string('fips_code')->nullable();
                $table->string('iso2')->nullable();
                $table->string('type')->nullable();
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->string('wikiDataId')->nullable();
                $table->timestamps();

                $table->foreign('country_id')->references('id')->on('countries');
            });
        }

        if (!Schema::hasTable('cities')) {
            Schema::create('cities', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->unsignedBigInteger('state_id');
                $table->string('state_code');
                $table->unsignedBigInteger('country_id');
                $table->string('country_code', 2);
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->string('wikiDataId')->nullable();
                $table->timestamps();

                $table->foreign('state_id')->references('id')->on('states');
                $table->foreign('country_id')->references('id')->on('countries');
            });
        }
    }

    /**
     * Seed minimal geographical data for testing
     */
    protected function seedMinimalGeographicalData(): void
    {
        // Only seed if tables are empty
        if (\DB::table('regions')->count() === 0) {
            // Create test region
            $regionId = \DB::table('regions')->insertGetId([
                'name' => 'Test Region',
                'translations' => json_encode(['en' => 'Test Region']),
                'wikiDataId' => 'Q1',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create test subregion
            $subregionId = \DB::table('subregions')->insertGetId([
                'name' => 'Test Subregion',
                'translations' => json_encode(['en' => 'Test Subregion']),
                'region_id' => $regionId,
                'wikiDataId' => 'Q2',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create test country
            $countryId = \DB::table('countries')->insertGetId([
                'name' => 'Test Country',
                'iso3' => 'TST',
                'numeric_code' => '999',
                'iso2' => 'TS',
                'phonecode' => '999',
                'capital' => 'Test Capital',
                'currency' => 'TST',
                'currency_name' => 'Test Currency',
                'currency_symbol' => '$',
                'tld' => '.test',
                'native' => 'Test Country',
                'region' => 'Test Region',
                'region_id' => $regionId,
                'subregion' => 'Test Subregion',
                'subregion_id' => $subregionId,
                'nationality' => 'Test',
                'timezones' => json_encode(['UTC']),
                'translations' => json_encode(['en' => 'Test Country']),
                'latitude' => 0.0,
                'longitude' => 0.0,
                'emoji' => '🏳️',
                'emojiU' => 'U+1F3F3',
                'wikiDataId' => 'Q3',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create test state
            $stateId = \DB::table('states')->insertGetId([
                'name' => 'Test State',
                'country_id' => $countryId,
                'country_code' => 'TS',
                'fips_code' => 'TS01',
                'iso2' => 'TS-01',
                'type' => 'state',
                'latitude' => 0.0,
                'longitude' => 0.0,
                'wikiDataId' => 'Q4',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create test city
            \DB::table('cities')->insert([
                'name' => 'Test City',
                'state_id' => $stateId,
                'state_code' => 'TS01',
                'country_id' => $countryId,
                'country_code' => 'TS',
                'latitude' => 0.0,
                'longitude' => 0.0,
                'wikiDataId' => 'Q5',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Mock Vite manifest for testing when assets aren't built
     */
    protected function mockViteManifest(): void
    {
        $buildDir = public_path('build');
        if (!is_dir($buildDir)) {
            mkdir($buildDir, 0755, true);
        }

        $manifest = [
            'resources/css/app.css' => [
                'file' => 'assets/app.css',
                'src' => 'resources/css/app.css'
            ],
            'resources/js/app.js' => [
                'file' => 'assets/app.js',
                'src' => 'resources/js/app.js'
            ]
        ];

        file_put_contents($buildDir . '/manifest.json', json_encode($manifest));

        // Create dummy asset files
        $assetsDir = $buildDir . '/assets';
        if (!is_dir($assetsDir)) {
            mkdir($assetsDir, 0755, true);
        }

        file_put_contents($assetsDir . '/app.css', '/* Test CSS */');
        file_put_contents($assetsDir . '/app.js', '/* Test JS */');
    }

    protected function tearDown(): void
    {
        // Clean up mock files if they were created
        if (app()->environment('testing') && file_exists(public_path('build/manifest.json'))) {
            $buildDir = public_path('build');
            if (is_dir($buildDir)) {
                $this->deleteDirectory($buildDir);
            }
        }

        parent::tearDown();
    }

    /**
     * Recursively delete a directory
     */
    protected function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}
