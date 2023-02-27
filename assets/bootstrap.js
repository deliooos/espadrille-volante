import { startStimulusApp } from '@symfony/stimulus-bridge';
import Lightbox from 'stimulus-lightbox'
import Notification from 'stimulus-notification'

// Registers Stimulus controllers from controllers.json and in the controllers/ directory
export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.[jt]sx?$/
));

// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);

// Initializing the lightbox controller for the housing details pages and notification for Symfony flash messages
app.register('lightbox', Lightbox);
app.register('notification', Notification);
