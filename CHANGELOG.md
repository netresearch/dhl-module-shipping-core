# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## 2.4.0

Magento 2.4.7 compatibility release

### Added

- Support for Magento 2.4.7

### Changed

- Use escaper instead of block for escaping in templates

### Removed

- Support for PHP 7.x
- Support for PHP 8.1

## 2.3.0

### Added

- Link to Marketplace review page in module configuration.

## 2.2.0

Magento 2.4.4 compatibility release

### Added

- Support for Magento 2.4.4

### Removed

- Support for PHP 7.1

## 2.1.0

### Added

- Trigger settings validation in config infobox.

### Changed

- Establish compatibility with shipping core 2.7.
- Display metapackage version number in config infobox.

## 2.0.0

### Changed

- Migrate to shipping framework v2.

## 1.1.6

### Fixed

- Prevent `Call to a member function getProduct() on bool` error, reported by
  [DavidLambauer](https://github.com/DavidLambauer) via PR [#2](https://github.com/netresearch/module-shipping-core/pull/2).

## 1.1.5

### Fixed

- Prevent broken order detail page for countries with optional postal code, reported by
  [HenKun](https://github.com/HenKun) via issue [#8](https://github.com/netresearch/dhl-module-shipping-core/issues/8).
- Consider selected scope when rendering _My Own Packages_ config field, reported by
  [okolya](https://github.com/okolya) via issue [#7](https://github.com/netresearch/dhl-module-shipping-core/issues/7).
- Update split street (name and number) when shipping address gets updated. 

## 1.1.4

### Changed

- Allow carrier modules to apply custom logic on the street splitting algorithm.

## 1.1.3

### Fixed

- Prevent database error when adding an item to cart via the _Recently Ordered_ widget.
- Add shipping and tracking information to shipment confirmation email during bulk action.
- Set HS code validation to max. 10 digits.

## 1.1.2

### Fixed

- Prevent ambiguous database column error during cron shipment creation.

## 1.1.1

### Fixed

- Display images in checkout when theme_id is not configured.

## 1.1.0

Magento 2.4 compatibility release

### Added

- Support for Magento 2.4

### Removed

- Support for Magento 2.2

### Fixed

- Consider _Email Copy of Shipment_ checkbox on _New Shipment_ page

## 1.0.1

Bug fixes & improvements release

### Changed

- Migrate location finder to new mapbox tiles api
- Load order export data in webapi scope only
- Improve translations
- Improve bulk processing performance
- Improve shipping setting dependency rules calculation

### Fixed

- Fix updating split street (DHL recipient street) when shipping address gets edited in admin panel

## 1.0.0

Initial release
