@foreach ($children as $child)
    @if($child instanceof Reports\Directory)
    <tr class="treegrid-{{ $child->id }} treegrid-collapsed" style="background-color: #f9f9f9;">
        <td>{{ $child->name }} <span class="badge">{{ $child->count }}</span></td>
        <td>{{ $child->description }}</td>
        <td colspan="2"></td>

    @else

    <tr class="treegrid-{{ str_random(6) }}{{ isset($parent) ? ' treegrid-parent-' . $parent : ''}}">
        @if(count($child->header_errors) > 0)
            <td colspan="4">
                <span class="glyphicon glyphicon-file" aria-hidden="true"></span>
                <a href="{{ route('reports.report', ['format' => 'html', 'report' => $child->report_path]) }}">
                    {{ $child->name or $child->report_path }}
                </a> has {{ count($child->header_errors) }} errors.
            </td>
        @else
            <td><span class="glyphicon glyphicon-file" aria-hidden="true"></span><a href="{{ route('reports.report', ['format' => 'html', 'report' => $child->report_path]) }}">{{ $child->name }}</a></td>
            <td>{{ $child->description }}</td>
            <td>{{ $child->connection }}</td>
            <td>{{ $child->lastModified->toFormattedDateString() }}</td>
        @endif
    @endif
    </tr>
    @if($child instanceof Reports\Directory)
        @include('reports::html.tree', ['children' => $child->children, 'parent' => $child->id])
    @endif
@endforeach
