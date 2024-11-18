# Contact Form Block Plugin for Kirby Uniform

This plugin implements a contact form block using Martin Zurowietz' [`kirby-uniform`](https://github.com/mzur/kirby-uniform) plugin for Kirby.
The block can be easly added to your blueprints and is fully configurable.

It comes with a simple captcha guard and a spam word guard to prevent spam submissions.
It also has the uniform honeypot and honeytime guards enabled by default.

The panel block allows adjustments of the labels of the form.

## Getting started

Use one of the following methods to install & use `tearoom1/uniform-contact-block`:


### Git submodule

If you know your way around Git, you can download this plugin as a [submodule](https://github.com/blog/2104-working-with-submodules):

```text
git submodule add https://github.com/tearoom1/uniform-contact-block.git site/plugins/uniform-contact-block
```


### Composer

```text
composer require tearoom1/uniform-contact-block
```


### Clone or download

1. Clone or download this repository from github: https://github.com/tearoom1/uniform-contact-block.git
2. Unzip / Move the folder to `site/plugins`.

## Dependencies

- [Kirby](https://getkirby.com)
- [Kirby Uniform](https://github.com/mzur/kirby-uniform)
- [Uniform Simple Captcha](https://codeberg.org/refbw/uniform-simple-captcha)
- [Uniform Spam Words](https://github.com/tearoom1/uniform-spam-words)

> Note: Check the corresponding documentation for further information and required configuration.
Specifically the uniform.honeytime guard from kirby-uniform is used and needs configuration in your `config.php`

## Multi language requirement
This plugin requires a Kirby multi-language setup. It uses routes that expect the language code as the first segment of the URL.
It can easily be stripped down to a single language setup by removing the language code from the routes and a few adjustments.

## Usage

Use the block by adding it to you blueprints fieldsets if they are defined:

```yaml
fieldsets:
  - uniform-contact
```

### Configuration

You may change certain options from your `config.php` globally:

```php
return [
    'tearoom1.uniform-contact-block' => [
        'fromEmail' => 'mail@example.org',
        'toEmail' => 'mail@example.org',
        'fromName' => 'My Name',
    ],
    'uniform.honeytime' => [
        'key' => 'base64:your-key-here',
    ],

];
```
And optional additional configuration for the included plugins. For example:
```php
    'simple-captcha' => [ // https://codeberg.org/refbw/uniform-simple-captcha
        'distort' => false,
        'textColor' => '#57a514',
        'bgColor' => '#fff',
    ],
    'tearoom1.uniform-spam-words' => [
        'spamThreshold' => 8,
        'spamWords' => [
            10 => ['my important spam word'],
        ],
    ]
```

## Styling

The plugin comes with a stylesheet.
It uses the following css variables that can be overwritten in your own stylesheet.

```css
--font-family
--font-size-default
--font-size-big
--color-white
--color-accent
--color-error
--gap
```

## License

This plugin is licensed under the [MIT License](LICENSE), but **using Kirby in production** requires you to [buy a license](https://getkirby.com/buy).
