#!/bin/bash
RELEASE=false
NOYUIC=false
INFOFILE="bt-booking.php"
PKGNAME="bt-booking"
PKGDIRNAME="BTBooking"
TMPPATH="/tmp/${PKGDIRNAME}"
# CURRENTDIR=${PWD##*/}
CURRENTDIR=$PWD

npm run prod

# Process translation files.
./releasel10n.sh

# Get current version
VERSION=`grep "Version:" $INFOFILE | sed 's/Version: //' | sed 's/^[ \t]*//;s/[ \t]*$//'`

# Remove previous package
if [ -f ${PKGNAME}-${VERSION}.* ]; then
rm ${PKGNAME}-${VERSION}.*
fi

# Create archive
if [ -d $TMPPATH ]; then
    rm -rf ${TMPPATH}/*; else mkdir $TMPPATH; fi

cp -r admin assets framework languages $TMPPATH
cp bt-*.php $TMPPATH
cp class.* $TMPPATH
cp LICENSE README.md readme.txt $TMPPATH

# cp -r ${TMPPATH}/* /srv/www/wordpress/wp-content/plugins/BTBooking

pushd /tmp > /dev/null
zip -9 -r -q ${CURRENTDIR}/${PKGNAME}-${VERSION}.zip ${PKGDIRNAME}
popd > /dev/null

# pushd .. > /dev/null
# find $CURRENTDIR \( ! -regex '.*/\..*' ! -name '*.sh' ! -iname 'doxy*' ! -name '*.kdev4' \) -print | zip -q ${PKGNAME}-${VERSION}.zip -@
# popd > /dev/null
