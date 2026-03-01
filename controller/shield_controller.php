<?php
/**
 *
 * AI & Scraper Shield. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026, Derky
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace derky\aiscrapershield\controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use phpbb\config\config;
use phpbb\controller\helper;
use phpbb\template\template;
use phpbb\language\language;
use phpbb\captcha\factory;
use phpbb\user;
use phpbb\request\request_interface;
use derky\aiscrapershield\service\shield_service;

class shield_controller
{
	public function __construct(
		protected factory $captcha_factory,
		protected config $config,
		protected helper $helper,
		protected language $language,
		protected string $phpbb_ext,
		protected string $phpbb_root_path,
		protected request_interface $request,
		protected shield_service $shield_service,
		protected template $template,
		protected user $user
	)
	{
	}

	/**
	 * @return Response
	 */
	public function handle(): Response
	{
		// Send bots or logged-in users back to the forum index
		if ($this->user->data['is_registered'] || $this->user->data['is_bot'])
		{
			$redirect_url = append_sid($this->phpbb_root_path . 'index.' . $this->phpbb_ext);
			return new RedirectResponse($redirect_url);
		}

		$page_title = $this->language->lang('SHIELD_PAGE_TITLE');
		$submit = $this->request->is_set_post('submit');
		$form_name = 'ai_scraper_shield';

		$captcha = $this->captcha_factory->get_instance($this->config['captcha_plugin']);
		$captcha->init(CONFIRM_POST);

		if ($submit)
		{
			if (!check_form_key($form_name))
			{
				$error[] = $this->language->lang('FORM_INVALID');
			}
			else
			{
				$redirect = $this->request->variable('redirect', '');

				$captcha_data = ['redirect' => $redirect];
				$vc_response = $captcha->validate($captcha_data);
				if ($vc_response)
				{
					$error[] = $vc_response;
				}
				else
				{
					if ($captcha->is_solved() === true)
					{
						$captcha->reset();
						$this->shield_service->mark_shield_passed($this->user->session_id);

						$redirect_url = $redirect ?: ($this->request->header('Referer') ?: append_sid($this->phpbb_root_path . 'index.' . $this->phpbb_ext));

						// Decode is needed because additional parameters such as &hilit= are decoded as &amp;hilit= and will otherwise be blocked as "INSECURE_REDIRECT"
						$redirect_url = htmlspecialchars_decode(redirect($redirect_url, true), ENT_QUOTES);
						return new RedirectResponse($redirect_url);
					}
				}
			}
		}

		add_form_key($form_name);

		$this->template->assign_vars(array(
			'CAPTCHA_TEMPLATE' => $captcha->get_template(),
			'ERROR'	=> !empty($error) ? implode('<br />', $error) : '',
		));

		return $this->helper->render('@derky_aiscrapershield/ai_scraper_shield_body.html', $page_title);
	}
}
