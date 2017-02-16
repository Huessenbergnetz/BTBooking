var elixir = require('laravel-elixir');
elixir.config.assetsPath = 'assetssrc';
elixir.config.publicPath = '';

elixir(function(mix) {
    
    var llp = 'node_modules/leaflet/dist/';
    
    mix.sass('admin.scss', 'admin/assets/admin.css');
    
    mix.sass('btb-avada-style.scss', 'assets/btb-avada-style.css');
    mix.sass('btb-bs3-style.scss', 'assets/btb-bs3-style.css');
    mix.sass('btb-default-style.scss', 'assets/btb-default-style.css');
    
    mix.scripts([
                'btb-checkout.js',
                'btb-direct-booking.js'
    ], 'assets/btb-scripts.js');
    
    mix.scripts([
        'admin/edit-booking.js',
        'admin/edit-event.js',
        'admin/edit-venue.js',
        'btb-country-chooser.js'
    ], 'admin/assets/btb-admin-scripts.js');
    
    mix.copy(llp + 'leaflet.css', 'assets/leaflet/leaflet.css');
    mix.copy(llp + 'leaflet.js', 'assets/leaflet/leaflet.js');
    mix.copy(llp + 'images', 'assets/leaflet/images');
});
