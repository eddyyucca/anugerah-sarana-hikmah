@props(['headers' => [], 'rows' => [], 'actions' => null, 'class' => null])

<div class="table-responsive">
    <table class="table table-modern {{ $class }}">
        @if(count($headers) > 0)
        <thead>
            <tr>
                @foreach($headers as $header)
                    @if(is_array($header))
                        <th style="width: {{ $header['width'] ?? 'auto' }}">{{ $header['label'] ?? $header['key'] }}</th>
                    @else
                        <th>{{ $header }}</th>
                    @endif
                @endforeach
                @if($actions)
                    <th style="width: 100px;">Actions</th>
                @endif
            </tr>
        </thead>
        @endif

        <tbody>
            @forelse($rows as $row)
                <tr>
                    @if(is_array($headers) && count($headers) > 0)
                        @foreach($headers as $header)
                            <td>
                                @if(is_array($header))
                                    {{ $row[$header['key']] ?? '-' }}
                                @else
                                    {{ $row[$header] ?? '-' }}
                                @endif
                            </td>
                        @endforeach
                    @else
                        <td>{{ $row }}</td>
                    @endif

                    @if($actions)
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            {{ $slot }}
                        </div>
                    </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) + ($actions ? 1 : 0) }}" class="text-center text-muted py-4">
                        No data available
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
