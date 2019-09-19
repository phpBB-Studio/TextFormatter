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

/**
 * phpBB Studio - Text formatter: ACP Module information
 */
class main_info
{
	public function module()
	{
		return [
			'filename'	=> '\phpbbstudio\textformatter\acp\main_module',
			'title'		=> 'ACP_STUDIO_TEXTFORMATTER',
			'modes'		=> [
				'settings'		=> [
					'title'	=> 'ACP_STUDIO_TF_SETTINGS',
					'auth'	=> 'ext_phpbbstudio/textformatter && acl_a_board',
					'cat'	=> ['ACP_STUDIO_TEXTFORMATTER'],
				],
				'keywords'		=> [
					'title'	=> 'ACP_STUDIO_TF_KEYWORDS',
					'auth'	=> 'ext_phpbbstudio/textformatter && acl_a_board',
					'cat'	=> ['ACP_STUDIO_TEXTFORMATTER'],
				],
			],
		];
	}
}
