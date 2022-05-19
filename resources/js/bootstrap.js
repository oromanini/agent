window._ = require('lodash');

window.$ = require('jquery');

require('jquery-mask-plugin');

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
