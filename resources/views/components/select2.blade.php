<div>
    <select
        id="{{ $name }}"
        name="{{ $name . ($multiple ? '[]' : '') }}"
        class="form-control select2-{{ str_replace(['[', ']'], '', $name) }}"
        @if ($multiple) multiple="multiple" @endif
        style="width: 100%;"
    >
        @foreach ($selectedOptions as $option)
            <option value="{{ $option['id'] }}" selected>
                {{ $option['text'] }}
            </option>
        @endforeach
    </select>

    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.select2-{{ $name }}').select2({
                placeholder: "{{ $placeholder }}",
                allowClear: true,
                theme: 'bootstrap4',
                language: 'pt-BR',
                ajax: {
                    url: "{{ route('select2.list', ['model' => $model]) }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term || '',
                            page: params.page
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;

                        if (data.data && Array.isArray(data.data)) {
                            return {
                                results: data.data.map(function (item) {

                                    return {
                                        id: item.id,
                                        text: item.name // Usa 'name' do objeto retornado
                                    };
                                }),
                                pagination: {
                                    more: data.meta.current_page < data.meta.last_page
                                }
                            };
                        }

                        return { results: [] };
                    },
                    cache: true
                },
            });
        });
    </script>
@endpush
