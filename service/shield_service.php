<?php
/**
 *
 * AI & Scraper Shield. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026, Derky
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace derky\aiscrapershield\service;

use phpbb\db\driver\driver_interface;

class shield_service
{
	public function __construct(protected driver_interface $db)
	{
	}

	public function mark_shield_passed(string $session_id): void
	{
		$sql = 'UPDATE ' . SESSIONS_TABLE . "
				SET session_aiscrapershield = 1
				WHERE session_id = '" . $this->db->sql_escape($session_id) . "'";
		$this->db->sql_query($sql);
	}
}
