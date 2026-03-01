<?php
/**
 *
 * AI & Scraper Shield. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026, Derky
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace derky\aiscrapershield\event;

use phpbb\event\data;
use phpbb\language\language;
use phpbb\user;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class main_listener implements EventSubscriberInterface
{
	public static function getSubscribedEvents(): array
	{
		return [
			'core.user_setup' => 'load_language_on_setup',
			'core.viewtopic_modify_forum_id' => 'check_session',
		];
	}

	protected language $language;
	protected user $user;

	public function __construct(language $language, user $user)
	{
		$this->language = $language;
		$this->user = $user;
	}

	public function load_language_on_setup(data $event): void
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = [
			'ext_name' => 'derky/aiscrapershield',
			'lang_set' => 'common',
		];
		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function check_session(data $event): void
	{
		$shield_passed = $this->user->data['session_aiscrapershield'] ?? 0;

		if (!$shield_passed)
		{
			// Show Captcha
		}
	}
}
