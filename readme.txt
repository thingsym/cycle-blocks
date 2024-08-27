=== Cycle Blocks ===

Contributors: thingsym
Link: https://github.com/thingsym/cycle-blocks
Donate link: https://github.com/sponsors/thingsym
Stable tag: 1.1.3
Tested up to: 6.6.1
Requires at least: 6.0.0
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Tags: block, block editor, gutenberg

Cycle Blocks plugin is a collection of block for block themes (Full Site Editing).

== Description ==

Cycle Blocks plugin is a collection of block for block themes (Full Site Editing).
Of course, it is designed so that it can also be used with classic themes.

You can easily design your site and create content using blocks.

= Compatibility =

- WordPress version 6.0 or later
- Gutenberg version 12.0 or later

= Required plugins =

Cycle Blocks will need other recommended plugins to use icons library as Web Font.

[Font Awesome](https://ja.wordpress.org/plugins/font-awesome/)

= Blocks =

- Page List
- Profile
- Sitemap
- Fontawesome Icons (Required Font Awesome icons resource)

= Support =

If you have any trouble, you can use the forums or report bugs.

* Forum: [https://wordpress.org/support/plugin/cycle-blocks/](https://wordpress.org/support/plugin/cycle-blocks/)
* Issues: [https://github.com/thingsym/cycle-blocks/issues](https://github.com/thingsym/cycle-blocks/issues)

= Contribution =

Small patches and bug reports can be submitted a issue tracker in Github. Forking on Github is another good way. You can send a pull request.

Translating a plugin takes a lot of time, effort, and patience. I really appreciate the hard work from these contributors.

If you have created or updated your own language pack, you can send gettext PO and MO files to author. I can bundle it into plugin.

* [VCS - GitHub](https://github.com/thingsym/cycle-blocks)
* [Homepage - WordPress Plugin](https://wordpress.org/plugins/cycle-blocks/)
* [Translate Cycle Blocks into your language.](https://translate.wordpress.org/projects/wp-plugins/cycle-blocks)

You can also contribute by answering issues on the forums.

* Forum: [https://wordpress.org/support/plugin/cycle-blocks/](https://wordpress.org/support/plugin/cycle-blocks/)
* Issues: [https://github.com/thingsym/cycle-blocks/issues](https://github.com/thingsym/cycle-blocks/issues)

= Patches and Bug Fixes =

Forking on Github is another good way. You can send a pull request.

1. Fork [Cycle Blocks](https://github.com/thingsym/cycle-blocks) from GitHub repository
2. Create a feature branch: git checkout -b my-new-feature
3. Commit your changes: git commit -am 'Add some feature'
4. Push to the branch: git push origin my-new-feature
5. Create new Pull Request

= Contribute guidlines =

If you would like to contribute, here are some notes and guidlines.

* All development happens on the **main** branch, so it is always the most up-to-date
* If you are going to be submitting a pull request, please submit your pull request to the **main** branch
* See about [forking](https://help.github.com/articles/fork-a-repo/) and [pull requests](https://help.github.com/articles/using-pull-requests/)

= Test Matrix =

For operation compatibility between PHP version and WordPress version, see below [GitHub Actions](https://github.com/thingsym/cycle-blocks/actions).

== Installation ==

1. Download and unzip files. Or install Cycle Blocks plugin using the WordPress plugin installer. In that case, skip 2.
2. Upload "cycle-blocks" to the "/wp-content/plugins/" directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Have fun!

== Changelog ==

= 1.1.3 - 2023.11.21 =
* tested up to 6.4.1
* fix npm scripts
* update npm dependencies for update-browserslist-db
* fix phpunit.xml config
* phpunit-polyfills bump up
* remove dist/js/blocks

= 1.1.2 - 2023.04.21 =
* tested up to 6.2.0
* fix jest config
* update npm dependencies
* add npm scripts
* fix Undefined variable $post

= 1.1.1 - 2022.12.08 =
* fix test case
* improve code with phpcs
* fix block wrapper
* change variable name
* add classname to block wrapper
* fix __experimentalGetSettings deprecated
* change the image size according to layout or the number of columns
* remove duplicate editor css
* using bem and remove wp-block prefix
* update github actions, Node.js 12 actions are deprecated
* fix undefined variable php notice
* fix composer.json
* fix workflows
* fix compatible with setUp(): void for ci

= 1.1.0 - 2022.10.13 =
* fix phpcs.ruleset.xml
* update composer dependency
* fix phpcs composer scripts
* add PHP CodeSniffer to ci
* improve code with phpcs
* fix conditional expression
* fix scss
* add who prop to UserControl
* add support section and enhance contribution section to README
* fix license

= 1.0.0 - 2022.09.30 =
* initial commit
