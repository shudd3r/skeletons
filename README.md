# Shudd3r/Skeletons
[![Latest Stable Version](https://poser.pugx.org/shudd3r/skeletons/version)](https://packagist.org/packages/shudd3r/skeletons)
[![Build status](https://github.com/shudd3r/skeletons/workflows/build/badge.svg)](https://github.com/shudd3r/skeletons/actions)
[![Coverage status](https://coveralls.io/repos/github/shudd3r/skeletons/badge.svg?branch=develop)](https://coveralls.io/github/shudd3r/skeletons?branch=develop)
[![PHP version](https://img.shields.io/packagist/php-v/shudd3r/skeletons.svg)](https://packagist.org/packages/shudd3r/skeletons)
[![LICENSE](https://img.shields.io/github/license/shudd3r/skeletons.svg?color=blue)](LICENSE)
### Template engine for package skeletons

Skeleton packages are used to maintain **consistent structure**
of document (license, readme etc.) and dev environment files
**across multiple packages**. This library allows building skeleton
package scripts capable of:
- Generating package skeleton from template files
- Verifying synchronization of existing project with chosen
  template as a part of [Continuous Integration](https://en.wikipedia.org/wiki/Continuous_integration) process
- Updating template placeholder values used in existing package files

#### Scripting features:
- Template placeholders can be chosen by skeleton creators.
- Each placeholder is assigned to `Replacement` abstraction that manages validation,
  providing & resolving placeholder's (default) value.
- Replacement may also provide subtypes for defined placeholder (e.g. escaped slashes for `{namespace}`).
- `GenericReplacement` for simple replacements and fluent builder interface for easier instantiation.
- Placeholders for `{original.content}` with optional default mockup value.
- Possibility to customize template handling for individual files
  through `Template` abstraction.
- Synchronization that allows regenerating missing & divergent files.
- Safe file operations - overwritten files are copied into backup directory.
- Filename extension directives that allow:
  - including (deactivating) files that could affect deployed skeleton files (e.g. `.gitignore`),
  - handling "local" files, that are not (or cannot be) part of published package,
  - using initial "mockup" files that can be removed or developed without breaking skeleton synchronization,
  - automatic adding/removing dummy files that ensure directory existence (`.gitkeep`)

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

#### Executable script file
Entire script that uses this library might look like
attached [docs/script-example](docs/script-example) file.

##### Setup steps:
- Instantiate input arguments and application:
  ```php
  namespace Shudd3r\Skeletons;
  
  use Shudd3r\Skeletons\Environment\Files\Directory\LocalDirectory;
  
  $args = new InputArgs($argv);
  
  $package  = new LocalDirectory(get_cwd());
  $template = new LocalDirectory(__DIR__ . '/template');
  $app      = new Application($package, $template);
  ```

- Add [`Replacement`](src/Replacements/Replacement.php) definitions
  for placeholders used in templates:
  ```php
  use Shudd3r\Skeletons\Replacements\Replacement;
  
  $app->replacement('package.name')->add(new Replacement\PackageName());
  $app->replacement('repository.name')->add(new Replacement\RepositoryName('package.name'));
  $app->replacement('package.description')->add(new Replacement\PackageDescription('package.name'));
  $app->replacement('namespace.src')->add(new Replacement\SrcNamespace('package.name'));
  ```
  Simple replacement definitions don't need to be implemented - [`GenericReplacement`](src/Replacements/Replacement/GenericReplacement.php)
  can be used:
  ```php
  use Shudd3r\Skeletons\Replacements\Replacement\GenericReplacement;
  
  $default  = fn () => 'default@example.com';
  $validate = fn (string $value) => $value === filter_var($value, FILTER_VALIDATE_EMAIL);
  
  $app->replacement('author.email')
      ->add(new GenericReplacement($default, $validate, null, 'Your email address', 'email'));
  ```
  It can also be built using fluent builder invoked with `build()` method:
  ```php
  $app->replacement('author.email')
      ->build(fn () => 'default@example.com')
      ->argumentName('email')
      ->inputPrompt('Your email address')
      ->validate(fn (string $value) => $value === filter_var($value, FILTER_VALIDATE_EMAIL));
  ```

- Setup custom [`Templates\Template`](src/Templates/Template.php)
  instantiation for selected template files<sup>*</sup>:
  ```php
  use Shudd3r\Skeletons\Templates\Contents;
  use Shudd3r\Skeletons\Templates\Template;
  
  $app->template('composer.json')->createWith(
      fn (Contents $contents) => new Template\MergedJsonTemplate(
          new Template\BasicTemplate($contents->template()),
          $contents->package(),
          $args->command() === 'update'
      )
  );
  ```
  <sup>*Template that defines dynamic keys for `MergedJsonTemplate`
  (in case of `composer.json` it would usually be `namespace` placeholder)
  requires different merging algorithm for updates. If template doesn't use
  dynamic keys, boolean parameter is not required.</sup>

- Run application with `InputArgs`:
  ```php
  $exitCode = $app->run($args);
  exit($exitCode);
  ```

### Command line usage
```
vendor\bin\script-name <command> [<options>] [<argument>=<value>]
```
Available `<command>` values:
- `init`: generate file structure from skeleton template (with backup on overwrite)
- `check`: verify project synchronization with used skeleton
- `update`: change current placeholder values (synchronized package required)
- `sync`: generate missing & mismatched skeleton files (with backup on overwrite)
- `help`: display help similar to this section

Application `<options>`:
- `-i`, `--interactive`: allows providing placeholder values for `init` or `update`
  command via interactive shell. When no `<argument>=<value>` is provided interactive
  mode is implicitly switched on.
- `-l`, `--local`: may be used to include files that are not deployed to remote
  repository if skeleton defines them (like git hooks or IDE settings). This option
  may be used for all file operations - initialization, validation, synchronization
  and updates.

Available `<argument>` names depend on placeholders configured in application.
Currently, built-in placeholders can receive their values from following arguments:
- `package`: package name (Packagist)
- `repo`: remote (GitHub) repository name
- `desc`: package description
- `ns`: project's main namespace

Values that contain spaces should be surrounded with double quotes.
For example following command for [script-example](docs/script-example)
would update package description:
```bash
vendor/bin/script-example update desc="New package description"
```
When both `--interactive` option and placeholder arguments are provided,
valid argument values will become default for empty input.

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
- `.sk_dummy` - similar to init files, but serving as temporary
  directory placeholder (usually `.gitkeeep`). Can be removed only
  when other files in same directory are present. `init`, `update`
  & `sync` operations will remove redundant or add essential dummy
  files.
  Also note that package with redundant or missing dummy files will
  be **marked as invalid** by `check` operation.
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

##### Original Content placeholder
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
cannot be handled in a simple, text expanding way like, for example, `README.md` file.
This is where custom templates might be used.

As for now this package provides only one file-based template:

##### Merged Json Template
This custom template can handle normalization & merging of generated `.json`
template files with corresponding files existing in developed package like `composer.json`.
In short, skeleton file can define order of keys, but doesn't have to specify their values.
It will also ensure same order of keys in repeating (list) structures.
_Adding key in the wrong position will require synchronization_ which will merge & normalize
skeleton with existing file (and create backup copy).
For details check out [`MergedJsonTemplateTest`](tests/Templates/Template/MergedJsonTemplateTest.php)
file.
