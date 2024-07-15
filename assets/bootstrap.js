import { Application } from '@hotwired/stimulus';

// Expose jQuery for Bootstrap plugins (optional)
const $ = require('jquery');
global.$ = global.jQuery = $;

const application = Application.start();

// Register any custom, 3rd party controllers here
// Will be loaded asynchronously
