<?php
/**
 *
 * phpBB Studio - Text formatter. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\textformatter\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * phpBB Studio - Text formatter: Event listener
 */
class listener implements EventSubscriberInterface
{
	/**
	 * Assign functions defined in this class to event listeners in the core.
	 *
	 * @static
	 * @return array
	 * @access public
	 */
	static public function getSubscribedEvents()
	{
		return [
			'core.text_formatter_s9e_configure_after'	=> [['renderer_keywords'], ['renderer_plugins']],
			'core.text_formatter_s9e_render_before'		=> 'renderer_parameters',
		];
	}

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\config\db_text */
	protected $config_text;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\language\language */
	protected $lang;

	/** @var \phpbb\path_helper */
	protected $path_helper;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string Table prefix */
	protected $table_prefix;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string php File extension */
	protected $php_ext;

	/**
	 * Constructor.
	 *
	 * @param  \phpbb\auth\auth						$auth			Auth object
	 * @param  \phpbb\cache\service					$cache			Cache service object
	 * @param  \phpbb\config\config					$config			Config object
	 * @param  \phpbb\config\db_text				$config_text	Config text object
	 * @param  \phpbb\db\driver\driver_interface	$db				Database object
	 * @param  \phpbb\controller\helper				$helper			Controller helper object
	 * @param  \phpbb\language\language				$lang			Language object
	 * @param  \phpbb\path_helper					$path_helper	Path helper object
	 * @param  \phpbb\template\template				$template		Template object
	 * @param  \phpbb\user							$user			User object
	 * @param  string								$table_prefix	Table prefix
	 * @return void
	 * @access public
	 */
	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\cache\service $cache,
		\phpbb\config\config $config,
		\phpbb\config\db_text $config_text,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\controller\helper $helper,
		\phpbb\language\language $lang,
		\phpbb\path_helper $path_helper,
		\phpbb\template\template $template,
		\phpbb\user $user,
		$table_prefix
	)
	{
		$this->auth			= $auth;
		$this->cache		= $cache;
		$this->config		= $config;
		$this->config_text	= $config_text;
		$this->db			= $db;
		$this->helper		= $helper;
		$this->lang			= $lang;
		$this->path_helper	= $path_helper;
		$this->template		= $template;
		$this->user			= $user;

		$this->table_prefix	= $table_prefix;
		$this->root_path	= $path_helper->get_phpbb_root_path();
		$this->php_ext		= $path_helper->get_php_ext();
	}

	/**
	 * Add renderer keywords.
	 *
	 * @event  text_formatter_s9e_configure_after
	 * @param  \phpbb\event\data	$event		The event object
	 * @return void
	 * @access public
	 */
	public function renderer_keywords($event)
	{
		if (!$this->config['studio_tf_keywords'])
		{
			return;
		}

		$configurator = $event['configurator'];

		$keywords = [];

		$s_first = (bool) $this->config['studio_tf_first'];
		$s_lower = (bool) $this->config['studio_tf_case'];
		$s_map = (bool) $this->config['studio_tf_map'];

		$sql = 'SELECT *
				FROM ' . $this->table_prefix . 'studio_keywords';
		$result = $this->db->sql_query($sql, 3600);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$keyword = $row['keyword'];

			$configurator->Keywords->add($keyword);

			if ($s_lower)
			{
				$keyword = utf8_strtolower($keyword);
			}

			if ($s_map)
			{
				$value = !empty($row['value']) ? $row['value'] : $keyword;

				$keywords[$keyword] = $value;
			}
			else
			{
				$keywords[] = $keyword;
			}
		}
		$this->db->sql_freeresult($result);

		$tag = $configurator->Keywords->getTag();

		$tag->template = htmlspecialchars_decode($this->config_text->get('studio_tf_template'));

		if ($s_first)
		{
			$configurator->Keywords->onlyFirst = true;
		}

		if ($s_lower)
		{
			$configurator->Keywords->caseSensitive = false;

			$tag->attributes['value']
				->filterChain
				->append('strtolower');
		}

		if ($s_map)
		{
			$tag->attributes['value']
				->filterChain
				->append($configurator->attributeFilters->get('#hashmap'))
				->setMap($keywords);
		}

		$event['configurator'] = $configurator;
	}

	/**
	 * Enable renderer plugins.
	 *
	 * @event  text_formatter_s9e_configure_after
	 * @param  \phpbb\event\data	$event		The event object
	 * @return void
	 * @access public
	 */
	public function renderer_plugins($event)
	{
		$configurator = $event['configurator'];

		$plugins = [
			'Autoimage', 'Autovideo',
			'FancyPants', 'HTMLComments', 'HTMLEntities',
			'Litedown', 'PipeTables',
		];

		foreach ($plugins as $plugin)
		{
			if ($this->config['studio_tf_' . utf8_strtolower($plugin)])
			{
				$configurator->plugins->load($plugin);
			}
		}

		if ($this->config['studio_tf_pipetables'])
		{
			/** @var \s9e\TextFormatter\Configurator\Items\TemplateDocument $template */
			$template = $configurator->tags['TABLE']->template->asDOM();

			/** @var \DOMNodeList $tables */
			$tables = $template->getElementsByTagName('table');

			/** @var \DOMElement $table */
			foreach ($tables as $table)
			{
				$table->setAttribute('class', 'tf-table');
			}

			$template->saveChanges();
		}

		$event['configurator'] = $configurator;
	}

	/**
	 * Add renderer parameters.
	 *
	 * @event  text_formatter_s9e_render_before
	 * @param  \phpbb\event\data	$event		The event object
	 * @return void
	 * @access public
	 */
	public function renderer_parameters($event)
	{
		/** @var \phpbb\textformatter\s9e\renderer $renderer */
		$renderer = $event['renderer'];

		$avatar = $this->get_user_avatar();
		$rank = $this->get_user_rank();

		$renderer->get_renderer()->setParameters([
			'USER_CLEAN'			=> (string) $this->user->data['username_clean'],
			'USER_NAME'				=> (string) $this->user->data['username'],

			'USER_COLOUR'			=> (string) get_username_string('colour', $this->user->data['user_id'], $this->user->data['username'], $this->user->data['user_colour']),
			'USER_PROFILE'			=> (string) get_username_string('profile', $this->user->data['user_id'], $this->user->data['username'], $this->user->data['user_colour']),

			'USER_ID'				=> (int) $this->user->data['user_id'],
			'USER_GROUP_ID'			=> (int) $this->user->data['group_id'],
			'USER_GROUP_IDS'		=> (string) implode(',', $this->get_user_groups()),

			'USER_AVATAR_SRC'		=> (string) $avatar['src'],
			'USER_AVATAR_WIDTH'		=> (int) $avatar['width'],
			'USER_AVATAR_HEIGHT'	=> (int) $avatar['height'],
			'USER_RANK_SRC'			=> $rank ? (string) $rank['src'] : '',
			'USER_RANK_TITLE'		=> $rank ? (string) $rank['title'] : '',

			'USER_POSTS'			=> (int) $this->user->data['user_posts'],
			'USER_LANG'				=> (string) $this->user->data['user_lang'],
			'USER_LANG_NAME'		=> (string) $this->user->lang_name,
			'USER_STYLE'			=> (string) $this->user->style['style_path'],
			'USER_TIME'				=> (string) $this->user->format_date(time()),
			'USER_TIMEZONE'			=> (string) $this->user->data['user_timezone'],

			'SITE_NAME'				=> (string) $this->config['sitename'],
			'SITE_DESC'				=> (string) $this->config['site_desc'],

			'S_CONTENT_DIRECTION'	=> (string) $this->lang->lang('DIRECTION'),

			'S_NEW_PM'				=> (bool) ($this->user->data['is_registered'] && $this->user->data['user_new_privmsg'] && (!$this->user->data['user_last_privmsg'] || $this->user->data['user_last_privmsg'] > $this->user->data['session_last_visit'])),

			'S_FOUNDER'				=> (bool) ((int) $this->user->data['user_type'] === USER_FOUNDER),
			'S_LOGGED_IN'			=> (bool) ((int) $this->user->data['user_id'] !== ANONYMOUS),
			'S_REGISTERED'			=> (bool) $this->user->data['is_registered'],

			'S_ACP'					=> (bool) (!empty($this->user->data['is_registered']) && $this->auth->acl_get('a_')),
			'S_MCP'					=> (bool) (!empty($this->user->data['is_registered']) && ($this->auth->acl_get('m_') || $this->auth->acl_getf_global('m_'))),

			'U_BOARD'				=> generate_board_url(),
			'U_BBCODE'				=> $this->helper->route('phpbb_help_bbcode_controller'),
			'U_CONTACT_US'			=> ($this->config['contact_admin_form_enable'] && $this->config['email_enable']) ? $this->append_sid('memberlist', 'mode=contactadmin') : '',
			'U_FAQ'					=> $this->helper->route('phpbb_help_faq_controller'),
			'U_INDEX'				=> $this->append_sid('index'),
			'U_LOGIN'				=> $this->append_sid('ucp', 'mode=login'),
			'U_LOGOUT'				=> $this->append_sid('ucp', 'mode=logout'),
			'U_PMS'					=> $this->append_sid('ucp', 'i=pm&amp;folder=inbox'),
			'U_PRIVACY'				=> $this->append_sid('ucp', 'mode=privacy'),
			'U_REGISTER'			=> $this->append_sid('ucp', 'mode=register'),
			'U_SEARCH'				=> $this->append_sid('search'),
			'U_SEARCH_ACTIVE'		=> $this->append_sid('search', 'search_id=active_topics'),
			'U_SEARCH_NEW'			=> $this->append_sid('search', 'search_id=newposts'),
			'U_SEARCH_SELF'			=> $this->append_sid('search', 'search_id=egosearch'),
			'U_SEARCH_UNANSWERED'	=> $this->append_sid('search', 'search_id=unanswered'),
			'U_SEARCH_UNREAD'		=> $this->append_sid('search', 'search_id=unreadposts'),
			'U_TEAM'				=> !$this->auth->acl_get('u_viewprofile') ? '' : $this->append_sid('memberlist', 'mode=team'),
			'U_TERMS_USE'			=> $this->append_sid('ucp', 'mode=terms'),
			'U_UCP'					=> $this->append_sid('ucp'),
		]);

		$this->template->assign_var('STUDIO_TF_PIPETABLES', (bool) $this->config['studio_tf_pipetables']);

		$event['renderer'] = $renderer;
	}

	/**
	 * Wrapper function for append_sid.
	 *
	 * @param  string	$page		The phpBB page
	 * @param  string	$params		The URL parameters
	 * @return string				The URL with a session id appended
	 * @access public
	 */
	protected function append_sid($page, $params = '')
	{
		return (string) append_sid("{$this->root_path}{$page}.{$this->php_ext}", $params);
	}

	/**
	 * Get the group identifiers this user is a member of.
	 *
	 * @return array				The group identifiers
	 * @access protected
	 */
	protected function get_user_groups()
	{
		$cache = $this->cache->get_driver();
		$key = '_studio_user_groups_' . $this->user->data['user_id'];

		$group_ids = $cache->get($key);

		if ($group_ids === false)
		{
			$group_ids = [];

			$sql = 'SELECT group_id
					FROM ' . $this->table_prefix . 'user_group
					WHERE user_pending = 0
						AND user_id = ' . (int) $this->user->data['user_id'];
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$group_ids[] = (int) $row['group_id'];
			}
			$this->db->sql_freeresult($result);

			$cache->put($key, $group_ids);
		}

		return $group_ids;
	}

	/**
	 * Get user avatar attributes from the HTML formatted string.
	 *
	 * @return array				The avatar data
	 * @access protected
	 */
	protected function get_user_avatar()
	{
		$avatar = phpbb_get_user_avatar($this->user->data);

		if (empty($avatar))
		{
			return [
				'src'		=> '',
				'width'		=> 0,
				'height'	=> 0,
			];
		}

		$doc = new \DOMDocument();
		$doc->loadHTML($avatar);
		$xpath = new \DOMXPath($doc);

		$src = $xpath->evaluate("string(//img/@src)");
		$width = $xpath->evaluate("number(//img/@width)");
		$height = $xpath->evaluate("number(//img/@height)");

		return [
			'src'		=> $src,
			'width'		=> $width,
			'height'	=> (int) $height,
		];
	}

	/**
	 * Mimic the phpbb_get_user_rank() function,
	 * so we do not have to include functions_display on every page.
	 *
	 * @return array				The rank data
	 * @access protected
	 */
	protected function get_user_rank()
	{
		$data = [];

		$path = $this->path_helper->update_web_root_path($this->root_path . $this->config['ranks_path']) . '/';

		$ranks = $this->cache->obtain_ranks();

		if (!empty($user_data['user_rank']))
		{
			$rank = !empty($ranks['special'][$this->user->data['user_rank']]) ? $ranks['special'][$this->user->data['user_rank']] : [];

			$data['title']	= isset($rank['rank_title']) ? $rank['rank_title'] : '';
			$data['src']	= !empty($rank['rank_image']) ? $path . $rank['rank_image'] : '';
			$data['img']	= !empty($rank['rank_image']) ? '<img src="' . $data['src'] . '" alt="' . $data['title'] . '" title="' . $data['title'] . '" />' : '';
		}
		else if ($this->user->data['user_posts'] != 0)
		{
			if (!empty($ranks['normal']))
			{
				foreach ($ranks['normal'] as $rank)
				{
					if ($this->user->data['user_posts'] >= $rank['rank_min'])
					{
						$data['title']	= $rank['rank_title'];
						$data['src']	= !empty($rank['rank_image']) ? $path . $rank['rank_image'] : '';
						$data['img']	= !empty($rank['rank_image']) ? '<img src="' . $data['src'] . '" alt="' . $data['title'] . '" title="' . $data['title'] . '" />' : '';

						break;
					}
				}
			}
		}

		return $data;
	}
}
