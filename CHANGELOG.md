# Injader changelog

## 2.4.6 (currently on master)

Note: You must be on version 2.4.4 or above to upgrade to 2.4.6.

* New: Simplified Control Panel layout
* Bug: Fixed an issue where the custom order field wasn't getting set, causing a database error
* Bug: Fixed an issue with the default timezone not being set
* Guides: Created new install guide within the download file, under /guides/
* Maintenance: Fixed several notices in the installer
* Maintenance: Added support for a /private/ folder for storing files outside of Git but within your project
* Maintenance: Updated .gitignore
* Maintenance: Removed unreleased interface changes (images and CSS)
* Maintenance: Cleaned up old version upgrade code. Upgrades prior to 2.4.4 are no longer supported.

## 2.4.5

* New: Certain file types are now restricted when uploading files.
* Fixed notices in PHP 5.3 relating to the split() function.
* Fixed undefined variable notice in View.php.
* Removed SQL query screen.

## 2.4.4.P2

* Fixed XSS exploits

## 2.4.4.P1

* Fixed XSS exploits
