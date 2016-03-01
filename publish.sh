#!/bin/bash
RELEASE=false
NOYUIC=false
INFOFILE="bt-booking.php"
PKGNAME="bt-booking"
CURRENTDIR=${PWD##*/}

for ARG in $*
do
    case $ARG in
        --release) RELEASE=true;;
        --no-yuic) NOYUIC=true;;
    esac
done

# Find all js files and minify them if release, otherwise copy, to kepp name.
for SCRIPT in `find . -name "*.js" -not -name "*.min.js" -type f -printf '%p '`
do
    MINIFIEDNAME=`echo $SCRIPT | sed 's/.js/.min.js/'`
    if [ "$RELEASE" == "true" ]
    then
		if [ "$NOYUIC" == "true" ]
		then
			curl -X POST -s --data-urlencode "input@${SCRIPT}" https://javascript-minifier.com/raw > $MINIFIEDNAME
		else
			yc --type js -o $MINIFIEDNAME $SCRIPT
		fi
    else
        cp $SCRIPT $MINIFIEDNAME
    fi
done

# Find all css files and minify them if release, otherwise copy, to keep name.
for CSSFILE in `find . -name "*.css" -not -name "*.min.css" -type f -printf '%p '`
do
    MINIFIEDNAME=`echo $CSSFILE | sed 's/.css/.min.css/'`
    if [ "$RELEASE" == "true" ]
    then
		if [ "$NOYUIC" == "true" ]
		then
			curl -X POST -s --data-urlencode "input@${CSSFILE}" https://cssminifier.com/raw > $MINIFIEDNAME
		else
			yc --type css -o $MINIFIEDNAME $CSSFILE
		fi
    else
        cp $CSSFILE $MINIFIEDNAME
    fi
done

# Process translation files.
./releasel10n.sh

# Get current version
VERSION=`grep "Version:" $INFOFILE | sed 's/Version: //' | sed 's/^[ \t]*//;s/[ \t]*$//'`

# Remove previous package
if [ -f ${PKGNAME}-${VERSION}.* ]; then
rm ${PKGNAME}-${VERSION}.*
fi

# Create archive
pushd .. > /dev/null
find $CURRENTDIR \( ! -regex '.*/\..*' ! -name '*.sh' ! -iname 'doxy*' ! -name '*.kdev4' \) -print | zip -q ${PKGNAME}-${VERSION}.zip -@
popd > /dev/null