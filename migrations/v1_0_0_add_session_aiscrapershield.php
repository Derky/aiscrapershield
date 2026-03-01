<?php
/**
 *
 * AI & Scraper Shield. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026, Derky
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace derky\aiscrapershield\migrations;

class v1_0_0_add_session_aiscrapershield extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'sessions', 'session_aiscrapershield');
	}

	public static function depends_on()
	{
		return ['\phpbb\db\migration\data\v330\v330'];
	}

	public function update_schema(): array
	{
		return [
			'add_columns'	=> [
				$this->table_prefix . 'sessions' => [
					'session_aiscrapershield' => ['UINT', 0],
				],
			],
		];
	}

	public function revert_schema(): array
	{
		return [
			'drop_columns'	=> [
				$this->table_prefix . 'sessions' => [
					'session_aiscrapershield',
				],
			],
		];
	}
}
