<x-filament-panels::page>
    <form wire:submit="find">
        {{ $this->form }}

        <div class="text-right">
            @if ($saveBtn)
                <x-filament::button wire:click="saveData" class="mt-2" color="info" wire:dirty.remove
                    wire:target="data">
                    Print Data Student
                </x-filament::button>
                <x-filament::button wire:click="save" class="mt-2" color="secondary" wire:dirty.remove wire:target="data">
                    Print Report Student
                </x-filament::button>
            @endif
            <x-filament::button type="submit" class="mt-2 ">
                Find
            </x-filament::button>
        </div>
    </form>

    @if ($memberClassSchool)

        <!-- Placeholder for loading -->
        <div class="border shadow rounded-md p-4 w-full mx-auto" wire:loading wire:target="data">
            <div class="animate-pulse flex space-x-4">
                <div class="rounded-full bg-slate-700 h-10 w-10"></div>
                <div class="flex-1 space-y-6 py-1">
                    <div class="h-2 bg-slate-700 rounded"></div>
                    <div class="space-y-3">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="h-2 bg-slate-700 rounded col-span-2"></div>
                            <div class="h-2 bg-slate-700 rounded col-span-1"></div>
                        </div>
                        <div class="h-2 bg-slate-700 rounded"></div>
                    </div>
                </div>
            </div>
        </div>

        <x-filament-tables::container>
            <table class="min-w-full w-full table-fixed divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th scope="col"
                            class="w-auto px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            NIS
                        </th>
                        <th scope="col"
                            class="w-auto px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Name
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($memberClassSchool as $key => $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $item->nis }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $item->fullname }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </x-filament-tables::container>
    @endif



</x-filament-panels::page>
