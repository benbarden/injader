# Injader changelog

## 2.5.0 (currently on master)

Note: You must be on version 2.4.4 or above to upgrade to 2.5.0.

* New: Simplified Control Panel layout
* Guides: Created new install guide under /guides/
* Bug: Fixed an issue where the custom order field wasn't getting set, causing a database error
* Bug: Fixed an issue with the default timezone not being set
* Bug: Fixed several notices in the installer
* Upgrades: Upgraded jQuery from 1.3.2 to 1.11.1
* Maintenance: Manage Content no longer uses AJAX pagination
* Maintenance: Added support for a /private/ folder for storing files outside of Git but within your project
* Maintenance: Cleaned up old version upgrade code. Upgrades prior to 2.4.4 are no longer supported.
* Maintenance: Updated .gitignore
* Maintenance: Moved jQuery to a new assets folder
* Deletions: Removed unreleased interface changes (images and CSS)
* Deletions: Removed CodePress (doesn't work in Chrome, and I prefer plaintext)
* Deletions: Removed MagpieRSS (no feed to import anymore - check Github for news)
* Deletions: Removed Tablesorter plugin (doesn't work with latest jQuery)

## 2.4.5

* New: Certain file types are now restricted when uploading files.
* Fixed notices in PHP 5.3 relating to the split() function.
* Fixed undefined variable notice in View.php.
* Removed SQL query screen.

## 2.4.4.P2

* Fixed XSS exploits

## 2.4.4.P1

* Fixed XSS exploits
