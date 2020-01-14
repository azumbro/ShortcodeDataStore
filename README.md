<p align="center">
<img src="shortcode-datastore/assets/ShortcodeDatastoreLogo.png" width="50%;" style="margin: 0 auto;">
</p>
A Wordpress plugin that allows for flexible text or HTML to be bound to shortcodes that can be inserted into pages, posts, or templates. This allows for the same text/HTML to be inserted in multiple places and then managed from one dashboard.

# Installation
- **Option 1**: Download the plugin zip directly from the Wordpress plugin repository [here](). You can then upload the zip file to the ```plugins``` directory of your Wordpress installation and activate it from the plugin menu.
- **Option 2**: Select "Add New" from under "Plugins" on the left sidebar of the Wordpress admin dashboard and then search for "Shortcode Datastore" in the search field.
- Following installation, the plugin must be activated from the "Plugins" page on the Wordpress admin dashboard.

# Usage
- Activating the plugin creates a new plugin menu option in the left sidebar of the Wordpress admin dashboard titled "Shortcode Datastore".
- Clicking the "Shortcode Datastore" button will open the plugin dashboard which will display a list of the currently available shortcodes.
    - Clicking the "Add Shortcode" button will open a new editing window to create a shortcode. 
        - The key selected for the new shortcode must be unique (cannot be used for another shortcode).
        - By default, keys are converted to lower case and spaces are replaced with underscores.
        - The shortcode value can be entered in the standard Wordpress editor either as text or HTML. This value is what will be displayed when the shortcode is used on the site.
    - The shortcode table displays the existing shortcode entries.
        - Clicking the "Edit" button for a row will open an edit window where the shortcode value can be modified.
        - Clicking the "Delete" button for a row will remove the shortcode key and value.
        - The shortcode itself can be copied from the input field and inserted into a page, post, or template (anywhere standard Wordpress shortcodes work).
- Shortcode Datastore also works with Wordpress multisite installations. Shortcodes can be created and accessed from any subsite where the plugin is enabled (network activation is recommended).

# Releases
- Release 1.0.0
    - Initial plugin release