/**
 * Main JavaScript Entry Point
 * 
 * File này là entry point cho JavaScript application:
 * - Import bootstrap.js (Axios setup, CSRF token, etc.)
 * - Initialize Alpine.js cho reactive UI components
 * - Alpine.js được dùng cho:
 *   + Mobile menu toggle
 *   + Tab switching (profile page)
 *   + Modal interactions
 *   + Dynamic form behaviors
 * 
 * @author QuickPoll Team
 */

import './bootstrap';

import Alpine from 'alpinejs';

// Make Alpine globally available (có thể dùng trong Blade templates)
window.Alpine = Alpine;

// Start Alpine.js
Alpine.start();
