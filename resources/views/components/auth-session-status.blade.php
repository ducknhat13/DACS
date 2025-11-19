{{--
    Component: auth-session-status
    - Hiển thị thông báo trạng thái phiên đăng nhập (ví dụ: link reset password đã gửi).
--}}
@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600 dark:text-green-400']) }}>
        {{ $status }}
    </div>
@endif
