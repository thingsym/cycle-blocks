# Cycle Blocks

Cycle Blocks plugin is a collection of block for block themes (Full Site Editing).
Of course, it is designed so that it can also be used with classic themes.

You can easily design your site and create content using blocks.

## Blocks

* Page List
* Profile
* Sitemap
* Fontawesome Icons (Required Font Awesome icons resource)

## Installation

1. Download and unzip files. Or install Cycle Blocks plugin using the WordPress plugin installer. In that case, skip 2.
2. Upload "cycle-blocks" to the "/wp-content/plugins/" directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Have fun!

## Compatibility

* WordPress version 6.0 or later
* Gutenberg version 12.0 or later ([Versions in WordPress](https://developer.wordpress.org/block-editor/contributors/versions-in-wordpress/))

## Required plugins

Cycle Blocks will need other recommended plugins to use icons library as Web Font.

[Font Awesome](https://ja.wordpress.org/plugins/font-awesome/)

## WordPress Plugin Directory

Cycle Blocks is hosted on the WordPress Plugin Directory.

[https://wordpress.org/plugins/cycle-blocks/](https://wordpress.org/plugins/cycle-blocks/)

## Test Matrix

For operation compatibility between PHP version and WordPress version, see below [GitHub Actions](https://github.com/thingsym/cycle-blocks/actions).

## Build development environment

```console
cd /path/to/cycle-blocks

# Install package
npm intall

# Show tasks list
npm run

# Build plugin
npm run build
```

### PHP unit testing with PHPUnit

```console
cd /path/to/cycle-blocks

# Install package
composer intall

# Show tasks list
composer run --list

# Run test
composer run phpunit
```

### Javascript unit testing with Jest

```console
cd /path/to/cycle-blocks

# Install npm package
npm intall

# Run test
npm run test:jest
```

## Contribution

### Patches and Bug Fixes

Small patches and bug reports can be submitted a issue tracker in GitHub. Forking on GitHub is another good way. You can send a pull request.

1. Fork [Cycle Blocks](https://github.com/thingsym/cycle-blocks) from GitHub repository
2. Create a feature branch: git checkout -b my-new-feature
3. Commit your changes: git commit -am 'Add some feature'
4. Push to the branch: git push origin my-new-feature
5. Create new Pull Request

## Changelog

### [1.0.0] - 2022.09.30

* initial release

## License

Licensed under [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html).
