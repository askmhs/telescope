<?php

namespace Laravel\Telescope\Tests\Watchers;

use Laravel\Telescope\EntryType;
use Illuminate\Support\Facades\Event;
use Laravel\Telescope\Tests\FeatureTestCase;
use Laravel\Telescope\Watchers\EventWatcher;

class EventWatcherTest extends FeatureTestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app->get('config')->set('telescope.watchers', [
            EventWatcher::class => true,
        ]);
    }

    public function test_event_watcher_registers_any_events()
    {
        Event::listen(DummyEvent::class, function ($payload) {
        });

        event(new DummyEvent);

        $entry = $this->loadTelescopeEntries()->first();

        self::assertSame(EntryType::EVENT, $entry->type);
        self::assertSame(DummyEvent::class, $entry->content['name']);
    }

    public function test_event_watcher_stores_payloads()
    {
        Event::listen(DummyEvent::class, function ($payload) {
        });

        event(new DummyEvent('Telescope', 'Laravel', 'PHP'));

        $entry = $this->loadTelescopeEntries()->first();

        self::assertSame(EntryType::EVENT, $entry->type);
        self::assertSame(DummyEvent::class, $entry->content['name']);
        self::assertArrayHasKey('data', $entry->content['payload']);
        self::assertArraySubset(['Telescope', 'Laravel', 'PHP'], $entry->content['payload']['data']);

    }
}

class DummyEvent
{
    public $data;

    public function __construct(...$payload)
    {
        $this->data = $payload;
    }

    public function handle()
    {

    }
}
