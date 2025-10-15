# Bonsai Digital Maintenance Mode

A lightweight WordPress plugin that displays a customisable maintenance page to visitors while allowing authorised users to keep working. It can optionally replace WordPress’ core `wp-content/maintenance.php` during updates.

> **Status:** Stable  
> **Requires:** WordPress 5.8+ • PHP 7.4+ (8.x supported)

---

## Features

- Sends proper **503 Service Unavailable** + `Retry-After`
- Clean, responsive template with **background image**
- **Editable badge** and **header text**
- **WYSIWYG** main content (lists, headings, links)
- **Toggle** to show/hide all on-page content (background-only mode)
- **Basic SEO**: custom page title + meta description (still `noindex, nofollow`)
- Optional override of WordPress’ maintenance screen
- Writes **static HTML snapshot** to `wp-content/maintenance-template.html`
- Translation-ready, escaped/sanitised, minimal footprint

---

## Requirements

- **WordPress:** 5.8 or newer  
- **PHP:** 7.4 or newer (8.0–8.3 tested)  
- **Filesystem:** `wp-content/` must be writable for the static template and optional `maintenance.php` copy

---

## Installation

### Option A — Zip upload
1. Create a folder `bonsai-maintenance/` with `bonsai-maintenance.php` inside.
2. Zip the folder, then go to **Plugins → Add New → Upload Plugin** and upload.
3. Activate **Bonsai Digital Maintenance Mode**.

---

## Changelog

### 1.12 
- Adding Wordpress plugin update functionality

### 1.11
- Toggle **Show Main Content** (background-only mode).  
- SEO: **Page Title** + **Meta Description** options.  

### 1.10
- Editable **Badge Text**.  
- **WYSIWYG** for Main Content.  

### 1.9
- **Background Image** option.  

### 1.8
- Safer hook order; more sanitisation; static template generation; optional core override handling.

---

## Author

**The Bonsai Digital Collective**  
Maintainer: Ben Ervine  

For issues and enhancements, please open a GitHub issue with:  
- Steps to reproduce  
- Environment details (PHP/WP versions)  
- Any relevant logs  