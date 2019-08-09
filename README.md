[![Build Status](https://img.shields.io/travis/isfett/php-analyzer/master?style=flat-square)](https://travis-ci.org/isfett/php-analyzer)
[![codecov](https://img.shields.io/codecov/c/github/isfett/php-analyzer?style=flat-square)](https://codecov.io/gh/isfett/php-analyzer)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.1-8892BF.svg?style=flat-square)](https://php.net/)
[![Latest Stable Version](https://poser.pugx.org/isfett/php-analyzer/v/stable)](https://packagist.org/packages/isfett/php-analyzer)
[![Total Downloads](https://poser.pugx.org/isfett/php-analyzer/downloads)](https://packagist.org/packages/isfett/php-analyzer)
# PHP-Analyzer

`php-analyzer` is a tool that aims to help you for different tasks. Mostly I found that I want to resolve them while doing my job. For details check the documented commands below.

## Installation
Run
```
$ composer global require isfett/php-analyzer
```
or download the latest phar from [this repository](https://github.com/isfett/php-analyzer/releases).

## Usage
For usages of the commands, check the documentation of each command

## Commands
- [Most used conditions](docs/MostUsedConditions.md) Helps to check which conditions are used the most in your project. Just want to check if's? Or ternaries? No Problem! You can also split by logical operators, or split isset functions for each parameter. Including post-processing your conditions, flip-checking etc. You can find many examples within the link.

## Planned
- Magic Number Detector
- Magic String Detector
- Find duplicate code (ignoring codestyle, just checking statements)

## Contributing
Please see [CONTRIBUTING.md](CONTRIBUTING.md) for more information.

## License
The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.