#! /bin/bash

cat $1 > pack_tmp.js

java -jar ../compiler.jar --js pack_tmp.js --js_output_file "$1"
ok=$?

rm pack_tmp.js

if [[ $ok -eq 0 ]]; then
	echo "  done: $1" && exit 0
else
	echo "  ERROR: $1" && exit 1
fi
