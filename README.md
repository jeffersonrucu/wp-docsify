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
│   ├── assets/vendor/                # Bundled Docsify, Mermaid and D3 — no CDN
│   ├── docs/                         # Sample Markdown copied to uploads on activation
│   └── templates/docsify-docs.php    # Template file responsible for rendering Docsify
```

## 🧩 Features

- Direct integration of Docsify into WordPress
- Reads `.md` files from `wp-content/uploads/docsify-docs/`
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

## ✅ Example

```markdown
# Welcome to Docsify Docs

This is the initial documentation.

## Installation

Follow the steps to install and configure the plugin.
```

---

Developed by Jefferson Oliveira using the https://docsify.js.org library 🧑‍💻
