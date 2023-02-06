# Radio Accent - Song History Plugin
This is a plugin created for Wordpress that shows the songs played on a specific day.
For this, we have a API that can add songs to a MySQL database. With this plugin, we retrieve these songs and add them to a page.

You can add the Song History via the following shortcode in Wordpress
    [ra-playlist]

When you've added this, this will call the Plugin and show the songs that are retrieved.

## Database Setup
For this plugin, we use a MySQL database. The following SQL needs to be run to add the tables we need.
import `database.sql` into your MySQL environment to add the tables.

## Configuration
When this step is added, edit the settings in `includes/DB.php` with the correct MySQL info.

## Installation
Upload the folder to your Wordpress setup in the `wp-content/plugins` folder. After that, add the shortcode in a page and you're set.