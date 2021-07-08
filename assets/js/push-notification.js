window.addEventListener( "load", function () {
    firebase_messaging();
});

function firebase_messaging() {
    // Enter your Firebase Config from ( Firebase -> Project settings -> General -> Your app -> Config ).
    var firebaseConfig = {
        apiKey: "<apiKey>",
        authDomain: "<authDomain>",
        projectId: "<projectId>",
        storageBucket: "<storageBucket>",
        messagingSenderId: "<messagingSenderId>",
        appId: "<appId>"
    };

    firebase.initializeApp( firebaseConfig );

    const messaging = firebase.messaging();

    messaging.onMessage( function( payload ) {
        console.log( payload );
        const notificationOption = {
            body:payload.notification.body,
            icon:payload.notification.icon
        };

        if( Notification.permission === "granted" ){
            var notification = new Notification( payload.notification.title, notificationOption );

            // Notification onclick function
            notification.onclick = function( ev ) {
                ev.preventDefault();
                window.open( payload.notification.click_action, '_blank' );
                notification.close();
            }
        }

    });

    messaging.onTokenRefresh(function () {
        messaging.getToken()
            .then( function( newtoken ) {
                console.log( 'New Token received' );
            })
            .catch( function( error ) {
                console.log( error );
            });
    });

    InitializeFireBaseMessaging( messaging );
}

function InitializeFireBaseMessaging( messaging ) {
    messaging.requestPermission()
        .then(function() {
            return messaging.getToken();
        })
        .then( function( token ) {
            console.log( 'Token received' );

            let data = {
                action: 'update_notification_tokens',
                'token': token
            };

            jQuery.ajax({
                type: 'POST',
                url: ajax_obj.ajax_url,
                data: data,
                success : function( ) {
                    console.log( 'Token saved successfully' );
                }
            });
        })
        .catch( function( error ) {
            console.log( error );
        });
}
