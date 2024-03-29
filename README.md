# Shudd3r/Skeletons
[![Latest Stable Version](https://poser.pugx.org/shudd3r/skeletons/version)](https://packagist.org/packages/shudd3r/skeletons)
[![Build status](https://github.com/shudd3r/skeletons/workflows/build/badge.svg)](https://github.com/shudd3r/skeletons/actions)
[![Coverage status](https://coveralls.io/repos/github/shudd3r/skeletons/badge.svg?branch=develop)](https://coveralls.io/github/shudd3r/skeletons?branch=develop)
[![PHP version](https://img.shields.io/packagist/php-v/shudd3r/skeletons.svg)](https://packagist.org/packages/shudd3r/skeletons)
[![LICENSE](https://img.shields.io/github/license/shudd3r/skeletons.svg?color=blue)](LICENSE)
### Template engine for package skeletons

**_Feels like you're repeating yourself every time you start a new project?\
This package might help you automate some stuff!_**

Skeleton packages are used to maintain **consistent structure**
of document (`LICENSE`, `README` etc.) & dev environment files (tool configs,
workflows, scripts) across multiple packages & projects.
This library allows building skeleton package scripts capable of:
- Generating file structure for deployed package & local dev
  environment from skeleton files with built-in placeholder replacements
  or customized template processing scripts
- Verifying synchronization of existing project with chosen template
  as a part of [Continuous Integration](https://en.wikipedia.org/wiki/Continuous_integration) process
- Synchronizing mismatched & missing files in existing project built
  with prepared skeleton script and its template files
- Updating template placeholder values used in existing package files

### Basic Usage
Before diving into details you can learn some basics about skeleton
scripts by browsing files (or playing with to some extent) embedded
[skeleton package example](example).

Neither applications nor libraries will use this package directly,
but as a command line tool of the skeleton package they were built
with (dev dependency). To avoid conflicts it is released as a
standalone package that doesn't use any production dependencies,
and php version compatibility is the only limitation.

#### Skeleton package
Here's a list of main steps to create and use skeleton package - following
sections will cover template & script files in more details:
- Install this library as a dependency of your skeleton package
  using [Composer](https://getcomposer.org/) command:
  ```bash
  composer require shudd3r/skeletons
  ```
- Create template directory with your skeleton [template files](#template-files)
- Add CLI executable [script file](#executable-script-file) and `"bin"` directive
  in `composer.json` pointing to that file:
  ```json
  {
    "bin": ["my-skeleton-script"]
  }
  ```
- Publish skeleton package and require it as a **dev dependency** in your projects
- Use skeleton as their desired file structure through [CLI commands](#command-line-usage)

### Executable script file
Entire script that uses this library might look like attached [example-skeleton](example/example-skeleton) file.

##### Scripting features:
- Template placeholders are chosen by skeleton creators.
- Each placeholder is assigned to `Replacement` abstraction that manages user input,
  validation and resolving placeholder's (default) value.
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
- Directories without specified files required by skeleton are managed by `.gitkeep` dummy files.

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
  <sup>*Template that defines dynamic keys for [`MergedJsonTemplate`](src/Templates/Template/MergedJsonTemplate.php)
  (in case of `composer.json` it would usually be `namespace` placeholder)
  requires different merging algorithm for updates. If template doesn't use
  dynamic keys, its third (boolean flag) parameter is not required.</sup>

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
- `init`: generates file structure from skeleton template (with backup on overwrite)
- `check`: verifies project synchronization with used skeleton
- `update`: changes current placeholder values (synchronized package required)
- `sync`: generates missing & mismatched skeleton files (with backup on overwrite)
- `help`: displays help similar to this section

Application `<options>`:
- `-i`, `--interactive`: allows providing placeholder values for `init` or `update`
  command via interactive shell. When no `<argument>=<value>` is provided interactive
  mode is implicitly switched on.
- `-l`, `--local`: may be used to include files not deployed to remote repository
  if skeleton defines them (like git hooks or IDE settings). This option may be used
  for all file operations - initialization, validation, synchronization and updates.
  See `.sk_local` template filename suffix in [Directive suffixes](#directive-suffixes).

Available `<argument>` names depend on placeholders configured in application.
Currently, built-in placeholders can receive their values from following arguments:
- `package`: package name (Packagist)
- `repo`: remote (GitHub) repository name
- `desc`: package description
- `ns`: project's main namespace

Values that contain spaces should be surrounded with double quotes.
For example following command for [example-skeleton](example/example-skeleton)
script would update package description:
```bash
vendor/bin/example-skeleton update desc="New package description"
```
When both `--interactive` option and placeholder arguments are provided,
valid argument values will become default for empty input.

### Template files
Project file structure controlled by skeleton will
reflect template directory, and placeholders within
its files will be replaced.
Check out template directory in [example skeleton package](example/template)

#### Directive suffixes
Behavior of some template files can be modified by adding
a suffix to their names:
- `.sk_init` - files generated at initialization only, not verified.
  Usually used for example files.
- `.sk_local` - untracked, local dev environment files. Generated &
  updated, but not verified on remote environments.
- `.sk_file` - deactivates files processed by remote workflows.
  For example `.gitignore` file cannot be safely deployed as a part
  of skeleton, because it might exclude some of its files.
- `.sk_dir` - similar to `.sk_file` in context of directories.
  For example `.git` directory cannot be deployed. Such directory
  is expected to contain `.sk_local` or `.sk_init` files.

#### Empty directories
Empty directories cannot be committed, but by convention `.gitkeep` dummy
files may be used to enforce directory structure when no files are specified.
Dummy files should be removed when other files in required directory are
present.

Skeleton script will _create essential_ or _remove redundant_ `.gitkeep` files
with `init`, `update` & `sync` command, and package with redundant or missing
dummy files will be **marked as invalid** by `check` operation.
Contents of these files are not validated and [placeholders are not replaced](tests/Fixtures/example-files/package-after-sync/src/.gitkeep).

> In [example template](example/template) `src` and `tests` directories are
required, but on initialization `src/Example.php` and `tests/ExampleTest.php`
files will be created and `.gitkeep` file will be ignored, but when these
files are removed (and no other file is added) `.gitkeep` will become necessary
and `sync` command should be used to keep package compliant with the template.

#### Placeholders
Placeholder consists of its name surrounded by curly braces.
Script defines what kind of replacement given placeholder
represents. For example, application configured the following
way will replace `{namespace.src}` with given namespace value:
```php
$app->replacement('namespace.src')->add(new Replacement\SrcNamespace());
```

##### Placeholder subtypes
Replacement, beside direct value may define its subtypes.
For example [`SrcNamespace`](src/Replacements/Replacement/SrcNamespace.php)
replacement defined above will also replace `{namespace.src.esc}`
with escaped backslashes used in `composer.json` file,
and [`PackageName`](src/Replacements/Replacement/PackageName.php)
has `{*.composer}` subtype that gives normalized packagist
package name in lowercase.

##### Original Content placeholder
`{original.content}` is a special built-in placeholder that
represents places where project specific text might appear.
It's useful especially for README files, where skeleton cannot
dictate its entire content.
Template can also define **initial/default value** for it, and
single template file can use this placeholder multiple times.
For example README file that is expected to contain concrete
sections might be defined as follows:
```markdown
![{package.name} logo]({original.content>>>https://www.example.com/images/mockup-logo.png<<<original.content})
# {package.name}
### {package.desc}
{original.content>>>...Extended description here...<<<original.content}
### Installation
    composer require {package.name.composer}
{original.content}
### Basic Usage
{original.content>>>...<<<original.content}
```

#### Custom Template processing
Some files that change dynamically throughout project lifetime cannot be
handled in a simple, text expanding way like, for example,`README.md`.
This is where custom [templates](src/Templates/Template.php) might be used.

This package comes with [`Merged Json Template`](src/Templates/Template/MergedJsonTemplate.php)
as the only one file-based template.

##### Merged Json Template
This custom template can handle normalization & merging of generated `.json`
template files with corresponding files existing in developed package like `composer.json`.
In short, skeleton file can define order of keys, but doesn't have to specify their values.
It will also ensure same order of keys in repeating (list) structures.
_Adding key in the wrong position will require synchronization_ which will merge & normalize
skeleton with existing file (and create backup copy).
For details check out [`MergedJsonTemplateTest`](tests/Templates/Template/MergedJsonTemplateTest.php)
file.
