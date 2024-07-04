<x-filament-panels::page>
    <form wire:submit.prevent="find">
        {{ $this->form }}

        <div class="text-right">
            <x-filament::button type="submit" class="mt-2">
                Find
            </x-filament::button>
            @if ($saveBtn)
                <x-filament::button wire:click="save" class="mt-2" color="info">
                    Save
                </x-filament::button>
            @endif
        </div>
    </form>

    <!-- Kartu Informasi -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-filament::card class="col-span-1">
            <h2 class="text-lg font-semibold">C - Lorem Ipsum</h2>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing.</p>
        </x-filament::card>
        <x-filament::card class="col-span-1">
            <h2 class="text-lg font-semibold">ME - Lorem, ipsum.</h2>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing.</p>
        </x-filament::card>
        <x-filament::card class="col-span-1">
            <h2 class="text-lg font-semibold">I - Lorem, ipsum.</h2>
            <p>Lorem ipsum dolor, sit amet consectetur adipisicing..</p>
        </x-filament::card>
        <x-filament::card class="col-span-1">
            <h2 class="text-lg font-semibold">NE - Lorem, ipsum.</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing.</p>
        </x-filament::card>
    </div>

    @if ($saveBtn)
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
                            class="w-auto px-6 py-3 text-left text-sm font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                            AREA OF LEARNING & DEVELOPMENT
                        </th>
                        <th scope="col"
                            class="w-auto px-6 py-3 text-left text-sm font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                            ACHIEVEMENT
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($dataTkElements ?? [] as $element)
                        <tr class="bg-gray-400 text-center font-bold">
                            <td colspan="2" class="py-4">{{ $element->name }}</td>
                        </tr>
                        @foreach ($dataTkTopics->where('tk_element_id', $element->id) ?? [] as $topic)
                            <tr class="bg-gray-300 uppercase font-bold">
                                <td colspan="2" class="px-4 py-4">{{ $topic->name }}</td>
                            </tr>
                            @foreach ($dataTkSubtopics->where('tk_topic_id', $topic->id) ?? [] as $subtopic)
                                <tr class="bg-gray-200 font-bold">
                                    <td colspan="2" class="px-4 py-4"><i>{{ $subtopic->name }}</i></td>
                                </tr>
                                @foreach ($dataTkPoints->where('tk_subtopic_id', $subtopic->id) ?? [] as $point)
                                    <tr>
                                        <td class="px-6 py-4">{{ $point->name }}</td>
                                        <td class="px-6 py-4 border border-gray-200 dark:border-gray-700 text-center">
                                            <input type="hidden" name="tk_point_id[]" value="{{ $point->id }}">
                                            <div class="flex justify-center">
                                                <x-filament::input.select wire:model="achivement.{{ $point->id }}"
                                                    class="w-40">
                                                    <option value="">-- Select --</option>
                                                    <option value="C"
                                                        {{ ($achivement[$point->id] ?? '') == 'C' ? 'selected' : '' }}>C
                                                    </option>
                                                    <option value="ME"
                                                        {{ ($achivement[$point->id] ?? '') == 'ME' ? 'selected' : '' }}>
                                                        ME</option>
                                                    <option value="I"
                                                        {{ ($achivement[$point->id] ?? '') == 'I' ? 'selected' : '' }}>I
                                                    </option>
                                                    <option value="NI"
                                                        {{ ($achivement[$point->id] ?? '') == 'NI' ? 'selected' : '' }}>
                                                        NI</option>
                                                </x-filament::input.select>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @endforeach
                    @endforeach

                    <!-- EVENTS -->
                    <tr class="bg-gray-400 text-center font-bold">
                        <td colspan="2" class="py-4">EVENTS</td>
                    </tr>
                    @foreach ($dataEvents ?? [] as $event)
                        <tr>
                            <td class="px-6 py-4">{{ $event->name }}</td>
                            <td class="px-6 py-4 border border-gray-200 dark:border-gray-700">
                                <input type="hidden" name="tk_event_id[]" value="{{ $event->id }}">
                                <x-filament::input.select wire:model="eventAchievements.{{ $event->id }}"
                                    class="w-full">
                                    <option value="">-- Select --</option>
                                    <option value="C"
                                        {{ ($eventAchievements[$event->id] ?? '') == 'C' ? 'selected' : '' }}>C
                                    </option>
                                    <option value="ME"
                                        {{ ($eventAchievements[$event->id] ?? '') == 'ME' ? 'selected' : '' }}>ME
                                    </option>
                                    <option value="I"
                                        {{ ($eventAchievements[$event->id] ?? '') == 'I' ? 'selected' : '' }}>I
                                    </option>
                                    <option value="NI"
                                        {{ ($eventAchievements[$event->id] ?? '') == 'NI' ? 'selected' : '' }}>NI
                                    </option>
                                </x-filament::input.select>
                            </td>
                        </tr>
                    @endforeach

                    <!-- ATTENDANCE -->
                    <tr class="bg-gray-400 text-center font-bold dark:bg-gray-700">
                        <td colspan="2" class="py-4">ATTENDANCE</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 dark:text-gray-300">No. of School Days</td>
                        <td class="px-6 py-4 text-center">
                            <input type="number"
                                class="form-input dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 rounded-md mx-auto"
                                wire:model="attendance.no_school_days" name="no_school_days">
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 dark:text-gray-300">Days Attended</td>
                        <td class="px-6 py-4 text-center">
                            <input type="number"
                                class="form-input dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 rounded-md mx-auto"
                                wire:model="attendance.days_attended" name="days_attended">
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 dark:text-gray-300">Days Absent</td>
                        <td class="px-6 py-4 text-center">
                            <input type="number"
                                class="form-input dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 rounded-md mx-auto"
                                wire:model="attendance.days_absent" name="days_absent">
                        </td>
                    </tr>

                    <!-- CATATAN WALIKELAS -->
                    <tr class="bg-gray-400 text-center font-bold dark:bg-gray-700">
                        <td colspan="2" class="py-4">CATATAN WALIKELAS</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4" colspan="2" class="text-center">
                            <textarea class="form-textarea w-full dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 rounded-md mx-auto"
                                wire:model="homeroomNotes" name="catatan_wali_kelas" rows="3" minlength="30" maxlength="200"
                                oninvalid="this.setCustomValidity('Catatan harus berisi antara 20 s/d 100 karekter')"
                                oninput="setCustomValidity('')">{{ $homeroomNotes }}</textarea>
                        </td>
                    </tr>


                </tbody>
            </table>
        </x-filament-tables::container>
    @endif
</x-filament-panels::page>
