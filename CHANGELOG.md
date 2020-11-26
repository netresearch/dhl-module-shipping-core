# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

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
