# Shudd3r/Skeletons
[![Latest Stable Version](https://poser.pugx.org/shudd3r/skeletons/version)](https://packagist.org/packages/shudd3r/skeletons)
[![Build status](https://github.com/shudd3r/skeletons/workflows/build/badge.svg)](https://github.com/shudd3r/skeletons/actions)
[![Coverage status](https://coveralls.io/repos/github/shudd3r/skeletons/badge.svg?branch=develop)](https://coveralls.io/github/shudd3r/skeletons?branch=develop)
[![PHP version](https://img.shields.io/packagist/php-v/shudd3r/skeletons.svg)](https://packagist.org/packages/shudd3r/skeletons)
[![LICENSE](https://img.shields.io/github/license/shudd3r/skeletons.svg?color=blue)](LICENSE)
### Template engine for package skeletons

Skeleton packages are used to maintain **consistent structure**
of document (license, readme etc.) and dev environment files
**across multiple packages**. This library allows building
skeleton package scripts with following features:
- Generating package skeleton from template
- Verifying synchronization of existing project with chosen
  template (as a part of [_CI_](https://en.wikipedia.org/wiki/Continuous_integration) process)
- Updating template placeholders in existing project

### Basic Usage
> :warning: See [**shudd3r/skeleton-example**](https://github.com/shudd3r/skeleton-example)
> repository if you got the general idea of using skeleton scripts
> and prefer learning basics from "default example" that uses built-in
> features only and simplified command parsing.

Neither applications nor libraries will use this package directly,
but as a command line tool of the skeleton package they were built
with (dev dependency). To avoid conflicts it is released as a
standalone package that doesn't use any production dependencies,
and php version compatibility is the only limitation.

#### Creating skeleton package
Simplified steps with example script and arbitrary chosen names.
Following sections will cover template & script files in more details.
- Install this library as a dependency of your skeleton package
  using [Composer](https://getcomposer.org/) command:
  ```bash
  composer require shudd3r/skeletons
  ```
- Add `template` directory with your skeleton [template files](#template-files)
- Add CLI executable `my-skeleton` [script file](#executable-script-file)
- Add `"bin"` directive to composer.json pointing to that script
  ```json
  {
    "bin": ["my-skeleton"]
  }
  ```
- Publish skeleton package

#### Building projects using skeleton
Usage of skeleton package should be explained individually in
its README, because commands may depend on how its executable
script was built.

Scripts of different skeletons may vary significantly as
they're highly customizable. The differences may come from
command & options remapping, using only a subset of built-in
replacements, adding custom replacements or introducing
different template behavior for certain skeleton files.

### Executable script file
Entire script that uses this library might look like
attached [docs/script-example](docs/script-example) file.

#### Setup steps
Instantiate application:

```php
namespace Shudd3r\Skeletons;

use Shudd3r\Skeletons\Environment\Files\Directory\LocalDirectory;

$package  = new LocalDirectory(get_cwd());
$template = new LocalDirectory(__DIR__ . '/template');
$app      = new Application($package, $template);
```

Add [`Replacement`](src/Replacements/Replacement.php) definitions
for placeholders used in templates:
```php
use Shudd3r\Skeletons\Replacements\Replacement;

$app->replacement('package.name')->add(new Replacement\PackageName());
$app->replacement('repository.name')->add(new Replacement\RepositoryName('package.name'));
$app->replacement('package.description')->add(new Replacement\PackageDescription('package.name'));
$app->replacement('namespace.src')->add(new Replacement\SrcNamespace('package.name'));
```

Define custom [`Templates\Factory`](src/Templates/Factory.php)
objects for selected template files:
```php
use Shudd3r\Skeletons\Templates\Factory;

$app->template('composer.json')->add(new Factory\MergedJsonFactory());
```
Parse command `$args` into main `$command` and `$options` array
and execute application:
```php
$exitCode = $app->run($command, $options);
exit($exitCode);
```

#### Default CLI parameters
Available `$command` values:
- `init`: generate file structure from skeleton template (with backup on overwrite)
- `check`: verify project synchronization with used skeleton
- `update`: change current placeholder values (synchronized package required)
- `synch`: generate missing & mismatched skeleton files (with backup on overwrite)

Application `$options`:
- `-i`, `--interactive`: allows providing (init/update) placeholder values
  via interactive shell
- `--remote`: may be used with `check` or `synch` command to validate/generate
  only skeleton files of deployed package, when not all generated files are
  deployed to remote repository.

Built-in placeholder value options:
- `--package=...`: package name (Packagist)
- `--repo=...`: remote (GitHub) repository name
- `--desc=...`: package description
- `--ns=...`: project's main namespace

For example following command for [script-example](docs/script-example)
would update package description:
```bash
vendor/bin/script-example --desc="New package description" update
```
With both `--interactive` and placeholder options,
command values will become default for empty input.

### Template files
Project file structure controlled by skeleton will
reflect template directory, and placeholders within
its files will be replaced.
Template directory example can be found [here](tests/Fixtures/example-files/template)

#### Directive suffixes
Behavior of some template files can be modified by adding
a suffix to their names:
- `.sk_init` - files generated at initialization only, not verified.
  Usually used for example files.
- `.sk_local` - untracked, local dev environment files. Generated &
  updated, but not verified on remote environments.
- `.sk_file` - deactivates files processed by remote workflows.
  For example `.gitignore` file cannot be safely deployed as a part
  of skeleton, because it could make other skeleton files untracked. 
- `.sk_dir` - similar to `.sk_file` in context of directories.
  For example `.git` directory cannot be deployed. Such directory
  is expected to contain `.sk_local` or `.sk_init` files.

#### Placeholders
Placeholder consists of its name surrounded by curly braces.
Script defines what kind of replacement given placeholder
represents:
```php
$app->replacement('namespace.src')->add(new Replacement\SrcNamespace());
```
Application will replace `{namespace.src}` with given namespace value.

##### Placeholder subtypes
Replacement, beside direct value may define its subtypes.
For example [`SrcNamespace`](src/Replacements/Replacement/SrcNamespace.php)
replacement defined above will also replace `{namespace.src.esc}`
with escaped backslashes (needed for `composer.json` file),
and [`PackageName`](src/Replacements/Replacement/PackageName.php)
covers `{placeholder.title}` subtype, that gives package name
with capitalized segments.

###### Original Content placeholder
`{original.content}` is a special built-in placeholder that
represents places where project specific text might appear.
It's useful especially for README files, where skeleton cannot
dictate its entire content.
Template can also define **initial "prompt" value** for it, and
single template file can use this placeholder multiple times.
For example README file that is expected to contain concrete
sections might be defined as follows:
```markdown
![{package.name} logo]({original.content>>>https://www.example.com/images/default-logo.png<<<original.content})
# {package.name.title}
### {package.desc}
{original.content>>>...Extended description here...<<<original.content}
### Installation
    composer require {package.name}
{original.content}
### Basic Usage
{original.content>>>...<<<original.content}
```

#### Custom Template processing
Some files that change dynamically throughout project lifetime
cannot be handled in a generic way (like README).
This is where custom templates might be used.

##### Merged Json Template
This custom template can handle normalization of `.json` files
like `composer.json`. The way it works cannot be briefly described,
so check out [`MergedJsonTemplateTest`](tests/Templates/Template/MergedJsonTemplateTest.php)
for details.

### TODO features
- [x] `synch` command - generating divergent (with backup) & missing files
- [x] Handling empty directories - allow removing `.gitkeep` when files added
- [x] Handling `.gitattributes` in templates (blocking package import)
- [x] Initial "example files" - removed later, so ignored by validation
- [x] Handling untracked "dev files" like git hooks, IDE setup, etc.
- [ ] `help` command
