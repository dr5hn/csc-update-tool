<?php

use App\Models\User;
use App\Models\ChangeRequest;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Notifications\AdminChangeRequestNotification;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->admin = User::factory()->create(['is_admin' => true]);
});

describe('Change Request Creation', function () {
    test('user can view change request form', function () {
        $response = $this->actingAs($this->user)
            ->get(route('change-requests.new'));

        $response->assertStatus(200);
        $response->assertViewIs('change-requests.new');
    });

    test('user can create a draft change request', function () {
        $data = [
            'title' => 'Test Change Request',
            'description' => 'Test description',
            'new_data' => json_encode([
                'additions' => [
                    'added-country_1' => [
                        'name' => 'Test Country',
                        'iso2' => 'TC'
                    ]
                ]
            ])
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('change-requests.storeDraft'), $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('change_requests', [
            'title' => 'Test Change Request',
            'status' => 'draft',
            'user_id' => $this->user->id
        ]);
    });

    test('user can submit a change request', function () {
        Notification::fake();

        $data = [
            'title' => 'Test Change Request',
            'description' => 'Test description',
            'new_data' => json_encode([
                'additions' => [
                    'added-country_1' => [
                        'name' => 'Test Country',
                        'iso2' => 'TC'
                    ]
                ]
            ])
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('change-requests.store'), $data);

        $response->assertStatus(200);

        $changeRequest = ChangeRequest::where('title', 'Test Change Request')->first();
        expect($changeRequest->status)->toBe('pending');

        // Assert admin notification was sent
        Notification::assertSentTo(
            User::where('is_admin', true)->get(),
            AdminChangeRequestNotification::class
        );
    });
});

describe('Change Request Management', function () {
    test('user can view their change requests', function () {
        $changeRequest = ChangeRequest::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'My Change Request'
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('change-requests.index'));

        $response->assertStatus(200);
        $response->assertSee('My Change Request');
    });

    test('admin can approve change request', function () {
        $changeRequest = ChangeRequest::factory()->create([
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson(route('change-requests.approve', $changeRequest));

        $response->assertStatus(200);

        $changeRequest->refresh();
        expect($changeRequest->status)->toBe('approved');
        expect($changeRequest->approved_by)->toBe($this->admin->id);
    });

    test('admin can reject change request', function () {
        $changeRequest = ChangeRequest::factory()->create([
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson(route('change-requests.reject', $changeRequest), [
                'rejection_reason' => 'Invalid data provided'
            ]);

        $response->assertStatus(200);

        $changeRequest->refresh();
        expect($changeRequest->status)->toBe('rejected');
        expect($changeRequest->rejection_reason)->toBe('Invalid data provided');
    });
});

describe('SQL Generation', function () {
    test('can generate SQL for change request', function () {
        $changeRequest = ChangeRequest::factory()->create([
            'new_data' => json_encode([
                'additions' => [
                    'added-country_1' => [
                        'name' => 'Test Country',
                        'iso2' => 'TC'
                    ]
                ]
            ])
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('change-requests.sql', $changeRequest));

        $response->assertStatus(200);
        $response->assertViewIs('change-requests.sql');
    });

    test('can download SQL for change request', function () {
        $changeRequest = ChangeRequest::factory()->create([
            'new_data' => json_encode([
                'additions' => [
                    'added-country_1' => [
                        'name' => 'Test Country',
                        'iso2' => 'TC'
                    ]
                ]
            ])
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('change-requests.sql.download', $changeRequest));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain');
        expect($response->headers->get('Content-Disposition'))
            ->toContain('attachment');
    });
});

describe('Comments', function () {
    test('user can add comment to change request', function () {
        $changeRequest = ChangeRequest::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('change-requests.storeComment', $changeRequest), [
                'comment' => 'This is a test comment'
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'change_request_id' => $changeRequest->id,
            'user_id' => $this->user->id,
            'comment' => 'This is a test comment'
        ]);
    });
});

describe('Authorization', function () {
    test('guest cannot access change requests', function () {
        $response = $this->get(route('change-requests.index'));
        $response->assertRedirect(route('login'));
    });

    test('user can only edit their own draft requests', function () {
        $otherUser = User::factory()->create();
        $changeRequest = ChangeRequest::factory()->create([
            'user_id' => $otherUser->id,
            'status' => 'draft'
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('change-requests.edit', $changeRequest));

        $response->assertStatus(403);
    });

    test('only admin can approve/reject requests', function () {
        $changeRequest = ChangeRequest::factory()->create([
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('change-requests.approve', $changeRequest));

        $response->assertStatus(403);
    });
});
