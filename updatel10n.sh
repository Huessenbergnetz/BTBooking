#!/bin/bash
find . -type f -name '*.php' | xargs xgettext --from-code=utf-8 --language=PHP -k__ -k_e -k_x:2c,1 -k_n:1,2 -k_n_noop:1,2 -kesc_attr_e -kesc_html_e -kesc_html__ --copyright-holder=Buschtrommel --package-name="BT Booking" --package-version=1.1.4 --msgid-bugs-address=kontak@buschmann23.de -d bt-booking -o languages/bt-booking.pot
