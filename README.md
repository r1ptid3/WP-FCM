# Firebase Cloud Messaging in WordPress without any plugin

This is fully working WordPress theme with Firebase Cloud Messaging only functionality.

Below will be described all important steps to setup Firebase Cloud Messaging in WordPress

# Tests
- You can check how it works here: https://wp-fcm.mekhtiev.pro/
- Allow notifications from website with you browser on desktop or any android device
- Enter to author account with following credentials:

<br/>

<ul>
  <li>Login - awesome-coder</li>
  <li>Pass - mRHbeJPan&r8KD*5GuvdM5Uf</li>
</ul>

<br/>

<ul>
  <li>Publish NEW post</li>
  <li>Please, do not forget to clear your posts and block notifications after finishing test, otherwise you'll proceed getting all test push notifications from other testers</li>
</ul>

# Configurating

1. MAKE SURE THAT YOUR SITE USING HTTPS

2. Setup Code
- Place firebase-messaging-sw.js into root directory of your WordPress instllation. ( On the same level with wp-content folder )
- Place push-notification.js into your assets folder
- Copy all functions from functions.php file to your one

3. Initialize your firebase application
- Register at https://firebase.google.com
- Go to console and create your project https://console.firebase.google.com/
- Copy your Firebase Config from ( Firebase -> Project settings -> General -> Your app -> Config ) and put it into push-notification.js and firebase-messaging-sw.js
- Copy your Server Key from ( Firebase -> Project settings -> Cloud Messaging -> Server key ) and put it into functions.php to send_notification() function. Keep 'key=' before your server key!

4. Change your notification in "notification_on_publish" function in functions.php 
- As title and message you can use any string
- To show icon pass the image url
- Put any link into $click variable, this link will be open in new window on user's click to notification

5. Make sure that everything is fine and remove all console.logs from .js files and debug information from update_notification_tokens() function

# Docs
- https://firebase.google.com/docs/cloud-messaging/js/client?authuser=0
- https://mobiforge.com/design-development/web-push-notifications
- https://developers.google.com/web/fundamentals/push-notifications/sending-messages-with-web-push-libraries

# TODO
1. Subscribe & Unsibscribe users in right way https://developers.google.com/web/fundamentals/push-notifications/subscribing-a-user
2. Extend with couple buttons
