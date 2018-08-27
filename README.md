# SilverStripe Elemental Site Search

## Introduction

This module adds a basic site search to websites using the [silverstripe elemental](https://github.com/dnadesign/silverstripe-elemental) block module.

## Requirements

* SilverStripe Elemental ^3.0

## Installation

```
composer require eniagroup/silverstripe-elemental-site-search
```

## Alternative search options

The Elemental module comes with an indexer for Solr (via the
[silverstripe-fulltextsearch module](https://github.com/silverstripe/silverstripe-fulltextsearch)).

For information on configuring Solr please see [the fulltextsearch documentation](https://github.com/silverstripe/silverstripe-fulltextsearch).

## Versioning

This library follows [Semver](http://semver.org). According to Semver, you will be able to upgrade to any minor or patch version of this library without any breaking changes to the public API. Semver also requires that we clearly define the public API for this library.

All methods, with `public` visibility, are part of the public API. All other methods are not part of the public API. Where possible, we'll try to keep `protected` methods backwards-compatible in minor/patch versions, but if you're overriding methods then please test your work before upgrading.

## Reporting Issues

Please [create an issue](https://github.com/eniagroup/silverstripe-elemental-site-search/issues) for any bugs you've found, or features you're missing.
