var map = L.map('venueMap').setView([BTBooking.lat, BTBooking.lng], 13);
var marker = L.marker(L.latLng(BTBooking.lat, BTBooking.lng));

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

marker.addTo(map);

function onMapClick(e) {
	jQuery("#btb_address_lat").val(e.latlng.lat);
	jQuery("#btb_address_lon").val(e.latlng.lng);
	marker.setLatLng(e.latlng);
}

map.on('click', onMapClick);

function getAddressCoords() {
	jQuery('#mapMessages').empty();
	jQuery('#locs_table').hide();
	jQuery('#locs_body').empty();

	var params = new Object();

	var house_number = new String(jQuery('#btb_address_number').val());
	house_number.trim();
	if (house_number) {
		params.house_number = house_number;
	}

	var street = new String(jQuery('#btb_address_street').val());
	street.trim();
	if (street) {
		params.street = street;
	}

	var zip = new String(jQuery('#btb_address_zip').val());
	zip.trim();
	if (zip) {
		params.zip = zip;
	}

	var city = new String(jQuery('#btb_address_city').val());
	city.trim();
	if (city) {
		params.city = city;
	}

	var country = new String(jQuery('#btb_address_country').val());
	country.trim();
	if (country) {
		params.country = country.toLowerCase();
	}

	getAddressCoordsFromOSMNominatim(params);
}

function getAddressCoordsFromOSMNominatim(p) {
	var osmnAPI = "https://nominatim.openstreetmap.org/search?json_callback=?";
	var params = new Object();
	params.format = "json";
	params.addressdetails  = 1;

	var street_and_number = p.house_number + " " + p.street;
	if (street_and_number) {
		params.street = street_and_number;
	}

	if (p.zip) {
		params.postalcode = p.zip;
	}

	if (p.city) {
		params.city = p.city;
	}

	if (p.country) {
		params.countrycodes = p.country
	}

	jQuery.getJSON(osmnAPI, params)
		.done(function(data) {
			places = new Array();
			if (data.length) {
				data.forEach(function(place) {
					var loc = new Object();
					loc.lat = place.lat;
					loc.lng = place.lon;
					loc.house_number = place.address.house_number;
					loc.street = place.address.road;
					loc.city = place.address.city;
					loc.zip = place.address.postcode;
					loc.state = place.address.state;
					loc.country = place.address.country;
					loc.cc = place.address.country_code;
					places.push(loc);
				});
			}
			processAddressData(places);
		})
		.fail(function(data) {
			processAddressError('error', BTBooking.queryErrorMsg);
		});

}


function processAddressError(type, msg) {
	var content = new String();
	switch(type) {
		case "error":
			content = btb_render_error_msg(msg);
			break;
		default:
			content = btb_render_warning_msg(msg);
			break;
	}
	jQuery('#mapMessages').append(content);
}


function processAddressData(places) {
	if (places.length) {
		places.forEach(function(place, idx) {
			var htmlCont = [];
			var street_and_number = [];
			if (place.street) {
				street_and_number.push(place.street);
			}
			if (place.house_number) {
				street_and_number.push(place.house_number);
			}
			var htmlButton = [];
			htmlButton.push(
				'<button type="button" class="button button_small" ',
				'onclick="btb_choose_loc(this)" ',
				btb_create_data_attr('lat', place.lat),
				btb_create_data_attr('lng', place.lng),
				btb_create_data_attr('street', place.street),
				btb_create_data_attr('house-number', place.house_number),
				btb_create_data_attr('city', place.city),
				btb_create_data_attr('zip', place.zip),
				btb_create_data_attr('state', place.state),
				'>',
				Number(idx + 1).toString(),
				'</button>'
			);
			htmlCont.push(
				'<tr style="text-align:left">',
				btb_render_table_data(htmlButton.join('')),
				btb_render_table_data(street_and_number.join(' ')),
				btb_render_table_data(place.city),
				btb_render_table_data(place.zip),
				btb_render_table_data(place.state),
				'</tr>'
			);
			jQuery('#locs_body').append(htmlCont.join(''));
			jQuery('#locs_table').show();
		});
	} else {
		processAddressError('warning', BTBooking.nothingFoundMsg);
	}

}


function btb_render_error_msg(message) {
	var retHtml = [];
	retHtml.push(
		'<div class="error notice notice-error"><p>',
		message,
		'</div>'
	);
	return retHtml.join('');
}


function btb_render_warning_msg(message) {
	var retHtml = [];
	retHtml.push(
		'<div class="notice notice-warning"><p>',
		message,
		'</div>'
	);
	return retHtml.join('');
}


function btb_render_table_data(content) {
	var retHtml = [];
	var cont = content ? content : "";
	retHtml.push('<td>', cont, '</td>');
	return retHtml.join('');
}


function btb_create_data_attr(key, value) {
	var retHtml = ["data-"];

	retHtml.push(key, '="');

	if (value) {
		retHtml.push(value);
	}

	retHtml.push('" ');

	return retHtml.join('');
}


function btb_choose_loc(e) {
	var loc = jQuery(e);
	var lat = loc.data("lat");
	var lng = loc.data("lng")
	jQuery('#btb_address_street').val(loc.data("street"));
	jQuery('#btb_address_number').val(loc.data("house-number"));
	jQuery('#btb_address_zip').val(loc.data("zip"));
	jQuery('#btb_address_city').val(loc.data("city"));
	jQuery('#btb_address_region').val(loc.data("state"));
	jQuery('#btb_address_lat').val(lat);
	jQuery('#btb_address_lon').val(lng);
	marker.setLatLng([lat, lng]);
	map.setView([lat, lng]);
}

jQuery('#search_address').on('click', getAddressCoords);

jQuery('#btb_use_coordinates').on('change', function() {
	if (jQuery(this).prop('checked')) {
		jQuery('#search_address').show();
		jQuery('#venueMap').show();
	} else {
		jQuery('#venueMap').hide();
		jQuery('#search_address').hide();
	}
});