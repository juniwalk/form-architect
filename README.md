Form Architect
==============

[![Travis](https://img.shields.io/travis/juniwalk/form-architect.svg?style=flat-square)](https://travis-ci.org/juniwalk/form-architect)
[![GitHub Releases](https://img.shields.io/github/release/juniwalk/form-architect.svg?style=flat-square)](https://github.com/juniwalk/form-architect/releases)
[![Total Donwloads](https://img.shields.io/packagist/dt/juniwalk/form-architect.svg?style=flat-square)](https://packagist.org/packages/juniwalk/form-architect)
[![Code Quality](https://img.shields.io/scrutinizer/g/juniwalk/form-architect.svg?style=flat-square)](https://scrutinizer-ci.com/g/juniwalk/form-architect/)
[![Tests Coverage](https://img.shields.io/scrutinizer/coverage/g/juniwalk/form-architect.svg?style=flat-square)](https://scrutinizer-ci.com/g/juniwalk/form-architect/)
[![License](https://img.shields.io/packagist/l/juniwalk/form-architect.svg?style=flat-square)](https://mit-license.org)

Nette Framework component that lets you dynamically design forms.

Installation & usage
--------------------

Please use composer to install this package.
```
$ composer require juniwalk/form-architect:^1.0
```

register extension in your `config.neon`
```yml
extensions:
  - JuniWalk\FormArchitect\DI\FormArchitectExtension
```

then check [examples](docs/examples) for more information on how to setup the Architect.
