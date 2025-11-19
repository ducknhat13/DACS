{{--
    Email: contact
    - Mẫu email nhận từ form liên hệ: name, email, subject, message.
--}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.contact_form_email') }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #176BF0 0%, #0d4fa3 100%); padding: 30px; border-radius: 12px 12px 0 0; text-align: center;">
        <h1 style="color: white; margin: 0; font-size: 24px;">{{ __('messages.new_contact_message') }}</h1>
    </div>
    
    <div style="background: #ffffff; padding: 30px; border: 1px solid #e0e0e0; border-top: none; border-radius: 0 0 12px 12px;">
        <div style="margin-bottom: 25px;">
            <strong style="color: #176BF0; display: inline-block; min-width: 120px;">{{ __('messages.name') }}:</strong>
            <span>{{ $name }}</span>
        </div>
        
        <div style="margin-bottom: 25px;">
            <strong style="color: #176BF0; display: inline-block; min-width: 120px;">{{ __('messages.email') }}:</strong>
            <a href="mailto:{{ $email }}" style="color: #176BF0; text-decoration: none;">{{ $email }}</a>
        </div>
        
        <div style="margin-bottom: 25px;">
            <strong style="color: #176BF0; display: inline-block; min-width: 120px;">{{ __('messages.subject') }}:</strong>
            <span>{{ $subject }}</span>
        </div>
        
        <div style="margin-bottom: 25px;">
            <strong style="color: #176BF0; display: block; margin-bottom: 10px;">{{ __('messages.message') }}:</strong>
            <div style="background: #f5f5f5; padding: 15px; border-radius: 8px; border-left: 4px solid #176BF0; white-space: pre-wrap;">{{ $messageText }}</div>
        </div>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0; text-align: center; color: #666; font-size: 14px;">
            <p>{{ __('messages.email_sent_from') }} <a href="{{ url('/') }}" style="color: #176BF0; text-decoration: none;">QuickPoll</a></p>
        </div>
    </div>
</body>
</html>

