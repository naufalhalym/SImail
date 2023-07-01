@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('menu.transaction.menu'), __('menu.transaction.outgoing_letter'), __('menu.general.view')]">
        <a href="{{ route('transaction.outgoing.index') }}" class="btn btn-primary">{{ __('menu.general.back') }}</a>
    </x-breadcrumb>

    <div class="card mb-4">
        <div class="card-header pb-0">
            <div class="d-flex justify-content-between flex-column flex-sm-row">
                <div class="card-title">
                    <h5 class="text-nowrap mb-0 fw-bold">{{ $data->reference_number }}</h5>
                    <small class="text-black">
                        {{ $data->type == 'incoming' ? $data->from : $data->to }} |
                        <span
                            class="text-secondary">{{ __('model.letter.division') }}:</span> {{ $divisions->where('id', $data->division)->pluck('division')->implode('[]', '"') }}
                        |
                        {{ $data->classification?->type }}
                    </small>
                </div>
                <div class="card-title d-flex flex-row">
                    <div class="d-inline-block mx-2 text-end text-black">
                        <small class="d-block text-secondary">{{ __('model.letter.letter_date') }}</small>
                        {{ $data->formatted_letter_date }}
                    </div>
                    @if($data->type == 'incoming')
                        <div class="mx-3">
                            <a href="{{ route('transaction.disposition.index', $data) }}"
                               class="btn btn-primary btn">{{ __('model.letter.dispose') }} <span>({{ $data->dispositions->count() }})</span></a>
                        </div>
                    @endif
                    <div class="dropdown d-inline-block">
                        <button class="btn p-0" type="button" id="dropdown-{{ $data->type }}-{{ $data->id }}"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        @if($data->type == 'incoming')
                            <div class="dropdown-menu dropdown-menu-end"
                                 aria-labelledby="dropdown-{{ $data->type }}-{{ $data->id }}">
                                @if(!\Illuminate\Support\Facades\Route::is('*.show'))
                                    <a class="dropdown-item"
                                       href="{{ route('transaction.incoming.show', $data) }}">{{ __('menu.general.view') }}</a>
                                @endif
                                <a class="dropdown-item"
                                   href="{{ route('transaction.incoming.edit', $data) }}">{{ __('menu.general.edit') }}</a>
                                <form action="{{ route('transaction.incoming.destroy', $data) }}" class="d-inline"
                                      method="post">
                                    @csrf
                                    @method('DELETE')
                                    <span
                                        class="dropdown-item cursor-pointer btn-delete">{{ __('menu.general.delete') }}</span>
                                </form>
                            </div>
                        @else
                            <div class="dropdown-menu dropdown-menu-end"
                                 aria-labelledby="dropdown-{{ $data->type }}-{{ $data->id }}">
                                @if(!\Illuminate\Support\Facades\Route::is('*.show'))
                                    <a class="dropdown-item"
                                       href="{{ route('transaction.outgoing.show', $data) }}">{{ __('menu.general.view') }}</a>
                                @endif
                                <a class="dropdown-item"
                                   href="{{ route('transaction.outgoing.edit', $data) }}">{{ __('menu.general.edit') }}</a>
                                <form action="{{ route('transaction.outgoing.destroy', $data) }}" class="d-inline"
                                      method="post">
                                    @csrf
                                    @method('DELETE')
                                    <span
                                        class="dropdown-item cursor-pointer btn-delete">{{ __('menu.general.delete') }}</span>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <hr>
            <p>{{ $data->description }}</p>
            <div class="d-flex justify-content-between flex-column flex-sm-row">
                <small class="text-secondary">{{ $data->note }}</small>
                @if(count($data->attachments))
                    <div>
                        @foreach($data->attachments as $attachment)
                            <a href="{{ $attachment->path_url }}" target="_blank">
                                @if($attachment->extension == 'pdf')
                                    <i class="bx bxs-file-pdf display-6 cursor-pointer text-primary"></i>
                                @elseif(in_array($attachment->extension, ['jpg', 'jpeg']))
                                    <i class="bx bxs-file-jpg display-6 cursor-pointer text-primary"></i>
                                @elseif($attachment->extension == 'png')
                                    <i class="bx bxs-file-png display-6 cursor-pointer text-primary"></i>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="mt-2">
                <div class="divider">
                    <div class="divider-text">{{ __('menu.general.view') }}</div>
                </div>
                <dl class="row mt-3">

                    <dt class="col-sm-3">{{ __('model.letter.letter_date') }}</dt>
                    <dd class="col-sm-9">{{ $data->formatted_letter_date }}</dd>

                    <dt class="col-sm-3">{{ __('model.letter.received_date') }}</dt>
                    <dd class="col-sm-9">{{ $data->formatted_received_date }}</dd>

                    <dt class="col-sm-3">{{ __('model.letter.reference_number') }}</dt>
                    <dd class="col-sm-9">{{ $data->reference_number }}</dd>

                    <dt class="col-sm-3">{{ __('model.letter.division') }}</dt>
                    <dd class="col-sm-9">{{ $divisions->where('id', $data->division)->pluck('division')->implode('[]', '"') }}</dd>

                    <dt class="col-sm-3">{{ __('model.classification.code') }}</dt>
                    <dd class="col-sm-9">{{ $data->classification_code }}</dd>

                    <dt class="col-sm-3">{{ __('model.classification.type') }}</dt>
                    <dd class="col-sm-9">{{ $data->classification?->type }}</dd>

                    <dt class="col-sm-3">{{ __('model.letter.to') }}</dt>
                    <dd class="col-sm-9">{{ $data->to }}</dd>

                    <dt class="col-sm-3">{{ __('model.general.created_by') }}</dt>
                    <dd class="col-sm-9">{{ $data->user?->name }}</dd>

                    <dt class="col-sm-3">{{ __('model.general.created_at') }}</dt>
                    <dd class="col-sm-9">{{ $data->formatted_created_at }}</dd>

                    <dt class="col-sm-3">{{ __('model.general.updated_at') }}</dt>
                    <dd class="col-sm-9">{{ $data->formatted_updated_at }}</dd>
                </dl>
            </div>
        </div>
    </div>

@endsection
