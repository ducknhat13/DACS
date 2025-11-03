<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Notification' }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #176BF0 0%, #0d4fa3 100%); padding: 30px; border-radius: 12px 12px 0 0; text-align: center;">
        <h1 style="color: white; margin: 0; font-size: 24px;">{{ config('app.name', 'QuickPoll') }}</h1>
    </div>
    
    <div style="background: #ffffff; padding: 30px; border: 1px solid #e0e0e0; border-top: none; border-radius: 0 0 12px 12px;">
        @if(isset($greeting))
        <p style="font-size: 16px; font-weight: bold; color: #333; margin-bottom: 20px;">{{ $greeting }}</p>
        @endif
        
        @if(isset($introLines) && is_array($introLines))
        @foreach($introLines as $line)
        <p style="color: #333; margin-bottom: 15px;">{{ $line }}</p>
        @endforeach
        @endif
        
        @if(isset($actionText) && isset($actionUrl))
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $actionUrl }}" style="display: inline-block; padding: 12px 24px; background: #176BF0; color: white; text-decoration: none; border-radius: 8px; font-weight: bold;">{{ $actionText }}</a>
        </div>
        @endif
        
        @if(isset($outroLines) && is_array($outroLines))
        @foreach($outroLines as $line)
        <p style="color: #666; margin-top: 15px;">{{ $line }}</p>
        @endforeach
        @endif
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0; text-align: center; color: #666; font-size: 14px;">
            <p>{{ __('messages.email_sent_from') }} <a href="{{ url('/') }}" style="color: #176BF0; text-decoration: none;">{{ config('app.name', 'QuickPoll') }}</a></p>
        </div>
    </div>
</body>
</html>

