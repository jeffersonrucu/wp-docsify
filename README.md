<p align="center">
  <a href="https://docsify.js.org">
    <img alt="docsify" src="https://docsify.js.org/_media/icon.svg">
  </a>
</p>

<p align="center">
  A magical documentation site generator inside wordpress.
</p>

**Docsify Docs** is a WordPress plugin that allows you to create and manage documentation using [Docsify](https://docsify.js.org/), leveraging `.md` files directly within your project. It's ideal for technical projects, user manuals, or any kind of versioned technical documentation.

![image](https://github.com/user-attachments/assets/00a41df7-1b7b-4987-80b7-6fbea95e2070)

![image](https://github.com/user-attachments/assets/ef471225-3e9b-4690-81e1-2c201924685a)

## 📁 Project Structure

```
docsify-docs/
├── src/
│   ├── Access.php                    # Who may read the documentation
│   ├── Docs.php                      # Where the .md files live and how they are reached
│   ├── FileServer.php                # Serves the .md files under the access rule
│   ├── Hardening.php                 # Deny rule written next to the .md files
│   ├── assets/vendor/                # Bundled Docsify, Mermaid and D3 — no CDN
│   ├── docs/                         # Sample Markdown copied to the docs folder on activation
│   └── templates/docsify-docs.php    # Template file responsible for rendering Docsify
```

## 🧩 Features

- Direct integration of Docsify into WordPress
- Reads `.md` files from `wp-content/uploads/docsify-docs/`, or from any path you point it at
- Access control that covers the files, not only the page
- Automatic rendering via a custom template
- Simple and database-independent

## 🛠️ Installation

1. Clone this repository or place the plugin in your WordPress plugins directory:

```bash
wp-content/plugins/docsify-docs/
```

2. Activate the plugin through the WordPress admin panel.

3. Create a page in WordPress and select the **"Docsify Docs"** template, or generate it from the **Docsify Docs** admin menu.

## ✍️ How to Use

1. Add your documentation `.md` files inside the directory:

```
wp-content/uploads/docsify-docs/
```

2. The `README.md` file will be used as the documentation's home page.

3. Customize Docsify behavior (menus, themes, etc.) directly in the `src/templates/docsify-docs.php` file.

## 🔒 Access Control

Restriction is set from **Docsify Docs > Settings**: pick the roles allowed to read the documentation, and logged-out visitors are sent to the WordPress login page.

Docsify fetches every `.md` over the network, so restricting the page is not enough on its own — a documentation folder reachable by URL is readable by anyone who guesses the path. **Protect Files**, enabled by default, closes that gap:

- files are served by WordPress through `/docsify-docs-files/…` under the same role rule as the page;
- a deny rule is written next to the files so the folder itself answers nothing;
- only a fixed list of extensions is handed out, and paths that escape the documentation folder are refused.

The endpoint is a rewrite rule, so it needs pretty permalinks. On plain permalinks the setting reports itself as inactive and the files fall back to direct URLs. On nginx, or any server that ignores `.htaccess`, add the equivalent deny for the documentation folder to your server configuration.

## 📂 Keeping the Docs Under Version Control

Point the plugin at any absolute path with `DOCSIFYDOCS_DOCS_DIR` in `wp-config.php`:

```php
define( 'DOCSIFYDOCS_DOCS_DIR', __DIR__ . '/wp-content/plugins/docsify-docs/src/docs' );
```

With **Protect Files** enabled the folder never has to be reachable by URL, so it can also live outside the web root.

The defaults the plugin starts from can be set the same way, which keeps a project's identity in the repository rather than only in the database:

```php
define( 'DOCSIFYDOCS_DEFAULT_THEME_COLOR', '#004f9f' );
define( 'DOCSIFYDOCS_DEFAULT_ALLOWED_ROLES', [ 'administrator', 'editor' ] );
define( 'DOCSIFYDOCS_DEFAULT_IS_RESTRICTED', true );
define( 'DOCSIFYDOCS_DEFAULT_PROTECT_FILES', true );
```

## 🪝 Filters

| Filter | Purpose |
|---|---|
| `docsify_docs_dir` | Absolute path of the folder holding the `.md` files |
| `docsify_docs_base_path` | URL Docsify prepends to every file it fetches |
| `docsify_docs_isolate_styles` | Return `false` to keep theme and block styles on the page |
| `docsify_docs_kept_styles` | Style handles allowed on the documentation page |

## ✅ Example

```markdown
# Welcome to Docsify Docs

This is the initial documentation.

## Installation

Follow the steps to install and configure the plugin.
```

---

Developed by Jefferson Oliveira using the https://docsify.js.org library 🧑‍💻
