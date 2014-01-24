#! /bin/bash

declare -a exclude_patterns=('\.*.swp' '\.*.swn' '\.*.swo' diff deploy.sh)

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )" &&
cd $DIR &&

rm -rf se_copy &&
rm -rf search-everything.zip &&
mkdir se_copy &&
cp -r ../search-everything/ se_copy/search-everything &&

cd se_copy &&

echo "Removing tmp files" &&
for t in "${exclude_patterns[@]}"; do echo "  $t" && find . -name "$t" -delete; done &&

echo "Check php" &&
find . -name '*.php' | xargs -I file ../php_check.sh file &&

echo "Minimizing js" &&
find . -name '*.js' | xargs -I file ../minify_js.sh file &&

echo "Optimizing images" &&
find . -name '*.jpg' -exec ../optim_jpg.sh {} \; &&
find . -name '*.png' -exec ../optim_png.sh {} \; &&

echo "Packing" &&
zip -r search-everything.zip search-everything &&
mv search-everything.zip ../ &&

echo && echo "OK" || echo "ERROR"
