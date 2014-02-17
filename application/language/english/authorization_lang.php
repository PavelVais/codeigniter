<?php
/****************************************
 * Czech language for MyslimNaTebe sites
 * You can pass arguments to %1 and %x 
 * instead of words.
 ****************************************/

// Errors
$lang['auth.login.incorrect'] = 'Email or Password is incorrect.';
$lang['auth.password.incorrect'] = 'Password is incorrect.';
$lang['auth.password.empty'] = 'You can login in into this account only via Facebook. Account doesn\'t have password';
$lang['auth.account.banned'] = 'This account has been banned.';
$lang['auth.account.not_activated'] = 'Your account is not activated. Please activate it via sent email.';

$lang['auth.email.in_use'] = 'Email is already used by another user. Please choose another email.';
$lang['auth.email.unknown'] = "This email doesn't exists";
$lang['auth.username.in_use'] = 'Username already exists. Please choose another username.';
$lang['auth_current_email'] = 'This is your current email.';
$lang['auth_incorrect_captcha'] = 'Your text is not match with text in the picture.';
$lang['auth_captcha_expired'] = 'Your password expired, try it one more.'; //přepsat do češtiny
$lang['auth.must_be_logged_in'] = 'You must be logged in.';

$lang['auth.account.changed.successful'] = "Your account was successfully changed.";
$lang['auth.account.changed.not_successful'] = "In your account wasn\'t any changes.";

$lang['auth_password_not_same'] = 'Your passwords doesn\'t match.'; //přepsat do češtiny
$lang['auth_generic_error'] = 'To account wasn\'t possible to log in. Refresh your site and repete the log in.'; //přepsat do češtiny
$lang['auth_fcb_link'] = 'Account was successfully connect to Facebook.'; //přepsat do češtiny

$lang['auth.password.recovery.in_use'] = 'Last password recovery is still actual. Look into your email for next steps.';
$lang['auth.password.recovery.done'] = 'Confirm your email to recovery your password.';

// Notifications
$lang['auth_fcb_cant_log_in'] = 'Sign in via Facebook was failed. Please refresh site and try it again.';
$lang['auth.logged_in'] = 'You have been successfully logged in.';
$lang['auth.logged_out'] = 'You have been successfully logged out.';

$lang['auth_message_registration_disabled'] = 'Sign in is not allowed yet.';
$lang['auth_message_registration_completed_1'] = 'You was successfully signed in. Check your email to activate the account.';
$lang['auth_message_registration_completed_2'] = 'You was successfully signed in.';
$lang['auth_message_activation_email_sent'] = 'New activating email was sent on %s. Follow the steps in the email to activating the account.';
$lang['auth_message_activation_completed'] = 'Your account was successfully activated.';
$lang['auth_message_activation_failed'] = 'Activation code is bad or expired.';
$lang['auth_message_password_changed'] = 'Your password was successfully changed.';
$lang['auth_message_new_password_sent'] = 'Email with steps to restore the password was sent.';
$lang['auth_message_new_password_activated'] = 'Your password was successfully restored.';
$lang['auth_message_new_password_failed'] = 'Your activation key is incorrect or expired. Please check your email again and follow the instructions.';
$lang['auth_message_new_email_sent'] = 'A confirmation email has been sent to %s. Follow the instructions in the email to complete this change of email address.';
$lang['auth_message_new_email_activated'] = 'You have successfully changed your email';
$lang['auth_message_new_email_failed'] = 'Your activation key is incorrect or expired. Please check your email again and follow the instructions.';

$lang['auth_message_unregistered'] = 'Your account was deleted.';
$lang['auth_message_email_change_1'] = "Your email will be changed, after you confirm email in your new email address.";
$lang['auth_message_email_change_2'] = "Your email was confirm. Now you can use the new email for log in.";
$lang['auth_message_email_change_error'] = "Email is not possible to change, because the last change wasn't still confirmed. You have to go to your email and confirm it, or wait to expiration.";
$lang['auth_message_email_change_error_2'] = "The code to change is not correct. No account wasn't changed.";
$lang['auth_message_email_change_error_3'] = "This email is already used, it isn't possible to use it for your change.";
$lang['auth_message_account_changed'] = "Your account was successfully changed.";
$lang['auth_message_account_not_changed'] = "In your account was not made any changes.";

$lang['auth_message_password_change_success'] = "Your password was successfully restored, you can login now.";
$lang['auth_message_password_change_error_1'] = "Restore of password wasn't done, the change was expirat.";
$lang['auth_message_password_change_error_2'] = "Restore of password wasn't done, was inserted bad token.";


// Email subjects
$lang['auth_subject_welcome'] = 'Welcome  %s!';
$lang['auth_subject_activate'] = 'Welcome %s!';
$lang['auth_subject_forgot_password'] = 'You forgot your password to account %s?';
$lang['auth_subject_reset_password'] = 'Your new password for account %s';
$lang['auth_subject_change_email'] = 'Your new email address for account %s';
