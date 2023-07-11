#!/bin/bash

#############################################################################
# This "postCreate" script runs after the Dev Container successfully starts #
#############################################################################

# install composer deps
cd /workspace/server
composer install

# set up permissions for the var folder for symfony
chown -R www-data:www-data /workspace/server/var
