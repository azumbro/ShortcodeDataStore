![ShortcodeDatastore Logo](shortcode-datastore/assets/ShortcodeDatastoreLogo.png)

A Wordpress plugin that allows for flexible text or HTML to be bound to shortcodes that can be inserted into pages, posts, or templates. This allows for the same text/HTML to be inserted in multiple places and then managed from one dashboard.

# Usage
- Activating the plugin created a new plugin menu option in the left menu of the Wordpress admin dashboard.
- Clicking the "Shortcode Datastore" button will open the plugin dashboard which will display a list of the currently available shortcodes.
- Clicking the "Add Shortcode" button will open a new editing window to create a shortcode. 
    - The key selected for the new shortcode must be unique (cannot be used for another shortcode).
    - By default, keys are converted to lower case and spaces are replaced with underscores.
    - The shortcode value can be entered in the standard Wordpress editor either as text or HTML. This is what will be displayed when the shortcode is used.
- The shortcode table displays the key, value, and shortcode.
    - Clicking the "Edit" button for a row will open an edit window where the shortcode value can be edited.
    - Clicking the "Delte" button for a row will remove the shortcode key and value.
    - The shortcode itself can be copied from the input field and inserted into a page, post, or template (anywhere standard Wordpress shortcodes work!).
- Shortcode Datastore also works with Wordpress multisite. Shortcodes can be created and access from any subsite where the plugin is enabled (network activation is advised).