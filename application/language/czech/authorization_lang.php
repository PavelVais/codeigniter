<?php
/****************************************
 * Czech language for MyslimNaTebe sites
 * You can pass arguments to %1 and %x 
 * instead of words.
 ****************************************/

// Errors
$lang['auth.login.incorrect'] = 'Heslo nebo login jsou špatné nebo neexistují.';
$lang['auth.password.empty'] = 'K danému účtu je možné se přihlásit pouze přes Facebook. Účet nemá heslo.';

$lang['auth.password.incorrect'] = 'Vložili jste špatné heslo.';
$lang['auth.account.banned'] = 'Váš účet byl zablokován.';
$lang['auth.account.not_activated'] = 'Váš účet jste si ještě neaktivovali. Aktivování provede v emailu, který Vám při registraci přišel.';
$lang['auth.email.in_use'] = 'Email už je používán. Prosím, vyberte si jiný.';
$lang['auth.email.unknown'] = "Tento email není u nás uložen.";
$lang['auth.username.in_use'] = 'Přezdívka už je použita. Prosím, vyberte si jinou.';
$lang['auth.must_be_logged_in'] = 'Muste být přihlášen.';

$lang['auth.account.changed.successful'] = "Váš účet byl úspěšně změněn.";
$lang['auth.account.changed.not_successful'] = "Ve Vašem účtu nebyly provedeny žádné změny.";


$lang['auth_password_not_same'] = 'Vaše hesla se neshodují'; //přepsat do češtiny
$lang['auth_generic_error'] = 'Účet se nemohl přihlásit. Obnovte prosím stránku a akci zopakujte.'; //přepsat do češtiny
$lang['auth_fcb_link'] = 'Účet byl úspěšně provázán s Facebookem.'; //přepsat do češtiny

$lang['auth.password.recovery.in_use'] = 'Poslední obnova hesla je stále aktuální, podívejte se do své emailové schránky.';
$lang['auth.password.recovery.done'] = 'Pro obnovu hesla musíte potvrdit email, který Vám každou chvíli příjde.';

//Emaily
$lang['auth.email.change.error.already'] = "Email nelze změnit, protože byl v poslední době měněn. Musíte minulou změnu potvrdit, nebo počkat, až se zneaktivní.";
$lang['auth.email.change.error.syntax'] = 'Daný kód na změnu emailu je neplatný. Žádný účet, ani email nebyly změněny.';
$lang['auth.email.change.step1'] = "Váš email bude změněn, až jeho změnu potvrdíte v emailu, který vám dojde na Vaší novou adresu.";

// Notifications
$lang['auth_fcb_cant_log_in'] = 'Přihlášení přes facebook se nezdařilo. Stránku obnovte a zkuste to prosím znovu.';
$lang['auth.logged_in'] = 'Byl jste úspěšně přihlášen.';
$lang['auth.logged_out'] = 'Byl jste úspěšně odhlášen.';

$lang['auth_message_registration_disabled'] = 'Registrace nejsou momentálně povoleny.';
$lang['auth_message_registration_completed_1'] = 'Byl jste úspěšně registrován. Zkontrolujte prosím email a aktivujte si účet.';
$lang['auth_message_registration_completed_2'] = 'Byl jste úspěšně registrován.';
$lang['auth_message_activation_email_sent'] = 'Nový aktivační email byl poslan na %s. Řiďte se prosím pokyny v emailu a aktivujte si účet.';
$lang['auth_message_activation_completed'] = 'Váš účet byl úspěšně aktivován.';
$lang['auth_message_activation_failed'] = 'Aktivační kód je špatný, nebo zastaralý.';
$lang['auth_message_password_changed'] = 'Vaše heslo bylo úspěšně změněno.';
$lang['auth_message_new_password_sent'] = 'Email s instrukcema pro vytvoření nového heslo byl úspěšně odeslán.';
$lang['auth_message_new_password_activated'] = 'Úspěšně jste si resetovali heslo.';
$lang['auth_message_new_password_failed'] = 'Your activation key is incorrect or expired. Please check your email again and follow the instructions.';
$lang['auth_message_new_email_sent'] = 'A confirmation email has been sent to %s. Follow the instructions in the email to complete this change of email address.';
$lang['auth_message_new_email_activated'] = 'You have successfully changed your email';
$lang['auth_message_new_email_failed'] = 'Your activation key is incorrect or expired. Please check your email again and follow the instructions.';

$lang['auth_message_unregistered'] = 'Váš účet byl smazán.';

$lang['auth_message_email_change_2'] = "Váš email byl potvrzen. Nyní ke svému přihlášení využijte nový email.";

$lang['auth_message_email_change_error_3'] = "Daný email je již zabrán, nelze tedy použit pro vaší změnu.";


$lang['auth_message_password_change_success'] = "Vaše heslo bylo úspěšně zresetováno, nyní se můžete přihlásit.";
$lang['auth_message_password_change_error_1'] = "Obnova hesla nemohla proběhnout, změna již není aktuální.";
$lang['auth_message_password_change_error_2'] = "Obnova hesla neproběhla, byl vložen neznámý token.";


// Email subjects
$lang['auth_subject_welcome'] = 'Vítejte %s!';
$lang['auth_subject_activate'] = 'Vítejte %s!';
$lang['auth_subject_forgot_password'] = 'Zapomněli jste heslo na %s?';
$lang['auth_subject_reset_password'] = 'Vaše nové heslo pro %s';
$lang['auth_subject_change_email'] = 'Vaše nová emailová adresa pro účet %s';