# Injader install guide

## Get the files onto your site

Download the latest release zip file. Extract the contents.

Transfer the contents of the "upload" folder to your site via FTP.

## Open the install wizard

Open your site in a browser - you should automatically be redirected to the installer.

Under "New installs", click the link to run the install wizard.

You'll need to follow some additional steps, but starting the install wizard now is the easiest way to ensure you don't miss anything.

## Installer Step 1

The installer checks if it can write to your site. If this works, proceed to the next step of the guide.

If it fails: The installer needs to be able to write to the folder you just uploaded to. You'll need to set the ownership of the folder to the web user. E.g.

    $ cd <project-root>
    $ sudo chown -R apache:apache .
    $ sudo chmod -R g+rw .

## Installer Step 2

Create an empty MySQL database. Create a MySQL user and give the user access to the database you just created.

Now enter the database name, database username and password in the installer.

At this point, you will also need to create an admin username and password for your new Injader installation.

## Installer Step 3

This will confirm that the installer is able to connect to the database. If this step fails, go back to step 2 and correct the details.

## Installer Step 4

This creates a default installation in your database.

## Tidying up

Delete the installer folder from your site.
