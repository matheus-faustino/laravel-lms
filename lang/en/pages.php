<?php

return [

    'landing' => [
        'title'      => 'Welcome',
        'subtitle'   => 'Click on the button below to sing in.',
        'login_link' => 'Sign in',
    ],

    'home' => [
        'title'       => 'Home',
        'greeting'    => 'Welcome back, :name!',
        'description' => 'You are now logged in.',
        'logout'      => 'Sign out',
    ],

    'login' => [
        'title'             => 'Sign in to your account',
        'subtitle'          => 'Enter your credentials to continue',
        'email_label'       => 'Email',
        'email_placeholder' => 'your@email.com',
        'password_label'    => 'Password',
        'forgot_password'   => 'Forgot your password?',
        'remember_me'       => 'Remember me',
        'submit'            => 'Sign in',
        'no_account'        => "Don't have an account?",
        'register_link'     => 'Sign up',
    ],

    'register' => [
        'page_title'                   => 'Sign up',
        'heading'                      => 'Create account',
        'subtitle'                     => 'Fill in the details below to create your account',
        'name_label'                   => 'Name',
        'name_placeholder'             => 'Your full name',
        'email_label'                  => 'Email',
        'email_placeholder'            => 'your@email.com',
        'password_label'               => 'Password',
        'password_placeholder'         => 'Minimum 8 characters',
        'confirm_password_label'       => 'Confirm password',
        'confirm_password_placeholder' => 'Repeat your password',
        'submit'                       => 'Register',
        'have_account'                 => 'Already have an account?',
        'login_link'                   => 'Sign in',
    ],

    'forgot_password' => [
        'page_title'        => 'Recover password',
        'heading'           => 'Recover password',
        'subtitle'          => 'Enter your email to receive the password reset link',
        'email_label'       => 'Email',
        'email_placeholder' => 'your@email.com',
        'submit'            => 'Send reset link',
        'remembered'        => 'Remembered your password?',
        'back_to_login'     => 'Back to login',
    ],

    'reset_password' => [
        'page_title'                   => 'Reset password',
        'heading'                      => 'Reset password',
        'subtitle'                     => 'Choose a new password for your account',
        'email_label'                  => 'Email',
        'email_placeholder'            => 'your@email.com',
        'new_password_label'           => 'New password',
        'new_password_placeholder'     => 'Minimum 8 characters',
        'confirm_password_label'       => 'Confirm new password',
        'confirm_password_placeholder' => 'Repeat your new password',
        'submit'                       => 'Reset password',
    ],

    'errors' => [
        '404_title'   => 'Page not found',
        '404_message' => 'Page not found.',
        '500_title'   => 'Internal server error',
        '500_message' => 'An internal error occurred. Please try again later.',
        'back_home'   => 'Back to home',
    ],

];
