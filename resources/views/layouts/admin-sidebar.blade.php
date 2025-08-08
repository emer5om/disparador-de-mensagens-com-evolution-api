{{--
/**
 * Disparador WhatsApp - Sistema de Disparo de Mensagens
 * 
 * @package DisparadorWhatsApp
 * @author Emerson <https://github.com/emer5om>
 * @version 1.0.0
 * @license MIT
 * @link https://github.com/emer5om/disparador
 */
--}}
<!-- Desktop Sidebar -->
<div class="hidden lg:fixed lg:inset-y-0 lg:z-10 lg:flex lg:w-64 lg:flex-col">
    <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white dark:bg-gray-800 px-6 pb-4 shadow-lg">
        <div class="flex h-16 shrink-0 items-center">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                📱 Disparador
            </h1>
        </div>
        <nav class="flex flex-1 flex-col">
            <ul role="list" class="flex flex-1 flex-col gap-y-7">
                <li>
                    <ul role="list" class="-mx-2 space-y-1">
                        <!-- Dashboard -->
                        <li>
                            <a href="{{ route('admin.dashboard') }}" 
                               class="@if(request()->routeIs('admin.dashboard')) bg-gray-50 text-indigo-600 dark:bg-gray-700 dark:text-indigo-400 @else text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white @endif group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                <svg class="@if(request()->routeIs('admin.dashboard')) text-indigo-600 dark:text-indigo-400 @else text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-white @endif h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                </svg>
                                Dashboard
                            </a>
                        </li>

                        <!-- Leads -->
                        <li>
                            <a href="{{ route('admin.leads.index') }}" 
                               class="@if(request()->routeIs('admin.leads.*')) bg-gray-50 text-indigo-600 dark:bg-gray-700 dark:text-indigo-400 @else text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white @endif group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                <svg class="@if(request()->routeIs('admin.leads.*')) text-indigo-600 dark:text-indigo-400 @else text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-white @endif h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                </svg>
                                Leads
                            </a>
                        </li>

                        <!-- Campanhas -->
                        <li>
                            <a href="{{ route('admin.campaigns.index') }}" 
                               class="@if(request()->routeIs('admin.campaigns.*')) bg-gray-50 text-indigo-600 dark:bg-gray-700 dark:text-indigo-400 @else text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white @endif group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                <svg class="@if(request()->routeIs('admin.campaigns.*')) text-indigo-600 dark:text-indigo-400 @else text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-white @endif h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25m0-9L3 16.5m0-9v9l9 5.25m0-9v9" />
                                </svg>
                                Campanhas
                            </a>
                        </li>

                        <!-- Instâncias -->
                        <li>
                            <a href="{{ route('admin.instances.index') }}" 
                               class="@if(request()->routeIs('admin.instances.*')) bg-gray-50 text-indigo-600 dark:bg-gray-700 dark:text-indigo-400 @else text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white @endif group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                <svg class="@if(request()->routeIs('admin.instances.*')) text-indigo-600 dark:text-indigo-400 @else text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-white @endif h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                </svg>
                                Instâncias
                            </a>
                        </li>

                        <!-- Relatórios -->
                        <li>
                            <a href="{{ route('admin.reports.index') }}" 
                               class="@if(request()->routeIs('admin.reports.*')) bg-gray-50 text-indigo-600 dark:bg-gray-700 dark:text-indigo-400 @else text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white @endif group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                <svg class="@if(request()->routeIs('admin.reports.*')) text-indigo-600 dark:text-indigo-400 @else text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-white @endif h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                                </svg>
                                Relatórios
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Settings Section -->
                <li class="mt-auto">
                    <ul role="list" class="-mx-2 mt-2 space-y-1">
                        <li>
                            <a href="{{ route('profile.edit') }}" class="text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                <svg class="text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-white h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Perfil
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</div>

<!-- Mobile Sidebar -->
<div x-show="sidebarOpen" 
     x-transition:enter="transition ease-in-out duration-300 transform"
     x-transition:enter-start="-translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-in-out duration-300 transform"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full"
     class="relative z-30 flex w-64 flex-col bg-white dark:bg-gray-800 shadow-xl lg:hidden">
    
    <div class="absolute top-0 right-0 -mr-12 pt-2">
        <button type="button" 
                @click="sidebarOpen = false"
                class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
            <span class="sr-only">Fechar sidebar</span>
            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div class="flex grow flex-col gap-y-5 overflow-y-auto px-6 pb-4">
        <div class="flex h-16 shrink-0 items-center">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                📱 Disparador
            </h1>
        </div>
        <nav class="flex flex-1 flex-col">
            <!-- Same navigation items as desktop sidebar -->
            <ul role="list" class="flex flex-1 flex-col gap-y-7">
                <li>
                    <ul role="list" class="-mx-2 space-y-1">
                        <!-- Dashboard -->
                        <li>
                            <a href="{{ route('admin.dashboard') }}" 
                               @click="sidebarOpen = false"
                               class="@if(request()->routeIs('admin.dashboard')) bg-gray-50 text-indigo-600 dark:bg-gray-700 dark:text-indigo-400 @else text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white @endif group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                <svg class="@if(request()->routeIs('admin.dashboard')) text-indigo-600 dark:text-indigo-400 @else text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-white @endif h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                </svg>
                                Dashboard
                            </a>
                        </li>

                        <!-- Leads -->
                        <li>
                            <a href="{{ route('admin.leads.index') }}" 
                               @click="sidebarOpen = false"
                               class="@if(request()->routeIs('admin.leads.*')) bg-gray-50 text-indigo-600 dark:bg-gray-700 dark:text-indigo-400 @else text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white @endif group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                <svg class="@if(request()->routeIs('admin.leads.*')) text-indigo-600 dark:text-indigo-400 @else text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-white @endif h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                </svg>
                                Leads
                            </a>
                        </li>

                        <!-- Campanhas -->
                        <li>
                            <a href="{{ route('admin.campaigns.index') }}" 
                               @click="sidebarOpen = false"
                               class="@if(request()->routeIs('admin.campaigns.*')) bg-gray-50 text-indigo-600 dark:bg-gray-700 dark:text-indigo-400 @else text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white @endif group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                <svg class="@if(request()->routeIs('admin.campaigns.*')) text-indigo-600 dark:text-indigo-400 @else text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-white @endif h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25m0-9L3 16.5m0-9v9l9 5.25m0-9v9" />
                                </svg>
                                Campanhas
                            </a>
                        </li>

                        <!-- Instâncias -->
                        <li>
                            <a href="{{ route('admin.instances.index') }}" 
                               @click="sidebarOpen = false"
                               class="@if(request()->routeIs('admin.instances.*')) bg-gray-50 text-indigo-600 dark:bg-gray-700 dark:text-indigo-400 @else text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white @endif group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                <svg class="@if(request()->routeIs('admin.instances.*')) text-indigo-600 dark:text-indigo-400 @else text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-white @endif h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                </svg>
                                Instâncias
                            </a>
                        </li>

                        <!-- Relatórios -->
                        <li>
                            <a href="{{ route('admin.reports.index') }}" 
                               @click="sidebarOpen = false"
                               class="@if(request()->routeIs('admin.reports.*')) bg-gray-50 text-indigo-600 dark:bg-gray-700 dark:text-indigo-400 @else text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white @endif group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                <svg class="@if(request()->routeIs('admin.reports.*')) text-indigo-600 dark:text-indigo-400 @else text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-white @endif h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                                </svg>
                                Relatórios
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</div>