<?php
/**
 *
 * phpBB Studio - Text formatter. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\textformatter\acp;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * phpBB Studio - Text formatter: ACP Module
 */
class main_module
{
	/** @var \phpbb\cache\driver\driver_interface */
	protected $cache;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\config\db_text */
	protected $config_text;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var string Table prefix */
	protected $table_prefix;

	/** @var string ACP Page title */
	public $page_title;

	/** @var string ACP Page template */
	public $tpl_name;

	/** @var string Custom form action */
	public $u_action;

	public function main($id, $mode)
	{
		$this->inject_services();

		$this->tpl_name = "tf_{$mode}";
		$this->page_title = 'ACP_STUDIO_TF_' . utf8_strtoupper($mode);

		$this->language->add_lang('tf_acp', 'phpbbstudio/textformatter');

		switch ($mode)
		{
			case 'settings':
				$form_key = 'tf_settings';
				add_form_key($form_key);

				$settings = [
					'fancypants',
					'autoimage', 'autovideo',
					'htmlcomments', 'htmlentities',
					'litedown', 'pipetables',
					'keywords',
					'case', 'map', 'first',
				];

				if ($this->request->is_set_post('submit'))
				{
					if (!check_form_key($form_key))
					{
						trigger_error($this->language->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
					}

					$this->config_text->set('studio_tf_template', $this->request->variable('template', '', true));

					foreach ($settings as $setting)
					{
						$this->config->set("studio_tf_{$setting}", $this->request->variable($setting, false));
					}

					$this->cache->purge();

					trigger_error($this->language->lang('CONFIG_UPDATED') . adm_back_link($this->u_action));
				}

				$vars = [];

				foreach ($settings as $setting)
				{
					$vars[$setting] = $this->config["studio_tf_{$setting}"];
				}

				$this->template->assign_vars([
					'TF_SETTINGS'	=> $vars,
					'TF_TEMPLATE'	=> $this->config_text->get('studio_tf_template'),
					'U_ACTION'		=> $this->u_action,
				]);
			break;

			case 'keywords':
				$action = $this->request->variable('action', '', true);
				$keyword = $this->request->variable('k', 0);

				switch ($action)
				{
					case 'edit';
						$sql = 'SELECT keyword, value
								FROM ' . $this->table_prefix . 'studio_keywords
								WHERE keyword_id = ' . (int) $keyword;
						$result = $this->db->sql_query_limit($sql, 1);
						$row = $this->db->sql_fetchrow($result);
						$this->db->sql_freeresult($result);

						if ($row === false)
						{
							trigger_error($this->language->lang('ACP_STUDIO_TF_NOT_EXIST') . adm_back_link($this->u_action), E_USER_WARNING);
						}

					case 'add':
						$form_key = 'tf_keywords';
						add_form_key($form_key);

						$word = !empty($row['keyword']) ? $row['keyword'] : '';
						$value = !empty($row['value']) ? $row['value'] : '';

						$data = [
							'keyword'	=> $this->request->variable('keyword', $word, true),
							'value'		=> $this->request->variable('value', $value, true),
						];

						if ($this->request->is_set_post('submit'))
						{
							if (!check_form_key($form_key))
							{
								trigger_error($this->language->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
							}

							$sql = 'SELECT 1
									FROM ' . $this->table_prefix . "studio_keywords
									WHERE keyword = '" . $this->db->sql_escape('') . "'
										AND keyword_id <> " . (int) $keyword;
							$result = $this->db->sql_query_limit($sql, 1);
							$exists = $this->db->sql_fetchrow($result);
							$this->db->sql_freeresult($result);

							if ($exists !== false)
							{
								trigger_error($this->language->lang('ACP_STUDIO_TF_NOT_UNIQUE') . adm_back_link($this->u_action), E_USER_WARNING);
							}

							if ($action === 'add')
							{
								$sql = 'INSERT INTO ' . $this->table_prefix . 'studio_keywords ' . $this->db->sql_build_array('INSERT', $data);
								$this->db->sql_query($sql);
							}
							else
							{
								$sql = 'UPDATE ' . $this->table_prefix . 'studio_keywords
										SET ' . $this->db->sql_build_array('UPDATE', $data) . '
										WHERE keyword_id = ' . (int) $keyword;
								$this->db->sql_query($sql);
							}

							$this->cache->purge();

							trigger_error($this->language->lang('ACP_STUDIO_TF_' . utf8_strtoupper($action) . '_SUCCESS') . adm_back_link($this->u_action));
						}

						$this->template->assign_vars([
							'TF_KEYWORD'	=> $data['keyword'],
							'TF_VALUE'		=> $data['value'],

							'U_ACTION'		=> $this->u_action . "&amp;action={$action}&k={$keyword}",
						]);
					break;

					case 'delete':
						if (confirm_box(true))
						{
							$sql = 'DELETE FROM ' . $this->table_prefix . 'studio_keywords
									WHERE keyword_id = ' . (int) $keyword;
							$this->db->sql_query($sql);

							$message = 'ACP_STUDIO_TF_DELETE_SUCCESS';

							if (!$this->request->is_ajax())
							{
								$message .= adm_back_link($this->u_action);
							}

							$this->cache->purge();

							trigger_error($message);
						}
						else
						{
							confirm_box(false, $this->language->lang('CONFIRM_OPERATION'), '');

							redirect($this->u_action);
						}
					break;

					default:
						$sql = 'SELECT *
								FROM ' . $this->table_prefix . 'studio_keywords
								ORDER BY keyword';
						$result = $this->db->sql_query($sql);
						$rowset = $this->db->sql_fetchrowset($result);
						$this->db->sql_freeresult($result);

						$this->template->assign_vars([
							'TF_OVERVIEW'	=> true,
							'TF_KEYWORDS'	=> $rowset,

							'U_ADD'			=> $this->u_action . '&amp;action=add',
							'U_DELETE'		=> $this->u_action . '&amp;action=delete&k=',
							'U_EDIT'		=> $this->u_action . '&amp;action=edit&k=',
						]);
					break;
				}
			break;
		}
	}

	/**
	 * Inject services from the container.
	 *
	 * @return void
	 * @access protected
	 */
	protected function inject_services()
	{
		/** @var ContainerInterface $phpbb_container */
		global $phpbb_container;

		$this->cache		= $phpbb_container->get('cache.driver');
		$this->config		= $phpbb_container->get('config');
		$this->config_text	= $phpbb_container->get('config_text');
		$this->db			= $phpbb_container->get('dbal.conn');
		$this->language		= $phpbb_container->get('language');
		$this->request		= $phpbb_container->get('request');
		$this->template		= $phpbb_container->get('template');

		$this->table_prefix	= $phpbb_container->getParameter('core.table_prefix');
	}
}
