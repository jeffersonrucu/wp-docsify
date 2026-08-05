# Bundled third-party libraries

WordPress.org does not allow loading assets from external services, so these are
shipped with the plugin. All licenses are GPL-compatible.

| Library | Version | License | Source |
| --- | --- | --- | --- |
| docsify (`docsify.min.js`, `themes/`, `plugins/search.min.js`) | 5.0.0 | MIT | https://www.npmjs.com/package/docsify |
| docsify-pagination | 2.10.1 | MIT | https://www.npmjs.com/package/docsify-pagination |
| docsify-copy-code | 3.0.2 | MIT | https://www.npmjs.com/package/docsify-copy-code |
| docsify-sidebar-collapse | 1.3.5 | MIT | https://www.npmjs.com/package/docsify-sidebar-collapse |
| docsify-mermaid | 2.0.1 | ISC | https://www.npmjs.com/package/docsify-mermaid |
| docsify-mermaid-zoom | 3.0.0 | MIT | https://www.npmjs.com/package/docsify-mermaid-zoom |
| mermaid | 11.16.0 | MIT | https://www.npmjs.com/package/mermaid |
| d3 | 7.9.0 | ISC | https://www.npmjs.com/package/d3 |

Files are taken verbatim from each package's published `dist/`. To update one,
download the same path from `https://cdn.jsdelivr.net/npm/<package>@<version>/`
and bump the version in this table.

Notes:

- docsify 5 moved from `lib/` to `dist/` and split the theme into
  `themes/core.min.css` plus add-ons; `themes/vue.css` is the add-on that keeps
  the v4 look.
- `mermaid.min.js` is the UMD build, which assigns `globalThis.mermaid`. The ESM
  build is not used because it resolves sibling chunks at runtime.
- `d3` is required by docsify-mermaid-zoom, not by mermaid itself.
