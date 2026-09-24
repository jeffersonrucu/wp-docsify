=== Docsify Docs ===
Contributors:      jeffersonrucu, studiostg
Tags:              documentation, docsify, markdown, docs, knowledge-base
Requires at least: 5.9
Tested up to:      7.0
Requires PHP:      7.4
Stable tag:        3.6.1
License:           GPL-2.0+
License URI:       https://www.gnu.org/licenses/gpl-2.0.txt

Integrate Docsify documentation into WordPress using a custom page template with role-based access control.

== Description ==

Docsify Docs embeds the [Docsify](https://docsify.js.org) documentation generator into your WordPress site as a custom page template. It renders Markdown (.md) files stored in your uploads directory — no database required.

**Features**

* Custom page template — select "Docsify Docs" on any WordPress page
* Role-based access control configured from the admin panel
* Markdown files served through WordPress, so the access rule covers the files and not just the page
* Admin settings panel under the Docsify Docs menu
* Custom logo picked from the Media Library
* Docsify plugins included: full-text search, pagination, copy code, collapsible sidebar, Mermaid diagrams
* Interactive API documentation with Swagger UI, from an OpenAPI file kept with the documentation
* Step-by-step guides: a numbered list marked with `<!-- docsify-guide -->` is rendered one step at a time, with the screenshot of each step
* All scripts and styles bundled with the plugin — no external CDN requests
* Documentation page isolated from theme and block styles
* Documentation files stored in `wp-content/uploads/docsify-docs/` — survives plugin updates
* Sample documentation copied to uploads on activation
* No Composer required — built-in PSR-4 autoloader

== Installation ==

**Installing the .zip from the WordPress admin**

1. Download `docsify-docs-3.0.0.zip`.
2. In WordPress, go to **Plugins > Add New Plugin** and click **Upload Plugin** at the top of the screen.
3. Choose the .zip file and click **Install Now**. Do not unzip it first.
4. Click **Activate Plugin**.

Upgrading from `wp-docsify`? Deactivate and delete it before activating this one. Deleting it does not touch your documentation, which lives in `wp-content/uploads/`.

**Installing manually (FTP/SSH)**

1. Unzip the file and upload the `docsify-docs` folder to `wp-content/plugins/`.
2. Activate the plugin through the **Plugins** menu in WordPress.

**After activating**

1. Go to **Docsify Docs** in the admin menu.
2. Click **Generate documentation page**. This creates a published page using the correct template — no manual setup needed.
3. Click **View page** to open it. Sample documentation is already rendering.
4. Optional: change the page address in the **Page URL** field, then click **Save URL**.
5. Under **Appearance**, upload your logo and pick the theme color.
6. Under **Access Control**, enable **Enable Restriction** and check the roles allowed to read the documentation.
7. Replace the Markdown files in `wp-content/uploads/docsify-docs/` with your own. `README.md` is the home page, `_sidebar.md` is the menu, and `_navbar.md` is the top bar.

Prefer to attach the documentation to a page you already have? Edit that page and pick the **Docsify Docs** template under **Page Attributes** instead of using the generate button.

== Frequently Asked Questions ==

= Where do I put my documentation files? =

After activation, sample docs are copied to `wp-content/uploads/docsify-docs/`. Replace or extend those Markdown files. `README.md` is always the home page.

= How do I restrict access? =

Go to **Docsify Docs** in the admin menu, enable **Enable Restriction**, and check the roles that should have access. Logged-out users are redirected to the WordPress login page automatically.

= Does this plugin require Composer? =

No. The Composer autoloader was replaced with a built-in PSR-4 loader. No `composer install` is needed.

= Can I use a custom theme? =

Yes. Swap the bundled Vue theme enqueued in `src/templates/docsify-docs.php` for another Docsify theme, or enqueue your own stylesheet.

= Will my documentation survive a plugin update? =

Yes. Documentation files are stored in `wp-content/uploads/docsify-docs/`, which is never touched by plugin updates.

= Can restricted documentation still be read by URL? =

Not with **Protect Files** enabled, which is the default. Docsify fetches every `.md` over the network, so a documentation folder reachable by URL is readable by anyone who guesses the path. With the setting on, files are served by WordPress through `/docsify-docs-files/…` under the same role rule as the page, and a deny rule is written next to them so the folder itself answers nothing.

The endpoint is a rewrite rule, so it needs pretty permalinks. On plain permalinks the setting reports itself as inactive and files fall back to direct URLs. On nginx, or any server that ignores `.htaccess`, add the equivalent deny for the documentation folder to your server configuration.

= Can I keep the documentation somewhere else? =

Yes. Define `DOCSIFYDOCS_DOCS_DIR` in `wp-config.php` with an absolute path, which is how you keep the Markdown files in a folder versioned with your project:

`define( 'DOCSIFYDOCS_DOCS_DIR', __DIR__ . '/wp-content/plugins/docsify-docs/src/docs' );`

With **Protect Files** enabled the folder does not have to be reachable by URL at all, so it can live outside the web root. The `docsify_docs_dir` filter does the same thing from PHP, and `docsify_docs_base_path` overrides the URL Docsify reads from.

== Screenshots ==

1. Docsify Docs documentation page rendered inside WordPress.
2. Access denied page shown to unauthorized users.
3. Admin settings panel under the Docsify Docs menu.

== Changelog ==

= 3.6.1 =
* The documentation fonts now ship with the plugin instead of loading from Google Fonts, so a Content Security Policy no longer blocks them.

= 3.6.0 =
* The guide progress bar is now split into one part per step, and the part of the step being played fills over the delay. Where the guide is and how much of the step is left are read at a glance, which a single continuous bar only hinted at.

= 3.5.0 =
* The Play button now shows how much of the step is left: the progress bar crawls to the next mark over the delay instead of standing still and jumping when the step changes. Under `prefers-reduced-motion` it keeps jumping.
* Shortened the Play delay from 3.2 to 2 seconds, so a walkthrough no longer feels stalled between steps.

= 3.4.3 =
* Fixed a guide screenshot growing to several screens of scrolling. A tall crop — the right column of an editor, a form column — was stretched to the width of the content area, so a 208x892 capture rendered 3900 pixels tall. Screenshots now keep their own size up to the column width and stop at 520 pixels tall; nothing is upscaled, so a small crop is sharp instead of blurry, and the lightbox still answers the detail.

= 3.4.2 =
* Fixed a documentation page vanishing from the menu. Every sidebar group was configured to start collapsed, so opening a page outside a group — the home page, for instance — hid the pages inside it, and a group written as plain text shows no indicator that it can be opened. First-level groups now start open, which is Docsify's own default; deeper groups still collapse.

= 3.4.1 =
* Fixed a logo picked from the Media Library disappearing from the sidebar when the file is an SVG carrying only a `viewBox`, with no width or height of its own: the image collapsed to zero height. The logo now has a fixed height and keeps its aspect ratio, and a square logo is still not stretched.

= 3.4.0 =
* Added step-by-step guides: a numbered list preceded by `<!-- docsify-guide -->` is rendered as a walkthrough — one step at a time, with Previous/Next, a Play button that advances on its own, the arrow keys, and a "See every step" view. The marker is an HTML comment and the steps stay an ordinary Markdown list, so the page still reads as a numbered list with images when the script is unavailable, and the search plugin indexes every step.
* Guide screenshots open enlarged in a dialog, closed with Escape or a click, reachable by keyboard.
* Widened the documentation content area from 72 characters to 1100 pixels, so a screenshot is readable without opening it. It still shrinks with the viewport.

= 3.3.1 =
* Fixed the documentation page created from the settings screen reporting success when WordPress refused the insert. The error was never returned, so the failure passed silently and the settings screen pointed at a page that does not exist.
* Fixed a fatal error risk on the settings screen: the Access Control and Appearance sections were registered without a render callback.
* Added an automated pipeline running on every pull request, checking syntax against PHP 7.4 to 8.4, the WordPress Coding Standards, static analysis and the consistency of the version headers.

= 3.3.0 =
* Fixed the default documentation directory: with `DOCSIFYDOCS_DOCS_DIR` undefined the files are now read from the plugin's own `src/docs` folder instead of the uploads directory, so documentation versioned with the project is served as is. Set `DOCSIFYDOCS_DOCS_DIR` to the uploads path to keep the previous behaviour.

= 3.2.0 =
* Added interactive API documentation with Swagger UI, bundled with the plugin. A Markdown page holding a link named `swagger` renders the specification it points at, so an API reference lives beside the rest of the documentation.
* OpenAPI files in JSON, YAML and YML are served by the file endpoint, so a specification stays behind the same access rule as the documentation.
* The API reference follows the theme color from the settings and the typography of the documentation, instead of the stock Swagger look.
* Updated the README with the current admin screen, project structure and screenshots.

= 3.1.1 =
* Fixed a blank documentation page on sites running a page cache or a JavaScript optimizer. WP Rocket's "delay JavaScript execution" rewrites every script tag to a type the browser will not run, which docsify cannot survive, and a cached copy of the page is served before WordPress loads. The page now declares the constants WP Rocket, W3 Total Cache and LiteSpeed Cache check before touching a response.
* Fixed a collapsed sidebar section disappearing from the menu. Docsify hides every non-anchor child of a collapsed item, which took plain-text group headings down with the list and left no way to reopen them.
* Fixed the admin bar printing unstyled over the documentation. The page drops the theme styles, so the bar had no stylesheet, and no room either, since docsify pins its layout to the top of the viewport. It is now hidden on the documentation page only.

= 3.1.0 =
* Added **Protect Files**: Markdown files are served by WordPress under the same role rule as the page, so restricted documentation is no longer readable by direct URL. Enabled by default.
* Added a deny rule written next to the documentation files while protection is on, so the folder itself is not served by Apache.
* Added `DOCSIFYDOCS_DOCS_DIR` to point the documentation at any absolute path, which allows keeping the Markdown files under version control with the project. Also available as the `docsify_docs_dir` filter, with `docsify_docs_base_path` for the URL.
* The `DOCSIFYDOCS_DEFAULT_*` constants can now be set from `wp-config.php`, so a project can keep its own defaults under version control instead of only in the database.
* The access rule now lives in one place and is shared by the page template and the file endpoint.
* An empty allowed-roles list now shows the access-denied page instead of redirecting to the home page.

= 3.0.0 =
* **Breaking:** the plugin folder and text domain are now `docsify-docs` (was `wp-docsify`). Settings, documentation files, and page templates are migrated automatically on the first admin page load.
* **Breaking:** the sample documentation is pt_BR only and lives directly in `wp-content/uploads/docsify-docs/`. The per-locale subfolder is gone; an existing `pt_BR/` folder is moved up automatically and an `en_US/` folder is left untouched.
* Added admin settings page (access control, logo, theme color, repository URL).
* Added logo selection from the Media Library, replacing the hardcoded `_media/logo.svg`.
* Sample documentation is now pt_BR only and lives directly in `wp-content/uploads/docsify-docs/` (no locale subfolder). Existing locale folders are migrated automatically.
* Bundled every Docsify asset with the plugin — no more jsDelivr/unpkg requests.
* Added View page button and inline editing of the documentation page URL.
* Theme and block styles are no longer loaded on the documentation page, so they cannot break the Docsify layout. Opt out with the `docsify_docs_isolate_styles` filter.
* Documentation files now stored in `wp-content/uploads/docsify-docs/` (survives plugin updates).
* Replaced Composer autoloader with built-in PSR-4 autoloader.
* Scripts and styles now registered via `wp_enqueue_script` / `wp_enqueue_style`.
* Added `wp_head()` and `wp_footer()` to all templates.
* Fixed text domain to match plugin slug (`docsify-docs`).
* Added `Requires at least`, `Requires PHP`, and `Domain Path` headers.
* Sample documentation copied to uploads directory on activation.
* Options cleaned up on plugin uninstall.
* Removed Google Fonts CDN from access-denied template (uses system fonts).
* Added direct-access guards to all PHP class files.

= 2.0.0 =
* Multi-language support and role-based access control.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 3.4.0 =
Adds step-by-step guides. Existing documentation renders as before, except for the wider content area, which affects every page.

= 3.2.0 =
Adds Swagger UI for API documentation. Nothing changes on existing documentation: the API reference only appears on pages that link to an OpenAPI file.

= 3.1.1 =
Recommended for anyone on 3.1.0, and required if your site runs a page cache or a JavaScript optimizer: the documentation page could render blank. After updating, purge your cache once so the stored copy of the page is dropped.

= 3.1.0 =
Protected file delivery is on after the update. Documentation keeps rendering, but the `.md` files stop answering on their old URLs. If your permalinks are set to Plain, the endpoint stays inactive and nothing changes.

= 3.0.0 =
Delete the old `wp-docsify` plugin before activating this one; docs in `wp-content/uploads/` are untouched. Settings and files migrate automatically. Docs are pt_BR only now: a `pt_BR/` folder moves up one level, an `en_US/` folder is kept but no longer rendered.
