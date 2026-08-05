=== Docsify Docs ===
Contributors:      jeffersonrucu, studiostg
Tags:              documentation, docsify, markdown, docs, knowledge-base
Requires at least: 5.9
Tested up to:      7.0
Requires PHP:      7.4
Stable tag:        3.0.0
License:           GPL-2.0+
License URI:       https://www.gnu.org/licenses/gpl-2.0.txt

Integrate Docsify documentation into WordPress using a custom page template with role-based access control.

== Description ==

Docsify Docs embeds the [Docsify](https://docsify.js.org) documentation generator into your WordPress site as a custom page template. It renders Markdown (.md) files stored in your uploads directory — no database required.

**Features**

* Custom page template — select "Docsify Docs" on any WordPress page
* Role-based access control configured from the admin panel
* Admin settings panel under the Docsify Docs menu
* Custom logo picked from the Media Library
* Docsify plugins included: full-text search, pagination, copy code, collapsible sidebar, Mermaid diagrams
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

== Screenshots ==

1. Docsify Docs documentation page rendered inside WordPress.
2. Access denied page shown to unauthorized users.
3. Admin settings panel under the Docsify Docs menu.

== Changelog ==

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

= 3.0.0 =
Delete the old `wp-docsify` plugin before activating this one; docs in `wp-content/uploads/` are untouched. Settings and files migrate automatically. Docs are pt_BR only now: a `pt_BR/` folder moves up one level, an `en_US/` folder is kept but no longer rendered.
