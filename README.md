# Reference Block Plugin for Kirby

This plugin allows you to reference other blocks.
Thus, you can easily repeat a block in multiple places on your
page while being able to update it in one central place.

## Getting started

Use one of the following methods to install & use `tearoom1/kirby-block-reference`:


### Git submodule

If you know your way around Git, you can download this plugin as a [submodule](https://github.com/blog/2104-working-with-submodules):

```text
git submodule add https://github.com/tearoom1/kirby-block-reference.git site/plugins/kirby-block-reference
```


### Composer

```text
composer require tearoom1/kirby-block-reference
```


### Clone or download

1. Clone or download this repository from github: https://github.com/tearoom1/kirby-block-reference.git
2. Unzip / Move the folder to `site/plugins`.

## Usage

Use the block by adding it to you blueprints fieldsets if they are defined:

```yaml
fieldsets:
  - reference
```

## License

This plugin is licensed under the [MIT License](LICENSE)

## Credits

- Developed by Mathis Koblin

[!["Buy Me A Coffee"](https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png)](https://coff.ee/tearoom1)
