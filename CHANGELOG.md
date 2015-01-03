# Injader changelog

## 3.0.0

Notes:
To simplify things, several legacy features have been removed in v3. Check the list below for details.
Old themes cannot be migrated to the new theme system. This won't matter if you haven't used Injader before.
It is currently not possible to install Injader in a subfolder.

* New: Revamped Control Panel and navigation
* New: Manual editing of content URLs
* New: Option to use Disqus comments
* New: Themes now use Twig
* New: Introduced Bootstrap for public-facing themes and for the Control Panel
* New: Introduced many new helper functions for themes
* New: Replaced TinyMCE with CKEditor
* New: Ability to theme the Control Panel (work-in-progress)
* Security: Replacing MD5 with BCRYPT
* SEO: Added canonical URL to category and article pages
* SEO: Added prev/next URLs to category pages
* Maintenance: Moved sitemap URL to the Control Panel dashboard
* Maintenance: Removed standard comments - use Disqus instead
* Maintenance: Removed navigation types - areas now have one level only
* Maintenance: Removed ?loggedin=1 from URLs to avoid multiple URLs from being shared
* Maintenance: Removed setting: Allow password changes
* Maintenance: Removed setting: Allow password resets
* Maintenance: Removed setting: Lock system (this will be reworked and added at a later date)
* Maintenance: Removed setting: Feedburner URL
* Maintenance: Removed setting: Favicon (the custom header can be used for this)
* Maintenance: Allowed site description field to be left blank
* Maintenance: Moved setting from Control Panel to config file: Log file row limit
* Maintenance: Moved setting from Control Panel to config file: Control Panel page count
* Maintenance: Cleaned up path constants and removed SystemDirs.php
* Code: Major framework changes and code cleanup (ongoing)
* Code: RSS feeds now use full headers
* Code: Renamed all database tables

## 2.5.0

Note: You must be on version 2.4.4 or above to upgrade to 2.5.0.

* New: Simplified Control Panel layout
* Guides: Created new install guide under /guides/
* Guides: Created new upgrade guide under /guides/
* Bug: Fixed an issue where the custom order field wasn't getting set, causing a database error
* Bug: Fixed an issue with the default timezone not being set
* Bug: Fixed several notices in the installer
* Upgrades: Upgraded jQuery from 1.3.2 to 1.11.1
* Upgrades: Upgraded TinyMCE from 3.0.1 to 3.5.11
* Maintenance: Manage Content no longer uses AJAX pagination
* Maintenance: Moved jQuery and TinyMCE to a new assets folder
* Maintenance: Added support for a /private/ folder for storing files outside of Git but within your project
* Maintenance: Cleaned up old version upgrade code. Upgrades prior to 2.4.4 are no longer supported.
* Maintenance: Updated .gitignore
* Maintenance: Tidied up the installer
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
