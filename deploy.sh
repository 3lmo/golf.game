#!/bin/sh

function fail_check {
    if [ "$?" -ne "0" ]; then
      echo "$1"
      exit 1
    fi
}

function make_apache_folder {
    echo "Creating apache folder: $1"
    sudo mkdir $1
    fail_check "Folder creation FAILED!"
    sudo chown -R apache:apache $1
    fail_check "Ownership change FAILED!"
    echo "Success."
}

function change_folder_perms {
    sudo chown -R apache:apache $1
    fail_check "Ownership change FAILED!"
    echo "Success."
}

php composer.phar install
php app/console cache:clear --env=prod

SRC_PATH=`pwd`
TARGET_PATH="/var/www/html/game"

sudo service httpd stop
fail_check "Can't stop Apache - do you have enough rights?"

sudo rm -Rf "$TARGET_PATH"
fail_check
sudo cp -R "$SRC_PATH" "$TARGET_PATH"
fail_check

php app/console doctrine:fixtures:load --append

sudo rm -Rf "$TARGET_PATH/app/cache"
make_apache_folder "$TARGET_PATH/app/cache"
sudo rm -Rf "$TARGET_PATH/app/logs"
make_apache_folder "$TARGET_PATH/app/logs"

#make_apache_folder "$TARGET_PATH/web/uploads"
#make_apache_folder "$TARGET_PATH/web/uploads/template"
#make_apache_folder "$TARGET_PATH/web/files"
#make_apache_folder "$TARGET_PATH/web/files/order_pdf"
#make_apache_folder "$TARGET_PATH/web/files/csv"
#make_apache_folder "$TARGET_PATH/web/files/excel"

change_folder_perms "$TARGET_PATH"

sudo service httpd start
