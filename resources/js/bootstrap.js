/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as dropdowns.
 * This application only uses Bootstrap's dropdown component for navigation.
 */

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {
    console.error('Error loading Bootstrap dependencies:', e);
}
