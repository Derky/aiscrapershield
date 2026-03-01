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

use phpbb\controller\helper;
use phpbb\event\data;
use phpbb\language\language;
use phpbb\user;
use phpbb\request\request;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class main_listener implements EventSubscriberInterface
{
	public static function getSubscribedEvents(): array
	{
		return [
			'core.user_setup' => 'load_language_on_setup',
			'core.viewtopic_modify_forum_id' => 'check_session',
			'core.viewonline_overwrite_location' => 'viewonline_page',
		];
	}

	public function __construct(
		protected helper $helper,
		protected language $language,
		protected string $php_ext,
		protected request $request,
		protected user $user
	)
	{
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
		if (!$this->user->data['is_registered'] && !$this->user->data['is_bot'])
		{
			$shield_passed = $this->user->data['session_aiscrapershield'] ?? 0;

			if (!$shield_passed)
			{
				$redirect_url = $this->helper->route('derky_aiscrapershield_controller', [
					'redirect' => $this->request->server('REQUEST_URI'),
				]);
				$redirect_url = reapply_sid($redirect_url, true);

				redirect($redirect_url);
			}
		}
	}

	public function viewonline_page(data $event): void
	{
		$route = $this->helper->route('derky_aiscrapershield_controller');

		if ($event['on_page'][1] === 'app' && strrpos('/' . $event['row']['session_page'], $route) === 0)
		{
			$event['location'] = $this->language->lang('AI_SCRAPER_SHIELD');
			$event['location_url'] = $route;
		}
	}
}
