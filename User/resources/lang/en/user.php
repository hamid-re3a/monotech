<?php

return [

    'responses' => [
        'invalid-member-id' => 'Invalid member id',
        'global-error' => 'An error occurred, Try again please.',
        'max-attempts-exceeded' => 'Too many invalid attempts. Try again after a while',
        'user-account-deactivated-by-admin' => 'Your account is deactivated by admin',
        'user-account-activated-by-admin' => 'Your account is activated by admin',
        'successfully-registered-go-activate-your-email' => 'You\'ve successfully registered, please activate your email. ',
        'go-activate-your-email' => 'Please first activate your email ',
        'invalid-inputs-from-user' => 'Email or password is not correct',
        '2FA-is-now-disabled' => 'You\'ve disabled 2fa',
        'Invalid-verification-Code-Please-try-again' => 'Enter a valid 2FA code',
        '2FA-is-already-enabled' => '2Fa is already enabled',
        '2FA-is-enabled-successfully' => 'You\'ve enabled 2fa',
        'ok'=>'ok',
        'login-successful' => 'You\'ve successfully logged in ',
        'logout-successful' => 'You\'ve successfully logged out ',
        'password-successfully-changed' => 'You\'ve successfully changed your password ',
        'forgot-password-otp-exceeded-amount' => 'You\'ve reached the otp limitation please wait',
        'otp-is-wrong' => 'Otp is wrong',
        'otp-is-not-valid-any-more' => 'Otp is not valid anymore',
        'otp-successfully-sent' => 'Otp is successfully sent',
        'email-is-already-verified' => 'Email is already verified',
        'otp-exceeded-amount' => 'You\'ve reached the otp limitation please wait',

        'email-verification-code-is-incorrect' => 'The verification code is incorrect, Please check and refresh your email inbox',
        'email-verification-code-is-expired' => 'The email verification code is expired',
        'email-verification-code-is-used' => 'The email verification code is used',
        'wait-limit' => 'Please wait for a while. You\'ve reached the limit',
        'password-reset-code-is-invalid' => 'Enter a valid password reset code',
        'password-reset-code-is-expired' => 'The password reset code is expired',
        'password-reset-code-is-used' => 'The password reset code is used',


        'email-does-not-exist' => 'Email does\'nt exist',
        'email-already-exists' => 'The email address already exists',
        'password-already-used-by-you-try-another-one' => 'Your new password cannot be same as old password',
        'max-login-attempt-blocked' => 'You\'ve reached max login attempt, you are blocked',
        'invalid-input' => 'Inputs are not correct',
        'unauthorized' => 'You are not authorized to visit this',
        'unauthorized-email-is-not-verified' => 'You should verify your email first',
        'user-is-blocked' => 'Your account is blocked. Please reach support team',
        'username-already-exists' => 'The username already exists',
        'username-does-not-exist' => 'The user name does not exists' ,

        'transaction-password-otp-code-is-expired' => 'The otp code is expired',
        'transaction-password-code-code-is-used' => 'The otp code is used',
        'transaction-password-otp-code-is-incorrect' => 'The otp code is incorrect, Please check and refresh your email inbox',
        'current-transaction-password-is-invalid' => 'Current transaction password is not correct .',
        'transaction-password-successfully-changed' => 'You\'ve successfully changed your transaction password ',
        'profile-details-updated' => 'You\'ve successfully changed your profile ',
        'avatar-updated' => 'You\'ve successfully updated your avatar',
        'user-has-no-avatar' => 'User has no avatar',

        'wrong-wallet-address' => 'Invalid Bitcoin wallet address.',
        'wallet-updated' => 'You\'ve successfully updated your :currency wallet',

        'user-account-frozen-successfully' => 'User account has been frozen successfully',
        'user-account-unfreeze-successfully' => 'User account has been unfrozen successfully',

        'user-account-deactivate-successfully' => 'User account has been deactivated successfully',
        'user-account-activate-successfully' => 'User account has been activated successfully',

        'you-cant-block-unblock-your-account' => 'You can not block/unblock your account',
        'user.responses.you-cant-deactivate-active-your-account' => 'You can not activate/deactivate your account',
        'user.responses.you-cant-freeze-unfreeze-your-account' => 'You can not freeze/unfreeze your account',

        'invalid-setting-key' => 'Invalid setting key',
        'invalid-email-key' => 'Invalid email key',
        'invalid-login-attempt-id' => 'Invalid login attempt',
        'we-are-under-maintenance' => 'We\'re under maintenance.',
        'sponsor-has-no-valid-package' => 'Sponsor has no valid package.',

    ],
    'validation' => [
        'email-not-exists'=>'The selected email address is not yet registered.',
        'password-same' => 'The passwords are not matching.',
        'email-unique' => 'The email address already exists.',
        'username-unique' => 'The username already exists.',
        'username-regex' => 'The username may have alpha-numeric characters or underscores.',
        'username-required' => 'Enter your username.',
        'sponsor-username-regex' => 'Invalid sponsor name.',
        'sponsor-username-required' => 'Enter your sponsor username.',
        'sponsor-username-exists' => 'The sponsor username is invalid.',
        'password-regex' => 'The password is not complex enough.',
        'first-name-required' => 'Enter your first name.',
        'last-name-required' => 'Enter your last name.',
        'email-address-required' => 'Enter your email address.',
        'password-is-required' => 'Enter your password.',
        'confirm-password' => 'Confirm your password.',
        'email-is-incorrect' => 'The email address is incorrect.',
    ]
];

