<x-form-errors />

<x-form-input
    name="name"
    :label="__('shared/ui.name_label')"
    :value="$user->name ?? ''"
    :placeholder="__('shared/ui.full_name_placeholder')"
    :required="true"
    autocomplete="name"
    autofocus
    maxlength="50"
/>

<x-form-input
    name="email"
    type="email"
    :label="__('shared/ui.email_label')"
    :value="$user->email ?? ''"
    :placeholder="__('admin/users.form_email_placeholder')"
    :required="true"
    autocomplete="email"
/>

<x-form-input
    name="password"
    type="password"
    :label="__('shared/ui.password_label')"
    :placeholder="__('shared/ui.password_placeholder')"
    :required="true"
    autocomplete="new-password"
/>

<x-form-input
    name="password_confirmation"
    type="password"
    :label="__('shared/ui.confirm_password_label')"
    :placeholder="__('shared/ui.confirm_password_placeholder')"
    :required="true"
    autocomplete="new-password"
/>
