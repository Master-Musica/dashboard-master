=== Dashboard Master - Clean and Custom Dashboard Multisite ===
Contributors: mastermusica
Tags: dashboard, multisite, custom widgets, admin notices, adblocker, clean dashboard
Requires at least: 5.8
Tested up to: 6.5
Stable tag: 9.3
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Take back control of your WordPress dashboard. Create global and local custom widgets, block annoying admin notices, and clean up your network.

== Description ==

Is your WordPress dashboard cluttered with aggressive upsells, unnecessary ads, and confusing default widgets? **Dashboard Master - Clean and Custom Dashboard Multisite** is the ultimate architectural solution to transform your native WP dashboard into a clean, focused, and powerful communication hub.

Designed specifically for Multisite networks (but fully functional on single sites), this plugin completely wipes out the native dashboard clutter and replaces it with a highly customizable widget system governed by strict access rules.

### 🌟 Core Features

*   **Global Network Widgets:** Super Admins can set up 2 global dashboard widgets that are permanently pinned across all subsites in the network. Perfect for network-wide announcements or onboarding.
*   **Local Subsite Widgets:** Local administrators can create up to 6 custom widgets specifically for their site.
*   **Role-Based Visibility:** *New!* Local admins can restrict who sees each custom widget based on user roles (e.g., show a "Quick Links" widget only to Subscribers, and a "Metrics" widget only to Editors).
*   **Smart AdBlocker for Admin Notices:** Automatically intercepts and hides aggressive promotional banners and upsells from third-party themes and plugins (looking for keywords like "upgrade", "premium", "sale", "seja pro"), while keeping legitimate system success/error messages visible to your team.
*   **Show/Hide:** A powerful toggle button in the top Admin Bar (exclusive to Admins and Super Admins) that instantly reveals all hidden notices for auditing purposes with a single click.
*   **Welcome Panel Eraser:** Permanently destroys the massive native "Welcome to WordPress" panel.
*   **YouTube Embed Fix:** Automatically injects the correct referrer policies to fix YouTube iframe error 153 on the dashboard.

### 🚀 Why Dashboard Master?

Most "white label" plugins are heavy, bloated, and break other plugins by aggressively removing PHP hooks. Dashboard Master acts like a scalpel: it uses intelligent JavaScript and CSS scoping to hide ads without breaking the functional buttons of complex plugins like H5P, WooCommerce, or PMPro. 

It ensures a pristine, distraction-free environment for your students, teachers, and authors, while giving administrators the X-Ray tools they need to monitor the system.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/dashboard-master` directory, or install the plugin directly through the WordPress plugins screen.
2. Activate the plugin. (If using Multisite, "Network Activate" the plugin).
3. Navigate to the new `Dashboard Master` menu in your WordPress admin sidebar.
4. If you are a Super Admin, fill in your Global Blocks.
5. Add your Local Blocks, select which User Roles can see them, and save!

== Frequently Asked Questions ==

= Does it hide ALL admin notices? =
No. Our Smart AdBlocker targets specific promotional CSS classes and scans the text for marketing keywords. Legitimate feedback notices (like "Post saved successfully") will still appear, ensuring your site remains functional.

= I need to read a blocked notice. What do I do? =
Simply click the "Mostrar Avisos" (Show Notices) button with the eye icon located in the top black Admin Bar. This X-Ray mode will temporarily reveal all blocked notices. This button is only visible to Administrators and Super Admins.

= Does it work on standard Single Site WordPress? =
Yes! While built with Multisite architecture in mind, single-site administrators can fully utilize the widget creator and the Smart AdBlocker.

== Screenshots ==

1. The clean and organized Dashboard Master management interface.
2. Role-based visibility selection for local widgets.
3. The Show/Hide Notices Mode button in action on the Admin Bar.


= 1.0 =
* Initial release.
