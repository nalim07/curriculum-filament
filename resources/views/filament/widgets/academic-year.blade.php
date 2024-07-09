<x-filament-widgets::widget class="fi-account-widget">
    <x-filament::section>
        <div class="flex flex-wrap justify-between items-center gap-x-3 gap-y-3">
            <div class="sm:w-1/2 md:w-1/3 lg:flex-1">
                <h2 class="text-base font-semibold leading-6 text-gray-950 dark:text-white text-center mb-1">
                    School Year
                </h2>
                <div class="flex justify-center">
                    <p
                        class="inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-800 px-2 py-1 text-xs font-medium text-blue-700 dark:text-blue-200 ring-1 ring-inset ring-blue-700/10 dark:ring-blue-200/30">
                        {{ $currentAcademicYear }}
                    </p>
                </div>
            </div>
            <div class="sm:w-1/2 md:w-1/3 lg:flex-1">
                <h2 class="text-base font-semibold leading-6 text-gray-950 dark:text-white text-center mb-1">
                    Term PG/KG
                </h2>
                <div class="flex justify-center">
                    <p
                        class="inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-800 px-2 py-1 text-xs font-medium text-blue-700 dark:text-blue-200 ring-1 ring-inset ring-blue-700/10 dark:ring-blue-200/30">
                        {{ $termPG }}
                    </p>
                </div>
            </div>
            <div class="sm:w-1/2 md:w-1/3 lg:flex-1">
                <h2 class="text-base font-semibold leading-6 text-gray-950 dark:text-white text-center mb-1">
                    Semester PS
                </h2>
                <div class="flex justify-center">
                    <p
                        class="inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-800 px-2 py-1 text-xs font-medium text-blue-700 dark:text-blue-200 ring-1 ring-inset ring-blue-700/10 dark:ring-blue-200/30">
                        {{ $termPS }} / {{ $semesterPS }}
                    </p>
                </div>
            </div>
            <div class="sm:w-1/2 md:w-1/3 lg:flex-1">
                <h2 class="text-base font-semibold leading-6 text-gray-950 dark:text-white text-center mb-1">
                    Semester JHS
                </h2>
                <div class="flex justify-center">
                    <p
                        class="inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-800 px-2 py-1 text-xs font-medium text-blue-700 dark:text-blue-200 ring-1 ring-inset ring-blue-700/10 dark:ring-blue-200/30">
                        {{ $termJHS }} / {{ $semesterJHS }}
                    </p>
                </div>
            </div>
            <div class="sm:w-1/2 md:w-1/3 lg:flex-1">
                <h2 class="text-base font-semibold leading-6 text-gray-950 dark:text-white text-center mb-1">
                    Semester SHS
                </h2>
                <div class="flex justify-center">
                    <p
                        class="inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-800 px-2 py-1 text-xs font-medium text-blue-700 dark:text-blue-200 ring-1 ring-inset ring-blue-700/10 dark:ring-blue-200/30">
                        {{ $termSHS }} / {{ $semesterSHS }}
                    </p>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
