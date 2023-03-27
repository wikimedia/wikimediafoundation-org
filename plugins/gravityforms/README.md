# Gravity Forms
Version 2.6.6.1 of Gravity Forms

## Projects using this repository

- SC CA
- HM Agency Marketing Site
- Wikimedia Sound Logo

*If other projects use this repository, please add them here*

## Using this plugin as a submodule

1. Go to your project plugins folder (MU or not)
1. Add the plugin as submodule 
   - `git submodule add git@github.com:humanmade/gravity-forms.git gravityforms`
1. Commit the changes as normal, your plugin will be added as a submodule :)

## Updating 

1. Clone this repo `git clone git@github.com:humanmade/gravity-forms.git`
1. `cd gravity-forms`
1. Create a new a branch with the name of the Gravity Forms version you want to update to.
	 - `git checkout -b 2.6.6.1`
1. Update the files to the new version.
1. Commit, tag & push.
    - `git add .`
    - `git commit -m "2.6.6.1"`
    - `git tag "2.6.6.1"`
    - `git push origin --all`
1. Make a Pull Request with the version's name.

This way we can keep all the newer versions on Git and if a site needs other version than the latest one it can have it from this repository too.



