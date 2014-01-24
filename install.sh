if [ "`echo $0 | cut -c1`" = "/" ] ; then
        SCRIPT_DIR=`dirname $0`
else
        SCRIPT_FILE=`pwd`/`echo $0`
        SCRIPT_DIR=`dirname $SCRIPT_FILE`
fi

if [ -e $SCRIPT_DIR/config.sh -a "$1" != reinstall ] ; then
	echo 'This project was already installed. To run this script again, run it with the "reinstall" parameter.'
	exit 1
fi

echo 'Creating "config.sh".'
cat > $SCRIPT_DIR/config.sh <<EOF
# Please fill out the following settings
# 
# The URL of the testing WordPress installation
WP_URL="http://localhost/wp"
# This plugin's directory of the testing WordPress instalation, where this plugin is installed to.
WP_PLUGIN_DIR="/var/www/localhost/wp/wp-content/plugins/wordpress-23-related-posts-plugin"
# You can also use the deploy.sh script to deploy to multiple WordPress installations, using:
	#WP_URL=("http://localhost/wp1" "http://localhost/wp2")
	#WP_PLUGIN_DIR=(
	#	"/var/www/localhost/wp1/wp-content/plugins/wordpress-23-related-posts-plugin"
	#	"/var/www/localhost/wp2/wp-content/plugins/wordpress-23-related-posts-plugin"
	#)
EOF

echo 'Please modify the "config.sh" file and enter your local settings.'
echo 'Then use "wordpress-23-related-posts-plugin/deploy.sh" script to deploy the plugin.'
