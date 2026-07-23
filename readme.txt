=== Dashboard Master - Clean and Custom Dashboard Multisite ===
Contributors: mastermusica
Tags: dashboard, multisite, custom widgets, admin notices, adblocker, clean dashboard
Requires at least: 5.8
Tested up to: 6.5
Stable tag: 9.3
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

# Dashboard Master – Clean and Custom Dashboard Multisite

[cite_start]Dashboard Master is a lightweight, highly focused WordPress plugin designed specifically for Multisite networks[cite: 2074]. [cite_start]It transforms the default, cluttered WordPress admin dashboard into a clean, communication-focused hub[cite: 2075]. [cite_start]By hiding native distractions and aggressive third-party advertisements, it allows network administrators to transmit clear instructions to subsite managers without them getting lost in unnecessary system settings[cite: 2076].

## Key Features

* [cite_start]**Hybrid Widget Management:** Empowers Super Admins to set 2 fixed global widgets mirrored automatically across the entire network, while giving local subsite Admins the freedom to create up to 6 custom local blocks[cite: 2077].
* [cite_start]**Smart AdBlocker (JavaScript Engine):** Employs an intelligent JavaScript scanner that hunts for promotional keywords (like "premium", "upgrade", or "sale") to dynamically hide aggressive upsells from plugins and themes (e.g., Elementor, LearnPress, PMPro)[cite: 2078]. [cite_start]Crucially, it preserves legitimate, functional notices (such as H5P success messages) intact[cite: 2079].
* [cite_start]**X-Ray Mode:** A unique toggle button in the admin bar available exclusively for Admins and Super Admins[cite: 2080]. [cite_start]It instantly reveals all hidden notices and blocked banners for quick auditing, without breaking the layout or the security nonces of the plugins[cite: 2081].
* [cite_start]**Role-Based Visibility:** Local administrators can configure exactly which user roles (Subscribers, Editors, Authors, etc.) are allowed to view specific custom widgets, allowing for highly targeted communication[cite: 2082].
* [cite_start]**Deep White-Labeling:** Aggressively simplifies the user interface by removing all native WordPress dashboard widgets, the "Help" tab, and "Screen Options"[cite: 2083]. [cite_start]It also customizes the admin footer to display a tailored brand signature while hiding the WordPress version[cite: 2084].
* [cite_start]**Safe Media & Embeds:** Automatically corrects YouTube cross-origin iframe errors (Error 153) by injecting strict referrer policies[cite: 2085]. [cite_start]It ensures embedded videos and iframes are fully responsive, safely sanitized, and perfectly aligned[cite: 2086].
* [cite_start]**Internationalization (i18n):** Fully prepared for global distribution with native support for English and Brazilian Portuguese, adhering to strict WordPress.org translation standards[cite: 2087].

## Architecture & Security

[cite_start]Unlike heavy, bloated white-labeling alternatives, Dashboard Master operates as a highly precise "scalpel"[cite: 2088].

* [cite_start]**Scope Isolation:** Aggressive widget removal runs strictly on the index.php dashboard page to prevent functional conflicts with other complex plugins[cite: 2089].
* [cite_start]**Rigorous Sanitization:** Uses wp_kses based on user capabilities to safely allow iframes and media embeds through the database while blocking malicious script injections[cite: 2090].
* [cite_start]**Non-Destructive Storage:** Separates data logic cleanly, utilizing update_site_option for global data and update_option for local subsite data[cite: 2091].

## Installation for Multisite

1. [cite_start]Download the .zip file from the repository[cite: 2092].
2. [cite_start]In your WordPress network admin panel, navigate to **My Sites > Network Admin > Plugins**[cite: 2093].
3. [cite_start]Click **Add New** and upload the .zip file[cite: 2094].
4. [cite_start]Once installed, click **Network Activate**[cite: 2095]. [cite_start]The plugin will instantly take effect across all subsites without local admins being able to deactivate it[cite: 2095].
