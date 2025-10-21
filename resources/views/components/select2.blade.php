<div>
    <label for="{{ $name }}">{{ $name }}</label>
    <select id="{{ $name }}" name="{{ $name . ($multiple ? '[]' : '') }}"
        class="form-control select2-{{ str_replace(['[', ']'], '', $name) }} {{ $validClass }}"
        @if ($multiple) multiple="multiple" @endif style="width: 100%;"
        data-dependent="{{ $dependent }}" data-value="{{ $dependentValue }}">
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
        $(document).ready(function() {

            let $selectElement = $('.select2-{{ str_replace(['[', ']'], '', $name) }}');
            let dependentOn = $selectElement.data('dependent');
            let currentCascadeValue = $selectElement.data('value');
            let baseUrl = "{{ route('select2.list', ['model' => $model]) }}";

            function initializeSelect2(cascadeValue) {
                currentCascadeValue = cascadeValue;

                if ($selectElement.data('select2')) {
                    $selectElement.select2('destroy');
                }

                $selectElement.select2({
                    placeholder: "{{ $placeholder }}",
                    allowClear: true,
                    theme: 'bootstrap4',
                    language: 'pt-BR',
                    ajax: {
                        url: function() {
                            return currentCascadeValue ?
                                baseUrl + '/' + currentCascadeValue :
                                baseUrl;
                        },
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                search: params.term || '',
                                page: params.page
                            };
                        },
                        processResults: function(data, params) {
                            params.page = params.page || 1;

                            if (data.data && Array.isArray(data.data)) {
                                return {
                                    results: data.data.map(function(item) {
                                        return {
                                            id: item.id,
                                            text: item.name
                                        };
                                    }),
                                    pagination: {
                                        more: data.meta.current_page < data.meta.last_page
                                    }
                                };
                            }

                            return {
                                results: []
                            };
                        },
                        cache: true
                    }
                });
            }

            if (dependentOn) {
                let $dependentElement = $('#' + dependentOn);

                $dependentElement.on('select2:select', function(e) {
                    let newCascadeValue = e.params.data.id;
                    $selectElement.empty().trigger('change');
                    initializeSelect2(newCascadeValue);
                });

                $dependentElement.on('select2:unselect', function() {
                    $selectElement.empty().trigger('change');
                    initializeSelect2(null);
                });
            }

            @if (!empty($selectedOptions))
                @foreach ($selectedOptions as $option)
                    if (!$selectElement.find("option[value='{{ $option['id'] }}']").length) {
                        let newOption = new Option("{{ $option['text'] }}", "{{ $option['id'] }}", true, true);
                        $selectElement.append(newOption).trigger('change');
                    }
                @endforeach
            @endif

            initializeSelect2(currentCascadeValue);
        });
    </script>
@endpush
