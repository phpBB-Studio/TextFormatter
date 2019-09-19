<?php
/**
 *
 * phpBB Studio - Text formatter. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\textformatter\migrations;

/**
 * phpBB Studio - Text formatter: ACP module migration
 */
class install_acp_module extends \phpbb\db\migration\migration
{
	/**
	 * Checks whether the Text formatter ACP module does exist or not.
	 *
	 * This is checked when a migration is installed. If true is returned, the migration will be set as
	 * installed without performing the database changes.
	 * This function is intended to help moving to migrations from a previous database updater, where some
	 * migrations may have been installed already even though they are not yet listed in the migrations table.
	 *
	 * @return bool		True if this migration is installed, False if this migration is not installed (checked on install)
	 * @access public
	 */
	public function effectively_installed()
	{
		$sql = 'SELECT module_id
				FROM ' . $this->table_prefix . "modules
				WHERE module_class = 'acp'
					AND module_langname = 'ACP_STUDIO_TEXTFORMATTER'";
		$result = $this->db->sql_query($sql);
		$module_id = (bool) $this->db->sql_fetchfield('module_id');
		$this->db->sql_freeresult($result);

		return $module_id !== false;
	}

	/**
	 * Assign migration file dependencies for this migration.
	 *
	 * @return array		Array of migration files
	 * @access public
	 * @static
	 */
	static public function depends_on()
	{
		return ['\phpbb\db\migration\data\v32x\v327'];
	}

	/**
	 * Add the Text formatter ACP module to the database.
	 *
	 * @return array		Array of module data
	 * @access public
	 */
	public function update_data()
	{
		return [
			['module.add', [
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_STUDIO_TEXTFORMATTER',
			]],
			['module.add', [
				'acp',
				'ACP_STUDIO_TEXTFORMATTER',
				[
					'module_basename'	=> '\phpbbstudio\textformatter\acp\main_module',
					'modes'				=> ['settings', 'keywords'],
				],
			]],
		];
	}
}
