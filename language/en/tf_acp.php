<?php
/**
 *
 * phpBB Studio - Text formatter. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

/**
 * Some characters you may want to copy&paste: ’ » “ ” …
 */
$lang = array_merge($lang, [
	'ACP_STUDIO_TF_FIRST'			=> 'Only render first keyword',
	'ACP_STUDIO_TF_CASE'			=> 'Make keywords case insensitive',
	'ACP_STUDIO_TF_MAP'				=> 'Map keywords to their value',
	'ACP_STUDIO_TF_TEMPLATE'		=> 'Keywords BBCode template',
	'ACP_STUDIO_TF_FANCYPANTS'		=> 'FancyPants',
	'ACP_STUDIO_TF_AUTOIMAGE'		=> 'Autoimage',
	'ACP_STUDIO_TF_AUTOVIDEO'		=> 'Autovideo',
	'ACP_STUDIO_TF_HTMLCOMMENTS'	=> 'HTML Comments',
	'ACP_STUDIO_TF_HTMLENTITIES'	=> 'HTML Entities',
	'ACP_STUDIO_TF_LITEDOWN'		=> 'Litedown',
	'ACP_STUDIO_TF_PIPETABLES'		=> 'PipeTables',
	'ACP_STUDIO_TF_KEYWORD'			=> 'Keyword',

	'ACP_STUDIO_TF_ADD_SUCCESS'		=> 'You have successfully added the keyword.',
	'ACP_STUDIO_TF_DELETE_SUCCESS'	=> 'You have successfully deleted the keyword.',
	'ACP_STUDIO_TF_EDIT_SUCCESS'	=> 'You have successfully edited the keyword.',

	'ACP_STUDIO_TF_NOT_EXIST'		=> 'The requested keyword does not exist.',
	'ACP_STUDIO_TF_NOT_UNIQUE'		=> 'The keyword you have entered is not unique.',
]);
