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
 * phpBB Studio - Text formatter: Tables migration
 */
class install_tables extends \phpbb\db\migration\migration
{
	/**
	 * Checks whether the Text formatter table does exist or not.
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
		return $this->db_tools->sql_table_exists($this->table_prefix . 'studio_keywords');
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
		return ['\phpbbstudio\textformatter\migrations\install_acp_module'];
	}

	/**
	 * Add the Text formatter tables and columns to the database.
	 *
	 * @return array		Array of tables and columns data
	 * @access public
	 */
	public function update_schema()
	{
		return [
			'add_tables'		=> [
				$this->table_prefix . 'studio_keywords'	=> [
					'COLUMNS'		=> [
						'keyword_id'	=> ['ULINT', null, 'auto_increment'],
						'keyword'		=> ['VCHAR_UNI', ''],
						'value'			=> ['VCHAR_UNI', ''],
					],
					'PRIMARY_KEY'	=> 'keyword_id',
					'KEYS'			=> [
						'keyword'		=> ['UNIQUE', 'keyword'],
					],
				],
			],
		];
	}

	/**
	 * Reverts the database schema by providing a set of change instructions
	 *
	 * @return array    Array of schema changes
	 * 					(compatible with db_tools->perform_schema_changes())
	 * @access public
	 */
	public function revert_schema()
	{
		return [
			'drop_tables'		=> [
				$this->table_prefix . 'studio_keywords',
			],
		];
	}
}
