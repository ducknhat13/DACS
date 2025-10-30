@props(['class' => ''])

<div class="dark-mode-toggle {{ $class }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" 
     x-init="
        if (darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
     ">
    <button 
        @click="
            darkMode = !darkMode;
            if (darkMode) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('darkMode', 'true');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('darkMode', 'false');
            }
        "
        class="btn btn-ghost btn-sm p-2 rounded-full hover:bg-surface-100 transition-all duration-200"
        :title="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
    >
        <i class="fa-solid fa-sun text-warning-500" x-show="!darkMode" x-transition></i>
        <i class="fa-solid fa-moon text-primary-500" x-show="darkMode" x-transition></i>
    </button>
</div>
