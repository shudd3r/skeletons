# Username/My-Package-Skeleton
[![Latest Stable Version](https://poser.pugx.org/shudd3r/skeletons/version)](https://packagist.org/packages/shudd3r/skeletons)
[![PHP version](https://img.shields.io/packagist/php-v/shudd3r/skeletons.svg)](https://packagist.org/packages/shudd3r/skeletons)
[![LICENSE](https://img.shields.io/github/license/shudd3r/skeletons.svg?color=blue)](../LICENSE)
### Example skeleton package using Shudd3r/Skeletons

Skeleton package built to illustrate how it can be managed by script
that uses **shudd3r/skeletons** library.

> :warning: **Commands below will not work** - it's just a mock up that
> reflects configuration used in example [`composer.json`](composer.json)
> file as if this directory was a standalone package.
> 
> However, you can run the `example-skeleton` script on testing project
> with skeletons package installed by running it from its root directory
> (with composer.json) - the (unix) command would be for example:
> ```bash
> vendor/shudd3r/skeletons/example/example-skeletons check
> ```
> You can also create some `test-skeleton.php` "shortcut" file there with:
> ```php
> include __DIR__ . '/vendor/shudd3r/skeletons/example/example-skeletons';
> ```

### Installation
Install with [Composer](https://getcomposer.org/) as dev dependency of your project:
```bash
composer require --dev username/my-package-skeleton
```

### Basic Usage
- Display help message with usage details (`help` command is optional):
  ```bash
  vendor/bin/example-skeleton help
  ```
- Initialize package with skeleton files providing template
  replacement values through interactive shell:
  ```bash
  vendor/bin/example-skeleton init
  ```
- Validate project's consistency with skeleton (can be added to CI workflow):
  ```bash
  vendor/bin/example-skeleton check
  ```
- Update placeholders through interactive shell or using provided argument:
  ```bash
  vendor/bin/example-skeleton update
  vendor/bin/example-skeleton update ns=UpdatedNamespace\Package
  ```
- Synchronize files with template:
  ```bash
  vendor/bin/example-skeleton sync
  ```
