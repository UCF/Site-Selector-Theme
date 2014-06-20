# UCF Partnership Site Selector Theme

WordPress theme built off of UCF's Generic theme.


## Installation Requirements:
* n/a


## Deployment

This theme relies on Twitter's Bootstrap framework. UCF's fork of the Bootstrap project (http://github.com/UCF/bootstrap/) is added as submodule in static/bootstrap. Bootstrap must be initialized as a submodule with every new clone of this theme repository.

#### Initializing Bootstrap with a new clone:
1. Pull/Clone the theme repo
2. From the theme's root directory, run `git submodule update --init static/bootstrap`
3. From the static/bootstrap directory, run `git checkout master`.  Make sure a branch has been checked out for submodules as they will default to 'no branch' when cloned.  If you're developing a new theme off of Generic and have created a new Bootstrap branch (see the Development section), checkout that branch instead.

#### Alternative method using Git v1.6.5+:
1. Run `git clone` using the `--recursive` parameter to clone the repo with all of its submodules; e.g. `git clone --recursive https://github.com/UCF/Wordpress-Generic-Theme.git`
2. From the static/bootstrap directory, run `git checkout master`.  Make sure a branch has been checked out for submodules as they will default to 'no branch' when cloned.


## Development

This theme relies on Twitter's Bootstrap framework. Bootstrap is a CSS framework that uses LESS to programatically develop stylesheets.
UCF's fork of the Bootstrap project (http://github.com/UCF/bootstrap/) is added as submodule in static/bootstrap.

### Setup
** Note: This theme uses a version of Bootstrap whose package requirements result in Bootstrap's CSS files compiling to empty files. Follow the steps below completely to install the packages so that the `make` command works correctly. (https://github.com/twitter/bootstrap/issues/8088) **

0. If they're not already installed on your machine, install node and npm for node-related package management.
1. If this is a brand new clone, run `git submodule update --init static/bootstrap` from the theme's root directory.
2. Navigate to static/bootstrap, then run `npm install` to install necessary dependencies for building Bootstrap's .less files. These packages are excluded in the submodule .gitignore.
3. Navigate to the submodule's node_modules/recess folder, and open **package.json**. Under 'dependencies', update 'less' from '>= 1.3.0' to '1.3.3' and save. Delete node_modules/ from within the recess directory.
4. From the recess directory, run `npm install`.
5. Navigate back to the root bootstrap directory and remove the compiled bootstrap directory, if it exists.

### Compiling
Once the setup instructions above have been completed, you can compile modified .less files from the root bootstrap directory with `make bootstrap`. Compiled files will save to a new directory 'bootstrap' within the root directory (static/bootstrap/bootstrap/).



## Important files/folders:

### functions/base.php
Where functions and classes used throughout the theme are defined.

### functions/config.php
Where Config::$links, Config::$scripts, Config::$styles, and
Config::$metas should be defined.  Custom post types and custom taxonomies should
be set here via Config::$custom_post_types and Config::$custom_taxonomies.
Custom thumbnail sizes, menus, and sidebars should also be defined here.

### functions.php
Theme-specific functions only should be defined here.  (Other required
function files are also included at the top of this file.)

### shortcodes.php
Where Wordpress shortcodes can be defined.  See example shortcodes for more 
information.

### custom-post-types.php
Where the abstract custom post type and all its descendants live.

### static/
Where, aside from style.css in the root, all static content such as
javascript, images, and css should live.
Bootstrap resources should also be located here.


## Notes

This theme utilizes Twitter Bootstrap as its front-end framework.  Bootstrap
styles and javascript libraries can be utilized in theme templates and page/post
content.  For more information, visit http://twitter.github.com/bootstrap/

Note that this theme may not always be running the most up-to-date version of
Bootstrap.  For the most accurate documentation on the theme's current
Bootstrap version, visit http://bootstrapdocs.com/ and select the version number
found at the top of static/bootstrap/bootstrap/css/bootstrap.css

### Using Cloud.Typography
This theme is configured to work with the Cloud.Typography web font service.  To deliver the web fonts specified in
this theme, a project must be set up in Cloud.Typography that references the domain on which this repository will live.

Development environments should be set up in a separate, Development Mode project in Cloud.Typography to prevent pageviews
from development environments counting toward the Cloud.Typography monthly pageview limit.  Paste the CSS Key URL provided 
by Cloud.Typography in the CSS Key URL field in the Theme Options admin area.

This site's production environment should have its own Cloud.Typography project, configured identically to the Development
Mode equivalent project.  **The webfont archive name (usually six-digit number) provided by Cloud.Typography MUST match the
name of the directory for Cloud.Typography webfonts in this repository!**


## Custom Post Types
* Parallax Feature


## Custom Taxonomies

n/a


## Shortcodes

### [blockquote]
* Generates a stylized blockquote.

### [parallax_feature]
* Adds a Parallax Feature to page/post content.

### [posttype-list]
* Custom post types that have defined $use_shortcode as True can automatically
utilize this shortcode for displaying a list of posts created under the given
post type; e.g., [document-list] will output a list of all published Documents.
Additional parameters can be used to further narrow down the shortcode's results;
see the Theme Help section on shortcodes for an available list of filters.

### [search_form]
* Outputs the site search form.  The search form output can be modified via
searchform.php

### [post-type-search]
* Generates a searchable list of posts. Post lists are generated in alphabetical order and, by default, by category and post title. Posts can be searched by post title and any tags assigned to the post. See the Theme Help section on shortcodes for more information.