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
 * phpBB Studio - Text formatter: Configuration migration
 */
class install_configuration extends \phpbb\db\migration\migration
{
	/**
	 * Checks whether the Text formatter configuration does exist or not.
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
		return isset($this->config['studio_tf_case']);
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
	 * Add the Text formatter configuration to the database.
	 *
	 * @return array		Array of configuration
	 * @access public
	 */
	public function update_data()
	{
		return [
			['config.add', ['studio_tf_first', 0]],
			['config.add', ['studio_tf_case', 0]],
			['config.add', ['studio_tf_map', 0]],
			['config.add', ['studio_tf_keywords', 0]],

			['config.add', ['studio_tf_autoimage', 0]],
			['config.add', ['studio_tf_autovideo', 0]],
			['config.add', ['studio_tf_fancypants', 0]],
			['config.add', ['studio_tf_htmlcomments', 0]],
			['config.add', ['studio_tf_htmlentities', 0]],
			['config.add', ['studio_tf_litedown', 0]],
			['config.add', ['studio_tf_pipetables', 0]],

			['config_text.add', ['studio_tf_template', '']],
		];
	}
}
