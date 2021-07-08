// !!!!!!!! Put this file into WordPress root instalation !!!!!!!!

// Import the same firebase scripts what you enqueue in your functions.php
importScripts('https://www.gstatic.com/firebasejs/8.7.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.7.0/firebase-messaging.js');

// Enter your Firebase Config from ( Firebase -> Project settings -> General -> Your app -> Config ).
var firebaseConfig = {
    apiKey: "apiKey>",
    authDomain: "authDomain>",
    projectId: "projectId>",
    storageBucket: "storageBucket>",
    messagingSenderId: "messagingSenderId>",
    appId: "appId>"
};

firebase.initializeApp( firebaseConfig );

const messaging = firebase.messaging();

messaging.setBackgroundMessageHandler( function( payload ) {
	console.log( payload );
	const notification = JSON.parse( payload );
    const notificationOption = {
        body:notification.body,
        icon:notification.icon
    };
    return self.registration.showNotification( payload.notification.title, notificationOption );
});