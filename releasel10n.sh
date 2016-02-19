#!/bin/bash
cd languages
for LANG in de_DE
do
msgfmt bt-booking-$LANG.po -o bt-booking-$LANG.mo
done
