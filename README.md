[![Build Status](https://img.shields.io/travis/isfett/php-analyzer/master?style=flat-square)](https://travis-ci.org/isfett/php-analyzer)
[![codecov](https://img.shields.io/codecov/c/github/isfett/php-analyzer?style=flat-square)](https://codecov.io/gh/isfett/php-analyzer)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.1-8892BF.svg?style=flat-square)](https://php.net/)
[![Latest Stable Version](https://poser.pugx.org/isfett/php-analyzer/v/stable)](https://packagist.org/packages/isfett/php-analyzer)
[![Total Downloads](https://poser.pugx.org/isfett/php-analyzer/downloads)](https://packagist.org/packages/isfett/php-analyzer)
# PHP-Analyzer

`php-analyzer` is a tool designed to help you with different problems.

Mostly I found that I wanted to resolve them while doing my job. For details check the documented commands below.

## Installation
Run
```
$ composer global require isfett/php-analyzer
```
or download the latest phar from [this repository](https://github.com/isfett/php-analyzer/releases).

## Usage
Each command has its own documentation; you can find those in the 'docs' subfolder.

## Information
This tool uses [a php parser written in php](https://github.com/nikic/PHP-Parser), ignoring different code-style or whitespaces. 

## Commands
- [Magic Number Detector](docs/MagicNumberDetector.md) This command helps to find Magic Numbers in your source code. You can also specify to check only case's inside switch's or default parameter values. You can find many examples in the linked command documentation.
- [Magic String Detector](docs/MagicStringDetector.md) This command helps to find Magic Strings in your source code. You can find many examples in the linked command documentation.
- [Most Used Conditions](docs/MostUsedConditions.md) This command helps to check which conditions are used the most in your project. Just want to check if's? Or ternaries? No problem! You can also use the command to split by logical operators, or split isset functions for each parameter, including post-processing your conditions, flip-checking, etc. You can find many examples in the linked command documentation.

## Planned
- Find Duplicate Code (ignoring codestyle, just checking statements)
- Find Classes/Functions with the highest cyclomatic complexity
- Halstead-Metrics
- Most Used Constants (Order by name or value)

## Contributing
Please see [CONTRIBUTING.md](CONTRIBUTING.md) for more information.

## License
The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
