# phpBB Studio - Textformatter documentation

## Table of Contents
- **[Textformatter plugins](#textformatter-plugins)**
- **[Autoimage](#autoimage)**
- **[Autovideo](#autovideo)**
- **[FancyPants](#fancypants)**
- **[HTML Comments](#html-comments)**
- **[HTML Entities](#html-entities)**
- **[Litedown](#litedown)**
- **[PipeTables](#pipetables)**
- **[Keywords](#keywords)**
  - _[Case insensitive](#case-insensitive)_
  - _[First occurrence](#first-occurrence)_
  - _[Keyword mapping](#keyword-mapping)_
  - _[Keyword template](#keyword-template)_
- **[BBCode parameters](#bbcode-parameters)**
  - _[BBCode template example](#bbcode-template-example)_
  - _[Parameters](#parameters)_

## Textformatter plugins
The [phpBB Forum Software](https://www.phpbb.com) uses the [s9e Textformatter](https://github.com/s9e/TextFormatter) developed by [JoshyPHP](https://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=1325630). This text formatter has a lot more possibilities and options than phpBB is using. In order to give you, the Administrator, full power over how your board's text is formatted, we have made this extension. This way you can easily enable / disable any plugin and optimise your custom BBCodes.

## Autoimage
> _Please have a look at s9e's own **[Autoimage synopsis](https://s9etextformatter.readthedocs.io/Plugins/Autoimage/Synopsis/)**._

This plugin converts plain-text image URLs into actual images. Only URLs starting with `http://` or `https://` and ending with `.gif`, `.jpeg`, `.jpg`, `.png`, `.svg`, `.svgz`, or `.webp` are converted.

## Autovideo
> _Please have a look at s9e's own **[Autovideo synopsis](https://s9etextformatter.readthedocs.io/Plugins/Autovideo/Synopsis/)**._

This plugin converts plain-text video URLs into playable videos. Only URLs starting with `http://` or `https://` and ending with `.mp4`, `.ogg` or `.webm` are converted.

## FancyPants
> _Please have a look at s9e's own **[FancyPants synopsis](https://s9etextformatter.readthedocs.io/Plugins/FancyPants/Synopsis/)**._

This plugin provides enhanced typography, aka _"fancy Unicode symbols"_.
This will convert plain text such as 
- `"quotes"` into “quotes” _(quotation quotes)_,
- `(c) (tm)` into &copy; &trade; _(common symbols)_,
- `--` into &mdash; _(medium dash)_,
- `...` into … _(ellipsis)_,
- etc..

## HTML Comments
> _Please have a look at s9e's own **[HTML Comments synopsis](https://s9etextformatter.readthedocs.io/Plugins/HTMLComments/Synopsis/)**._

This plugins allows HTML comments to be used. 
This will convert plain text `<!-- This is a comment -->` into the corresponding HTML comment that is not shown in the post. It is still visible in the HTML source code.

## HTML Entities
> _Please have a look at s9e's own **[HTML Entities synopsis](https://s9etextformatter.readthedocs.io/Plugins/HTMLEntities/Synopsis/)**._

By default, s9e\TextFormatter escapes HTML entities. This plugins allows HTML entities to be used.
This will convert plain text such as 
- `&copy;` into &copy; _(copyright symbol)_,
- `&trade;` into &trade; _(trademark symbol)_,
- `&mdash;` into &mdash; _(medium dash symbol)_,
- `&amp;` into &amp; _(ampersand symbol)_,
- etc..

## Litedown
> _Please have a look at s9e's own **[Litedown synopsis](https://s9etextformatter.readthedocs.io/Plugins/Litedown/Synopsis/)**._

This plugin implements a Markdown-like syntax, inspired by modern flavors of Markdown.

A more detailed description of the syntax is available in [s9e's Syntax documentation](https://s9etextformatter.readthedocs.io/Plugins/Litedown/Syntax/).

## PipeTables
> _Please have a look at s9e's own **[PipeTables synopsis](https://s9etextformatter.readthedocs.io/Plugins/PipeTables/Synopsis/)**._

This plugin implements a type of ASCII-style tables inspired by GitHub-flavored Markdown, Pandoc's pipe tables and PHP Markdown Extra's simple tables.

A more detailed description of the syntax is available in [s9e's Syntax documentation](https://s9etextformatter.readthedocs.io/Plugins/PipeTables/Syntax/).

Please note: A custom class will be added to all the PipeTables. This allows the table to be styled. The custom stylesheet has been included, which can be modified as you see fit.

## Keywords
> _Please have a look at s9e's own **[Keywords synopsis](https://s9etextformatter.readthedocs.io/Plugins/Keywords/Synopsis/)**._

This plugin serves to capture keywords in plain text and render them as a rich element of your choosing such as a link, a popup or a widget.

### Case insensitive
> _Please have a look at s9e's own **[Case Insensitive documentation](https://s9etextformatter.readthedocs.io/Plugins/Keywords/Synopsis/#examples)**._

It is also possible to make keyword case insensitive. So keywords are found regardless of their case, whether is it uppercase, lowercase or a mix of the two.

### First occurrence
> _Please have a look at s9e's own **[First Occurrence documentation](https://s9etextformatter.readthedocs.io/Plugins/Keywords/Synopsis/#how-to-only-capture-the-first-occurence-of-each-keyword)**._

It is also possible to only capture the first occurrence of a keyword in a post. This way only the first time the keyword is used, it will be replaced with the keyword template. However, this check is done with case sensitivity, regardless of the setting above. So if _"Only first occurrence"_ and _"Case insensitive"_ are turned on, both `Keyword` and `KeYwoRd` are still rendered.

### Keyword mapping
> _Please have a look at s9e's own **[Keyword Mapping documentation](https://s9etextformatter.readthedocs.io/Plugins/Keywords/Map/)**._

It is also possible to use a different value than the keyword in the _"Keyword template"_. Then you can do 'keyword mapping'. Associate a value to each keyword, which will be used instead of the keyword in the Template. However, this only has a simply 'Yes/No' option for ALL keywords. You can not define this on a per-keyword basis. So you either have to map all keywords or none.

### Keyword template
The keyword template is just like any other Custom BBCode template, as in that you can use HTML and XML to decide how it should be rendered. You can use the `{@value}` text to get the specific keyword. 

As an example:
```xml
<a href="http://bulbapedia.bulbagarden.net/wiki/{@value}"><xsl:apply-templates/></a>
```
```html
<a href="http://bulbapedia.bulbagarden.net/wiki/Pikachu">Pikachu</a>
```
Or if you've done _"keyword mapping"_, with (keyword: `array merge`, value: `array-merge`):
```xml
<a href="https://www.php.net/manual/en/function.{@value}.php"><xsl:apply-templates/></a>
```
```html
<a href="https://www.php.net/manual/en/function.array-merge.php">array merge</a>
```

## BBCode parameters
When creating Custom BBCodes, there are already a lot of possibilities. However, more are possible when using BBCode Template Parameters. These are parameters BBCode Templates can check against or use to display the BBCode.

### BBCode Template Example
```xml
<xsl:choose>
	<xsl:when test="$S_LOGGED_IN">
		You are currently logged in as:
		<a class="username-coloured">
			<xsl:attribute name="href">
				<xsl:value-of select="$USER_PROFILE"/>
			</xsl:attribute>
			<xsl:attribute name="style">
				<xsl:text>color: </xsl:text>
				<xsl:value-of select="$USER_COLOUR"/>
				<xsl:text>;</xsl:text>
			</xsl:attribute>
			<xsl:value-of select="$USER_NAME"/>
		</a>
	</xsl:when>
	<xsl:otherwise>
		<div>
			You need to
			<a>
				<xsl:attribute name="href">
					<xsl:value-of select="$U_LOGIN"/>
				</xsl:attribute>
				login
			</a>
			to read this content.
		</div>
	</xsl:otherwise>
</xsl:choose>
```

### Parameters
| Variable | Description
| --- | --- |
`USER_CLEAN` | The clean version of the user's username 
`USER_NAME` | The user's username
`USER_COLOUR` | The user's HEX colour _(`#AA000`)_
`USER_PROFILE` | The URL to the user's profile _(`./memberlist.php?mode=viewprofile&u=2`)_
`USER_ID` | The user identifier _(`2`)_
`USER_GROUP_ID` | The group identifier of this user's default group _(`5`)_
`USER_GROUP_IDS` | The group identifiers this user is a member of _(`2,3,6,8`)_
`USER_AVATAR_SRC` | The source URL for the user's avatar
`USER_AVATAR_WIDTH` | The width for the user's avatar
`USER_AVATAR_HEIGHT` | The height for the user's avatar
`USER_RANK_SRC` | The source URL for the user's rank
`USER_RANK_TITLE` | The title of the user's rank
`USER_POSTS` | The amount of posts this user has
`USER_LANG` | The user's default language _(`en`)_
`USER_LANG_NAME` | The user's default language name _(`British English`)_
`USER_STYLE` | The user's default style path _(`prosilver`)_
`USER_TIME` | The current time formatted to the user's preferences
`USER_TIMEZONE` | The user's timezone
`SITE_NAME` | The site name
`SITE_DESC` | The site description
`S_CONTENT_DIRECTION` | The content direction _(`ltr|rtl`)_
`S_NEW_PM` | If the user has a new Private Message
`S_FOUNDER` | If the user is a founder
`S_LOGGED_IN` | If the user is logged in
`S_REGISTERED` | If the user is a registered user
`S_ACP` | If the user has access to the ACP
`S_MCP` | If the user has access to the MCP
`U_BOARD` | The absolute URL to the board _(`http://www.example.com/index.php`)_
`U_BBCODE` | The URL to the BBCode FAQ
`U_CONTACT_US` | The URL to the Contact Us page
`U_FAQ` | The URL to the common FAQ
`U_INDEX` | The URL to the Board Index
`U_LOGIN` | The URL to log in
`U_LOGOUT` | The URL to log out
`U_PMS` | The URL to the Private Messages inbox
`U_PRIVACY` | The URL to the Privacy statement
`U_REGISTER` | The URL to register
`U_SEARCH` | The URL to search
`U_SEARCH_ACTIVE` | The URL to search active topics
`U_SEARCH_NEW` | The URL to search new posts
`U_SEARCH_SELF` | The URL to search own posts
`U_SEARCH_UNANSWERED` | The URL to search unanswered posts
`U_SEARCH_UNREAD` | The URL to search unread posts
`U_TEAM` | The URL to the Team page
`U_TERMS_USE` | The URL to the Terms of Use
`U_UCP` | The URL to the User Control Panel
