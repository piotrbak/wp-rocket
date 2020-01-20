<?php

namespace WP_Rocket\Tests\Integration\CDN\RocketCDN;

use WP_Rocket\Tests\Integration\TestCase;
use WP_Rocket\CDN\RocketCDN\APIClient;

/**
 * @covers \WP_Rocket\Subscriber\CDN\RocketCDN\AdminPageSubscriber::purge_cache_request
 * @group  RocketCDN
 * @group  RocketCDNAPI
 */
class Test_PurgeCacheRequest extends TestCase {

	public function tearDown() {
		parent::tearDown();

		delete_transient( 'rocketcdn_status' );
		delete_option( 'rocketcdn_user_token' );
	}

	/**
	 * Test should return the error packet when there's no subscription ID.
	 */
	public function testShouldReturnErrorPacketWhenNoSubscriptionId() {
		set_transient( 'rocketcdn_status', [ 'is_active' => false ], MINUTE_IN_SECONDS );

		$this->assertSame(
			[
				'status'  => 'error',
				'message' => 'RocketCDN cache purge failed: Missing identifier parameter.',
			],
			( new APIClient )->purge_cache_request()
		);
	}

	/**
	 * Test should return the error packet when the subscription ID is 0.
	 */
	public function testShouldReturnErrorPacketWhenSubscriptionIdIsZero() {
		set_transient( 'rocketcdn_status', [ 'is_active' => 0 ], MINUTE_IN_SECONDS );

		$this->assertSame(
			[
				'status'  => 'error',
				'message' => 'RocketCDN cache purge failed: Missing identifier parameter.',
			],
			( new APIClient )->purge_cache_request()
		);
	}

	/**
	 * Test should return error packet when no user token.
	 */
	public function testShouldReturnErrorPacketWhenNoToken() {
		set_transient( 'rocketcdn_status', [ 'id' => 1 ], MINUTE_IN_SECONDS );

		$this->assertSame(
			[
				'status'  => 'error',
				'message' => 'RocketCDN cache purge failed: Missing user token.',
			],
			( new APIClient )->purge_cache_request()
		);
	}

	/**
	 * Test should return error packet when subscription ID or token is invalid.
	 */
	public function testShouldReturnErrorPacketWhenInvalidSubscriptionIdOrToken() {
		set_transient( 'rocketcdn_status', [ 'id' => 1 ], MINUTE_IN_SECONDS );
		update_option( 'rocketcdn_user_token', '9944b09199c62bcf9418ad846dd0e4bbdfc6ee4b' );

		$this->assertSame(
			[
				'status'  => 'error',
				'message' => 'RocketCDN cache purge failed: The API returned an unexpected response code.',
			],
			( new APIClient )->purge_cache_request()
		);
	}

	/**
	 * Test should return the status when set in the transient.
	 */
	public function testShouldReturnSuccessPacketWhenAPIPurgedCache() {
		$this->assertTrue( true );

		// TODO: Needs assertions once we have a valid user token for a dummy testing account.
	}
}