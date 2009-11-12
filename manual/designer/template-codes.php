<html>
	<head>
		<link rel="stylesheet" href="/css/styles.css" />
	</head>
	<body>
		<h1>WebWorks WebME manual</h1>
		<h2>Designer Documentation - Template Codes</h2>
		<p>Following is a list of common codes used by the WebME engine.</p>
		<table>
			<tr><th>Code</th><th>Description</th></tr>
			<tr><th><code>{$PAGECONTENT}</code></th><td>This is the content of the page; the "body" of the page. This changes depending on the page you are on.</td></tr>
			<tr><th><code>{$WEBSITE_TITLE}</code></th><td>The website title. This is site-wide, and is set in the Site Options part of the admin.</td></tr>
			<tr><th><code>{$WEBSITE_SUBTITLE}</code></th><td>The website sub-title, if it has one. This is site-wide, and is set in the Site Options part of the admin.</td></tr>
		</table>
		<p>And here is a list of common functions. These are different to codes, in that they can include parameters to adjust what is printed to the HTML.</p>
		<table>
			<tr><th>Function</th><th>Description</th><th>Parameters</th><th>Examples</th></tr>
			<tr><th><code>{LOGO}</code></th><td>Prints out the site logo to the screen, resizing if necessary.</td><td><code>width</code>: the maximum width to be shown. defaults to 64<br /><code>height</code>: the maximum height to be shown. defaults to 64</td><td><code>{LOGO&nbsp;height=64&nbsp;width=98}</code><br /><code>{LOGO}</code></td></tr>
			<tr><th><code>{MENU}</code></th><td>Prints the menu to the HTML, including classes as appropriate.</td><td><code>mode</code>: the style of menu. choose from 'accordion', 'two-tier' and 'default'. defaults to 'default'<br /><code>preopen</code>: open up the menu to the current page. no values necessary<br /><code>direction</code>: how the menu is drawn. choose from 'horizontal' or 'vertical'. defaults to 'horizontal'.<br /><code>close</code>: whether to allow submenus to be closed. choose from 'yes' or 'no'. defaults to 'yes'.<br /><code>parent</code>: what is the root page of the menu. enter a page's name. defaults to the top-level.<br /><code>nodropdowns</code>: use this if you don't want submenus to appear.</td> <td><code>{MENU}</code><br /><code>{MENU mode="accordion"&nbsp;direction="vertical"}</code><br /><code>{MENU direction="vertical"&nbsp;preopen="yes"}</code><br /><code>{MENU close="no"}</code><br /><code>{MENU parent="/parent/page"}</code><br /><code>{MENU nodropdowns="yes"}</code></td></tr>
		</table>
		<p>Finally, there are the plugin-specific functions. For these to work, you need to have the relevant plugins enabled in the Site Options. Here is a list of the most common ones.</p>
		<table>
			<tr><th>Plugin</th><th>Function</th><th>Description</th><th>Parameters</th><th>Examples</th></tr>
			<tr><th>Banner-Image</th><td><code>{BANNER}</code></td><td>Draws a banner to the screen.</td><td><code>default</code>: HTML to show if no banner is selected in admin. defaults to blank</td><td><code>{BANNER&nbsp;default="&lt;img&nbsp;src=\"/f/banner.jpg\"&nbsp;/&gt;"}</code></td></tr>
			<tr><th>Mailing List</th><td><code>{MAILING_LIST}</code></td><td>Draws a simple form to allow people to subscribe to mailing lists</td><td><i>none</i></td><td><code>{MAILING_LIST}</code></td></tr>
			<tr><th>Panels</th><td><code>{PANEL}</code></td><td>Creates a panel, which can contain Widgets</td><td><code>name</code>: the name of the panel. it's common to name it after the location on the page, such as "left" or "right" or "footer" or "header"</td><td><code>{PANEL name="right"}</code></td></tr>
		</table>
	</body>
</html>
