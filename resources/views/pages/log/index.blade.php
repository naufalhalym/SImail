@extends('layout.main')

@section('content')
    <div class="card mb-5">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('model.log.log_name') }}</th>
                    <th>{{ __('model.log.description') }}</th>
                    <th>{{ __('model.log.created_at') }}</th>
                </tr>
                </thead>
                @if($data)
                    <tbody>
                    @foreach($data as $ActivityLog)
                        <tr>
                            <td>{{ $ActivityLog->log_name }}</td>
                            <td>{{ $ActivityLog->description }}</td>
                            <td>{{ $ActivityLog->created_at }}</td>

                        </tr>
                    @endforeach
                    </tbody>
                @else
                    <tbody>
                    <tr>
                        <td colspan="4" class="text-center">
                            {{ __('menu.general.empty') }}
                        </td>
                    </tr>
                    </tbody>
                @endif
                <tfoot class="table-border-bottom-0">
                <tr>
                    <th>{{ __('model.log.log_name') }}</th>
                    <th>{{ __('model.log.description') }}</th>
                    <th>{{ __('model.log.created_at') }}</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

