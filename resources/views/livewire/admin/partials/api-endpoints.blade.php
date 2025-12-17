<div class="space-y-6">
    @foreach($endpoints as $endpoint)
        <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <span class="px-2 py-1 text-xs font-bold rounded
                        @if($endpoint['method'] === 'GET') bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300
                        @elseif($endpoint['method'] === 'POST') bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300
                        @elseif($endpoint['method'] === 'PUT' || $endpoint['method'] === 'PATCH') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300
                        @elseif($endpoint['method'] === 'DELETE') bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300
                        @endif">
                        {{ $endpoint['method'] }}
                    </span>
                    <code class="text-sm font-mono text-gray-800 dark:text-gray-200">{{ $endpoint['endpoint'] }}</code>
                </div>
            </div>
            
            <div class="p-4 space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $endpoint['description'] }}</p>
                
                @if(isset($endpoint['params']))
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Query Parameters') }}</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-700">
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Parameter') }}</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Description') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($endpoint['params'] as $param => $desc)
                                        <tr>
                                            <td class="px-3 py-2 font-mono text-blue-600 dark:text-blue-400">{{ $param }}</td>
                                            <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $desc }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                @if(isset($endpoint['headers']))
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Headers') }}</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-700">
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Header') }}</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Description') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($endpoint['headers'] as $header => $desc)
                                        <tr>
                                            <td class="px-3 py-2 font-mono text-purple-600 dark:text-purple-400">{{ $header }}</td>
                                            <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $desc }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                @if(isset($endpoint['body']))
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Request Body') }}</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-700">
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Field') }}</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Validation') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($endpoint['body'] as $field => $validation)
                                        <tr>
                                            <td class="px-3 py-2 font-mono text-green-600 dark:text-green-400">{{ $field }}</td>
                                            <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $validation }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                @if(isset($endpoint['topics']))
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Supported Topics') }}</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($endpoint['topics'] as $topic)
                                <span class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md font-mono">
                                    {{ $topic }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(isset($endpoint['response']))
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Response') }}</h4>
                        <pre class="bg-gray-100 dark:bg-gray-700 p-3 rounded-md text-xs overflow-x-auto">{{ $endpoint['response'] }}</pre>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>
