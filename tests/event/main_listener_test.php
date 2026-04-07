<?php
/**
 *
 * AI & Scraper Shield. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026, Derky
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace derky\aiscrapershield\tests\event;

use derky\aiscrapershield\event\main_listener;
use phpbb\event\data;

class main_listener_test extends \phpbb_test_case
{
	protected $helper;
	protected $language;
	protected $request;
	protected $user;
	protected $listener;

	protected function setUp(): void
	{
		parent::setUp();

		$this->helper = $this->getMockBuilder('\phpbb\controller\helper')
			->disableOriginalConstructor()
			->getMock();

		$this->language = $this->getMockBuilder('\phpbb\language\language')
			->disableOriginalConstructor()
			->getMock();

		$this->language->method('lang')
			->will($this->returnArgument(0));

		$this->request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();

		$this->user = $this->getMockBuilder('\phpbb\user')
			->disableOriginalConstructor()
			->getMock();

		$this->listener = new main_listener(
			$this->helper,
			$this->language,
			'php',
			$this->request,
			$this->user
		);
	}

	/**
	 * Test that registered users are not redirected
	 */
	public function test_check_session_registered_no_redirect()
	{
		$this->user->data = [
			'is_registered' => true,
			'is_bot' => false,
			'session_aiscrapershield' => 0,
		];

		$this->request->expects($this->never())
			->method('server');

		$this->helper->expects($this->never())
			->method('route');

		$this->listener->check_session(new data([]));
		$this->assertTrue(true);
	}

	/**
	 * Test that bot users are not redirected
	 */
	public function test_check_session_bot_no_redirect()
	{
		$this->user->data = [
			'is_registered' => false,
			'is_bot' => true,
			'session_aiscrapershield' => 0,
		];

		$this->request->expects($this->never())
			->method('server');

		$this->helper->expects($this->never())
			->method('route');

		$this->listener->check_session(new data([]));
		$this->assertTrue(true);
	}

	/**
	 * Test that guest users with shield_passed are not redirected
	 */
	public function test_check_session_guest_shield_passed_no_redirect()
	{
		$this->user->data = [
			'is_registered' => false,
			'is_bot' => false,
			'session_aiscrapershield' => 1,
		];

		$this->request->expects($this->never())
			->method('server');

		$this->helper->expects($this->never())
			->method('route');

		$this->listener->check_session(new data([]));
		$this->assertTrue(true);
	}
}
