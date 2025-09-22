<?php

namespace Tests\Feature;

use App\Models\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ViewConcertListingTest extends TestCase
{

    use DatabaseMigrations;

    #[Test]
    public function userCanViewPublishedConcerts(): void
    {
        $concert = Concert::factory()->published()->create([
            'title' => 'The Red Chord',
            'subtitle' => 'with Animosity and Lethargy',
            'date' => Carbon::parse('December 13, 2016 8:00pm'),
            'ticket_price' => 3250,
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Example Lane',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '17916',
            'additional_information' => 'For tickets, call (555) 555-5555.'
        ]);

        $response = $this->get('/concerts/' . $concert->id);

        $response->assertStatus(200);
        $response->assertSeeText('The Red Chord');
        $response->assertSeeText('with Animosity and Lethargy');
        $response->assertSeeText('December 13, 2016');
        $response->assertSeeText('8:00pm');
        $response->assertSeeText('32.50');
        $response->assertSeeText('The Mosh Pit');
        $response->assertSeeText('123 Example Lane');
        $response->assertSeeText('Laraville, ON 17916');
        $response->assertSeeText('For tickets, call (555) 555-5555.');
    }

    #[Test]
    public function userCannotViewUnpublishedConcerts(): void
    {
        $concert = Concert::factory()->unpublished()->create();

        $response = $this->get('/concerts/' . $concert->id);

        $response->assertStatus(404);
    }
}
